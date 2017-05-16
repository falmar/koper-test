<?php
// Routes

// Migrate database when running dev mode
$app->get('/migrate', function (\Slim\Http\Request $request, \Slim\Http\Response $response) {
    if (getenv('SLIM_ENV') === 'production') {
        return $response->withStatus(401);
    }

    $product = new \KoperTest\db\Product($this->get('dbh'));

    $product->down();
    $product->up();
    $product->seed();
});

// Products
$app->get('/products/{id}', \KoperTest\Controllers\ProductsController::class . ':get');
$app->post('/products', \KoperTest\Controllers\ProductsController::class . ':add');
