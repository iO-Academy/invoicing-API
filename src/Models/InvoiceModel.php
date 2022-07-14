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
}