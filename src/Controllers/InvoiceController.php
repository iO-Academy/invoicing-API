<?php

namespace Invoices\Controllers;

use Invoices\Abstracts\Controller;
use Invoices\Models\InvoiceModel;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class InvoiceController extends Controller
{
    private InvoiceModel $invoiceModel;

    /**
     * @param InvoiceModel $invoiceModel
     */
    public function __construct(InvoiceModel $invoiceModel)
    {
        $this->invoiceModel = $invoiceModel;
    }

    public function getInvoices(Request $request, Response $response)
    {
        $invoices = $this->invoiceModel->getInvoices();
        return $this->respondWithJson($response, $invoices);
    }
}