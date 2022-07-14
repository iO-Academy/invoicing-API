<?php

namespace Invoices\Models;

use Invoices\Exceptions\InvalidInvoiceIdException;

class InvoiceModel
{
    private \PDO $db;

    /**
     * @param \PDO $db
     */
    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    public function getAllInvoices(string $sort = 'invoice_id'): array
    {
        $query = $this->db->prepare('
            SELECT `invoices`.`id`, `invoice_id`, `name`, `due`, `invoice_total`, `status`, `status_name` 
            FROM `invoices` 
            LEFT JOIN `status` ON `invoices`.`status` = `status`.`id`
            ORDER BY `' . $sort . '` DESC
        ');
        $query->execute();
        return $query->fetchAll();
    }

    public function getInvoicesByStatus(int $status, string $sort = 'invoice_id'): array
    {
        $query = $this->db->prepare('
            SELECT `invoices`.`id`, `invoice_id`, `name`, `due`, `invoice_total`, `status`, `status_name` 
            FROM `invoices` 
            LEFT JOIN `status` ON `invoices`.`status` = `status`.`id`
            WHERE `status` = ?
            ORDER BY `' . $sort . '` DESC
        ');
        $query->execute([$status]);
        return $query->fetchAll();
    }

    public function getInvoiceById(int $id): array
    {
        $query = $this->db->prepare('
            SELECT `invoices`.`id`, `invoice_id`, `name`, `street_address`, `city`, `created`, `due`, `invoice_total`, `paid_to_date`, `status`, `status_name` 
            FROM `invoices` 
            LEFT JOIN `status` ON `invoices`.`status` = `status`.`id`
            WHERE `invoices`.`id` = ?
        ');
        $query->execute([$id]);
        $invoice = $query->fetch();
        if ($invoice) {
            $invoice['details'] = $this->getInvoiceDetails($invoice['id']);
        } else {
            throw new InvalidInvoiceIdException('No invoice found with id: ' . $id);
        }
        return $invoice;
    }

    private function getInvoiceDetails(int $invoice_id): array
    {
        $query = $this->db->prepare('
                SELECT `description`, `quantity`, `rate`, `total` FROM `invoice_details` WHERE `invoices_id` = ?
            ');
        $query->execute([$invoice_id]);
        return $query->fetchAll();
    }

    public function createInvoice(array $invoice)
    {
        $curDate = new \DateTime();
        $dueDate = new \DateTime();
        $dueDate->add(new \DateInterval('P30D'));

        $query = $this->db->prepare('
            INSERT INTO `invoices` 
                (`name`, `street_address`, `city`, `created`, `due`, `invoice_total`) 
            VALUES (
                    :name,
                    :street_address,
                    :city,
                    :created,
                    :due,
                    :total
            );
        ');
        $result = $query->execute([
            'name' => $invoice['name'],
            'street_address' => $invoice['street_address'],
            'city' => $invoice['city'],
            'created' => $curDate->format('Y-m-d'),
            'due' => $dueDate->format('Y-m-d'),
            'total' => $invoice['total'],
        ]);

        if ($result) {
            $id = $this->db->lastInsertId();
            $query = $this->db->prepare('UPDATE `invoices` SET `invoice_id` = :invoice_id WHERE `id` = :id;');
            $result2 = $query->execute([
                'invoice_id' => 'RX1' . $id,
                'id' => $id
            ]);

            $result3 = true;
            try {
                foreach ($invoice['details'] as $detail) {
                    $this->createInvoiceDetail($detail, $id);
                }
            } catch (\Exception $exception) {
                $result3 = false;
            }
        }

        $return = [
            'invoice_id' => 'RX1' . $id,
            'id' => $id
        ];

        return (($result && $result2 && $result3) ? $return : false);
    }

    private function createInvoiceDetail(array $detail, int $id): bool
    {
        $query = $this->db->prepare('
            INSERT INTO `invoice_details` 
                (`invoices_id`, `description`, `quantity`, `rate`, `total`) 
            VALUES (
                    :invoices_id,
                    :description,
                    :quantity,
                    :rate,
                    :total
            );
        ');
        return $query->execute([
            'invoices_id' => $id,
            'description' => $detail['description'] ?? NULL,
            'quantity' => $detail['quantity'],
            'rate' => $detail['rate'],
            'total' => $detail['total']
        ]);
    }
}