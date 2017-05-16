<?php
declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: falmar
 * Date: 5/15/17
 * Time: 5:21 PM
 */

namespace KoperTest\Controllers;


use KoperTest\Models\Products;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;


class ProductsController extends BaseController
{
    /** @var ContainerInterface */
    protected $container = null;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    public function get(Request $request, Response $response, $args)
    {
        /** @var Logger $logger */
        $logger    = $this->container->get('logger');
        $productId = (int)($args['id'] ?? 0);

        $logger->info('/products/' . $productId);

        /** @var Response $response */
        $response = $response->withHeader('Content-Type', 'application/json;charset=utf-8');

        if (!$this->acceptsJSON($request->getHeaderLine('Accept'))) {
            $logger->notice('Request must accept media-type: application/json');

            return $response->withStatus(406)->withJson([
                'status'           => 406,
                'developerMessage' => 'Request must accept media-type: application/json.',
                'userMessage'      => 'Server couldn\'t provide a valid response.',
                'errorCode'        => '',
                'moreInfo'         => ''
            ]);
        }

        try {
            $model   = new Products($this->container);
            $product = $model->get($productId);

            if (!$product) {
                $logger->info('Product does not exist');

                return $response->withStatus(404)->withJson([
                    'status'           => 404,
                    'developerMessage' => "Product ({$productId}) does not exist.",
                    'userMessage'      => 'Product does not exist.',
                    'errorCode'        => '',
                    'moreInfo'         => ''
                ]);
            }

            $product['tags'] = json_decode($product['tags']);

            return $response->withJson($product);
        } catch (\Error $e) {
            $logger->error($e->getMessage());
        } catch (\PDOException $e) {
            $logger->error($e->getMessage());
        }

        return $response->withStatus(500)->withJson([
            'status'           => 500,
            'developerMessage' => 'Internal Server Error.',
            'userMessage'      => 'Unexpected error has occurred, try again later.',
            'errorCode'        => '',
            'moreInfo'         => ''
        ]);
    }
}
