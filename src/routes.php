<?php
// Routes

// Migrate database when running dev mode
if (getenv('SLIM_ENV') === 'development') {
    $app->get('/migrate', function (\Slim\Http\Request $request, \Slim\Http\Response $response) {
        $product = new \KoperTest\Migrations\Product($this->get('dbh'));
        $invoice = new \KoperTest\Migrations\Product($this->get('dbh'));

        $product->down();
        $product->up();
        $product->seed();

        $invoice->down();
        $invoice->up();
        $invoice->seed();

        return $response->withStatus(200);
    });
}

// Products
$app->get('/products', \KoperTest\Controllers\ProductsController::class . ':collection');
$app->get('/products/{id}', \KoperTest\Controllers\ProductsController::class . ':get');
$app->post('/products', \KoperTest\Controllers\ProductsController::class . ':add');
$app->put('/products/{id}', \KoperTest\Controllers\ProductsController::class . ':update');
$app->delete('/products/{id}', \KoperTest\Controllers\ProductsController::class . ':delete');

// Invoice
$app->get('/invoices', \KoperTest\Controllers\InvoicesController::class . ':collection');
$app->get('/invoices/{id}', \KoperTest\Controllers\InvoicesController::class . ':get');
$app->post('/invoices', \KoperTest\Controllers\InvoicesController::class . ':add');
$app->put('/invoices/{id}', \KoperTest\Controllers\InvoicesController::class . ':update');
$app->delete('/invoices/{id}', \KoperTest\Controllers\InvoicesController::class . ':delete');
