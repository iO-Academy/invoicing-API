<?php
declare(strict_types=1);

use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    // enable options requests
    $app->options('/{routes:.+}', function ($request, $response, $args) {
        return $response;
    });

    // enable CORS
    $app->add(function ($request, $handler) {
        $response = $handler->handle($request);
        return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
    });

    $app->get('/', function ($request, $response) {
        $html = '<h1>Invoice system API</h1>
                    <p>To use this API please check the 
                        <a href="https://github.com/iO-Academy/invoicing-API#api-documentation" target="_blank">documentation</a>.
                    </p>';
        $response->getBody()->write($html);
        return $response->withHeader('Content-type', 'text/html')->withStatus(200);
    });

    $app->get('/invoices', '\Invoices\Controllers\InvoiceController:getInvoices');
    $app->get('/invoices/{id}', '\Invoices\Controllers\InvoiceController:getInvoice');
    $app->post('/invoices', '\Invoices\Controllers\InvoiceController:newInvoice');
    $app->put('/invoices/{id}', '\Invoices\Controllers\InvoiceController:payInvoice');
    $app->delete('/invoices/{id}', '\Invoices\Controllers\InvoiceController:cancelInvoice');

    $app->get('/clients', '\Invoices\Controllers\ClientController:getClients');

};
