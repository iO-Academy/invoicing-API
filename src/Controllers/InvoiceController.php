<?php

namespace Invoices\Controllers;

use Invoices\Abstracts\Controller;
use Invoices\Exceptions\InvalidInvoiceIdException;
use Invoices\Models\ClientModel;
use Invoices\Models\InvoiceModel;
use Invoices\Validators\InvoiceValidator;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class InvoiceController extends Controller
{
    private InvoiceModel $invoiceModel;
    private ClientModel $clientModel;
    const SORT_OPTIONS = ['invoice_id', 'invoice_total', 'created', 'due'];

    /**
     * @param InvoiceModel $invoiceModel
     */
    public function __construct(InvoiceModel $invoiceModel, ClientModel $clientModel)
    {
        $this->invoiceModel = $invoiceModel;
        $this->clientModel = $clientModel;
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

    public function newInvoice(Request $request, Response $response)
    {
        $responseData = ['message' => 'Successfully created new invoice.', 'data' => []];

        try {
            $invoiceData = $request->getParsedBody();
            if (InvoiceValidator::validate($invoiceData)) {
                $client = $this->clientModel->getClientById($invoiceData['client']);
                $invoiceData = array_merge($invoiceData, $client);
                $invoiceId = $this->invoiceModel->createInvoice($invoiceData);
                if ($invoiceId === false) {
                    $responseData['message'] = 'Unable to create invoice, check the DB as it may have stored part of the new invoice.';
                    return $this->respondWithJson($response, $responseData, 500);
                }
                $responseData['data'] = $invoiceId;
                return $this->respondWithJson($response, $responseData);
            } else {
                $responseData['message'] = 'Invalid invoice data.';
                return $this->respondWithJson($response, $responseData, 400);
            }
        } catch (\Exception $e) {
            $responseData['message'] = 'Unexpected error.';
            return $this->respondWithJson($response, $responseData, 500);
        }
    }

    public function payInvoice(Request $request, Response $response, array $args)
    {
        $responseData = ['message' => 'Successfully marked invoice as paid.', 'data' => []];

        try {
            if (isset($args['id']) && is_numeric($args['id'])) {
                $result = $this->invoiceModel->markInvoiceAsPaid($args['id']);
                if ($result) {
                    return $this->respondWithJson($response, $responseData);
                } else {
                    $responseData['message'] = 'No invoice found with id: ' . $args['id'];
                    return $this->respondWithJson($response, $responseData, 400);
                }
            } else {
                $responseData['message'] = 'Invalid invoice ID';
                return $this->respondWithJson($response, $responseData, 400);
            }
        } catch (\Exception $e) {
            $responseData['message'] = 'Unexpected error.';
            return $this->respondWithJson($response, $responseData, 500);
        }
    }

    public function cancelInvoice(Request $request, Response $response, array $args)
    {
        $responseData = ['message' => 'Successfully cancelled invoice.', 'data' => []];

        try {
            if (isset($args['id']) && is_numeric($args['id'])) {
                $result = $this->invoiceModel->cancelInvoice($args['id']);
                if ($result) {
                    return $this->respondWithJson($response, $responseData);
                } else {
                    $responseData['message'] = 'No invoice found with id: ' . $args['id'];
                    return $this->respondWithJson($response, $responseData, 400);
                }
            } else {
                $responseData['message'] = 'Invalid invoice ID';
                return $this->respondWithJson($response, $responseData, 400);
            }
        } catch (\Exception $e) {
            $responseData['message'] = 'Unexpected error.';
            return $this->respondWithJson($response, $responseData, 500);
        }
    }
}