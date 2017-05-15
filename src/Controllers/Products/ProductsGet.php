<?php
declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: falmar
 * Date: 5/15/17
 * Time: 5:21 PM
 */

namespace KoperTest\Controllers\Products;


use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;


class ProductsGet
{
    /** @var ContainerInterface */
    protected $container = null;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response)
    {
        /** @var Logger $logger */
        $logger = $this->container->get('logger');
    }
}
