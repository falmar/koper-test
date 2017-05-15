<?php
// Routes

/**
 * Index Handler
 * @var \Slim\Http\Request $request
 * @var \Slim\Http\Response $response
 *
 * @return \Slim\Http\Response
 */
$indexHandler = function ($request, $response) {
    /**  @var \Monolog\Logger $logger Sample log message */
    $logger = $this->logger;

    $logger->info("Slim-Skeleton '/' route");

    return $response->withStatus(404);
};

$app->get('/', $indexHandler);

// Products
$app->get('/products/{id}', \KoperTest\Controllers\ProductsController::class . ':get');
