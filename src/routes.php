<?php
// Routes

// Migrate database when running dev mode
if (getenv('SLIM_ENV') !== 'production') {
    $app->get('/migrate', function (\Slim\Http\Request $request, \Slim\Http\Response $response) {
        $product = new \KoperTest\db\Product($this->get('dbh'));

        $product->down();
        $product->up();
        $product->seed();

        return $response->withStatus(200);
    });
}

// Products
$app->get('/products/{id}', \KoperTest\Controllers\ProductsController::class . ':get');
$app->post('/products', \KoperTest\Controllers\ProductsController::class . ':add');
$app->put('/products/{id}', \KoperTest\Controllers\ProductsController::class . ':update');
$app->delete('/products/{id}', \KoperTest\Controllers\ProductsController::class . ':delete');
