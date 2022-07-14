<?php

namespace Invoices\Controllers;

use Invoices\Abstracts\Controller;
use Invoices\Models\ClientModel;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ClientController extends Controller
{
    private ClientModel $clientModel;

    public function __construct(ClientModel $clientModel)
    {
        $this->clientModel = $clientModel;
    }

    public function getClients(Request $request, Response $response)
    {
        $responseData = ['message' => 'Successfully found clients.', 'data' => []];

        try {
            $responseData['data'] = $this->clientModel->getClients();
            return $this->respondWithJson($response, $responseData);
        } catch (\Exception $e) {
            $responseData['message'] = 'Unexpected error';
            return $this->respondWithJson($response, $responseData, 500);
        }
    }
}