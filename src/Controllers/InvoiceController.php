<?php

namespace Invoices\Controllers;

use Invoices\Abstracts\Controller;
use Invoices\Exceptions\InvalidInvoiceIdException;
use Invoices\Models\InvoiceModel;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class InvoiceController extends Controller
{
    private InvoiceModel $invoiceModel;
    const SORT_OPTIONS = ['invoice_id', 'invoice_total', 'created', 'due'];

    /**
     * @param InvoiceModel $invoiceModel
     */
    public function __construct(InvoiceModel $invoiceModel)
    {
        $this->invoiceModel = $invoiceModel;
    }

    public function getInvoices(Request $request, Response $response)
    {
        $responseData = ['message' => 'Successfully found invoices.', 'data' => []];

        try {
            $sort = $request->getQueryParams()['sort'] ?? 'invoice_id';
            $status = $request->getQueryParams()['status'] ?? false;
            if (in_array($sort, self::SORT_OPTIONS)) {
                if (is_numeric($status)) {
                    $responseData['data'] = $this->invoiceModel->getInvoicesByStatus($status, $sort);
                } else {
                    $responseData['data'] = $this->invoiceModel->getAllInvoices($sort);
                }
            } else {
                $responseData['message'] = 'Invalid sort parameter';
                return $this->respondWithJson($response, $responseData, 400);
            }
            return $this->respondWithJson($response, $responseData);
        } catch (\Exception $e) {
            $responseData['message'] = 'Unexpected error';
            return $this->respondWithJson($response, $responseData, 500);
        }
    }

    public function getInvoice(Request $request, Response $response, array $args)
    {
        $responseData = ['message' => 'Successfully found invoice.', 'data' => []];

        try {
            if (isset($args['id']) && is_numeric($args['id'])) {
                $responseData['data'] = $this->invoiceModel->getInvoiceById($args['id']);
                return $this->respondWithJson($response, $responseData);
            } else {
                $responseData['message'] = 'Invalid invoice ID';
                return $this->respondWithJson($response, $responseData, 400);
            }
        } catch (InvalidInvoiceIdException $e) {
            $responseData['message'] = $e->getMessage();
            return $this->respondWithJson($response, $responseData, 400);
        } catch (\Exception $e) {
            $responseData['message'] = 'Unexpected error';
            return $this->respondWithJson($response, $responseData, 500);
        }
    }
}