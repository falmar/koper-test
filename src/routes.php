<?php
// Routes

// Migrate database when running dev mode
if (getenv('SLIM_ENV') === 'development') {
    $app->get('/migrate', function (\Slim\Http\Request $request, \Slim\Http\Response $response) {
        $product         = new \KoperTest\Migrations\Product($this->get('dbh'));
        $invoice         = new \KoperTest\Migrations\Invoice($this->get('dbh'));
        $invoiceProducts = new \KoperTest\Migrations\InvoiceProduct($this->get('dbh'));

        $invoiceProducts->down();
        $product->down();
        $invoice->down();

        $product->up();
        $product->seed();

        $invoice->up();
        $invoice->seed();

        $invoiceProducts->up();
        $invoiceProducts->seed();

        return $response->withStatus(200);
    });
}

// CORS - apparently not needed, will use same path
$app->options('*', function (\Slim\Http\Request $request, \Slim\Http\Response $response) {
    $origin  = $request->getHeaderLine('Origin');
    $methods = $request->getHeaderLine("Access-Control-Request-Method");

    $this->logger->info('origin', $origin);
    $this->logger->info('methods', $methods);

    $response = $response->withStatus(200)
        ->withHeader("Access-Control-Allow-Origin", $origin)
        ->withAddedHeader("Access-Control-Allow-Headers", "Content-Type, Origin, Authorization")
        ->withAddedHeader("Access-Control-Allow-Methods", $methods);

    return $response;
});

$app->group('/v1', function () use ($app) {
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

    // Invoice
    $app->get('/invoices/{invoiceId}/products',
        \KoperTest\Controllers\InvoiceProductsController::class . ':collection');
    $app->get('/invoices/{invoiceId}/products/{productId}',
        \KoperTest\Controllers\InvoiceProductsController::class . ':get');
    $app->post('/invoices/{invoiceId}/products', \KoperTest\Controllers\InvoiceProductsController::class . ':add');
    $app->put('/invoices/{invoiceId}/products/{productId}',
        \KoperTest\Controllers\InvoiceProductsController::class . ':update');
    $app->delete(
        '/invoices/{invoiceId}/products/{productId}',
        \KoperTest\Controllers\InvoiceProductsController::class . ':delete'
    );
});
