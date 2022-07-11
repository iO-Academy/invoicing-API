<?php

namespace Invoices\Models;

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

    public function getInvoices()
    {
        $query = $this->db->prepare('
            SELECT `invoices`.`id`, `invoice_id`, `name`, `street_address`, `city`, `created`, `due`, `invoice_total`, `paid_to_date`, `status`, `status_name` 
            FROM `invoices` 
            LEFT JOIN `status` ON `invoices`.`status` = `status`.`id`
        ');
        $query->execute();
        $invoices = $query->fetchAll();

        foreach ($invoices as $k => $invoice) {
            $query = $this->db->prepare('
                SELECT `description`, `quantity`, `rate`, `total` FROM `invoice_details` WHERE `invoices_id` = ?
            ');
            $query->execute([$invoice['id']]);
            $invoices[$k]['details'] = $query->fetchAll();
        }
        return $invoices;
    }
}