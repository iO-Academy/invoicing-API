<?php

namespace Invoices\Validators;

class InvoiceValidator
{
    public static function validate(array $invoice): bool
    {
        return (
            (
                !empty($invoice['client']) &&
                is_numeric($invoice['client'])
            ) &&
            (
                !empty($invoice['total']) &&
                is_numeric($invoice['total'])
            ) &&
            (
                !empty($invoice['details']) &&
                is_array($invoice['details']) &&
                count($invoice['details']) > 0 &&
                self::validateInvoiceDetails($invoice['details']) &&
                self::totalInvoiceDetails($invoice['details']) == $invoice['total']
            )
        );
    }

    private static function validateInvoiceDetails(array $invoiceDetails): bool
    {
        $valid = true;
        foreach ($invoiceDetails as $detail) {
            if (
                empty($detail['quantity']) ||
                !is_numeric($detail['quantity']) ||
                empty($detail['rate']) ||
                !is_numeric($detail['rate']) ||
                empty($detail['total']) ||
                !is_numeric($detail['total'])
            ) {
                $valid = false;
            }
        }
        return $valid;
    }

    private static function totalInvoiceDetails(array $invoiceDetails): float
    {
        $total = 0;
        foreach ($invoiceDetails as $detail) {
            $total += $detail['total'];
        }
        return $total;
    }
}