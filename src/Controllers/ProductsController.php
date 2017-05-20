<?php
declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: falmar
 * Date: 5/15/17
 * Time: 5:21 PM
 */

namespace KoperTest\Controllers;


use KoperTest\Models\ProductsModel;
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

        $logger->info('GET /products/' . $productId);

        /** @var Response $response */
        $response = $response->withHeader('Content-Type', 'application/json;charset=utf-8');

        if (!$this->acceptsJSON($request->getHeaderLine('Accept'))) {
            $logger->notice('Request must accept media-type: application/json');

            return $response->withStatus(400)->withJson([
                'status'           => 400,
                'developerMessage' => 'Request must accept media-type: application/json.',
                'userMessage'      => 'Server couldn\'t provide a valid response.',
                'errorCode'        => '',
                'moreInfo'         => ''
            ]);
        }

        try {
            $model   = new ProductsModel($this->container);
            $product = $model->get($productId);

            if (!$product) {
                $logger->info('Product does not exist');

                return $response->withStatus(400)->withJson([
                    'status'           => 400,
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
        } catch (\Exception $e) {
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

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function collection(Request $request, Response $response)
    {
        /** @var Logger $logger */
        $logger = $this->container->get('logger');

        $logger->info('GET /products');

        /** @var Response $response */
        $response = $response->withHeader('Content-Type', 'application/json;charset=utf-8');

        if (!$this->acceptsJSON($request->getHeaderLine('Accept'))) {
            $logger->notice('Request must accept media-type: application/json');

            return $response->withStatus(400)->withJson([
                'status'           => 400,
                'developerMessage' => 'Request must accept media-type: application/json.',
                'userMessage'      => 'Server couldn\'t provide a valid response.',
                'errorCode'        => '',
                'moreInfo'         => ''
            ]);
        }

        try {
            $limit     = (int)$request->getParam('limit', null);
            $offset    = (int)$request->getParam('offset', null);
            $sortField = $request->getParam('sortField', null);
            $sortOrder = $request->getParam('sortOrder', null);

            $model = new ProductsModel($this->container);

            $results = $model->collection([
                'limit'     => $limit ? $limit : 25,
                'offset'    => $offset,
                'sortField' => $sortField,
                'sortOrder' => $sortOrder,
            ]);

            foreach ($results as $k => $result) {
                $result[$k]['tags'] = json_decode($result['tags']);
            }

            return $response->withJson([
                'metadata' => [
                    'resultset' => [
                        'count'  => $model->count(),
                        'limit'  => $limit ? $limit : 25,
                        'offset' => $offset
                    ]
                ],
                'results'  => $results
            ]);
        } catch (\Error $e) {
            $logger->error($e->getMessage());
        } catch (\PDOException $e) {
            $logger->error($e->getMessage());
        } catch (\Exception $e) {
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

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function add(Request $request, Response $response)
    {
        /** @var Logger $logger */
        $logger = $this->container->get('logger');

        $logger->info('POST /products');

        /** @var Response $response */
        $response = $response->withHeader('Content-Type', 'application/json;charset=utf-8');

        if (!$this->acceptsJSON($request->getHeaderLine('Accept'))) {
            $logger->notice('Request must accept media-type: application/json');

            return $response->withStatus(400)->withJson([
                'status'           => 400,
                'developerMessage' => 'Request must accept media-type: application/json.',
                'userMessage'      => 'Bad Request.',
                'errorCode'        => '',
                'moreInfo'         => ''
            ]);
        }

        if (!$this->isJSON($request->getHeaderLine('Content-Type'))) {
            $logger->notice('Request must have Content-Type: application/json.');

            return $response->withStatus(400)->withJson([
                'status'           => 400,
                'developerMessage' => 'Request must have Content-Type: application/json.',
                'userMessage'      => 'Bad Request.',
                'errorCode'        => '',
                'moreInfo'         => ''
            ]);
        }

        $product = $request->getParsedBody();

        if (
            json_last_error() !== JSON_ERROR_NONE ||
            !is_array($product) || (
                !isset($product['name']) || empty($product['name']) ||
                !isset($product['price']) || empty($product['price'])
            )
        ) {
            $logger->notice('Request body or fields cannot be empty.');

            return $response->withStatus(400)->withJson([
                'status'           => 400,
                'developerMessage' => 'Request body or fields cannot be empty.',
                'userMessage'      => 'Bad Request.',
                'errorCode'        => '',
                'moreInfo'         => ''
            ]);
        }

        try {
            $now                   = (new \DateTime())->format(\DateTime::ATOM);
            $product['tags']       = is_array($product['tags']) ? json_encode($product['tags']) : '[]';
            $product['created_at'] = $now;
            $product['updated_at'] = $now;

            $model     = new ProductsModel($this->container);
            $productId = $model->add($product);
            $product   = $model->get($productId);

            $product['tags'] = json_decode($product['tags']);

            return $response->withJson($product)->withStatus(201);
        } catch (\Error $e) {
            $logger->error($e->getMessage());
        } catch (\PDOException $e) {
            $logger->error($e->getMessage());
        } catch (\Exception $e) {
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

    public function update(Request $request, Response $response, array $args)
    {
        /** @var Logger $logger */
        $logger    = $this->container->get('logger');
        $productId = (int)($args['id'] ?? 0);

        $logger->info('POST /products');

        /** @var Response $response */
        $response = $response->withHeader('Content-Type', 'application/json;charset=utf-8');

        if (!$this->acceptsJSON($request->getHeaderLine('Accept'))) {
            $logger->notice('Request must accept media-type: application/json');

            return $response->withStatus(400)->withJson([
                'status'           => 400,
                'developerMessage' => 'Request must accept media-type: application/json.',
                'userMessage'      => 'Bad Request.',
                'errorCode'        => '',
                'moreInfo'         => ''
            ]);
        }

        if (!$this->isJSON($request->getHeaderLine('Content-Type'))) {
            $logger->notice('Request must have Content-Type: application/json.');

            return $response->withStatus(400)->withJson([
                'status'           => 400,
                'developerMessage' => 'Request must have Content-Type: application/json.',
                'userMessage'      => 'Bad Request.',
                'errorCode'        => '',
                'moreInfo'         => ''
            ]);
        }

        $product = $request->getParsedBody();

        if (
            json_last_error() !== JSON_ERROR_NONE ||
            !is_array($product) || (
                !isset($product['name']) || empty($product['name']) ||
                !isset($product['price']) || empty($product['price'])
            )
        ) {
            $logger->notice('Request body or fields cannot be empty.');

            return $response->withStatus(400)->withJson([
                'status'           => 400,
                'developerMessage' => 'Request body or fields cannot be empty.',
                'userMessage'      => 'Bad Request.',
                'errorCode'        => '',
                'moreInfo'         => ''
            ]);
        }

        try {
            $model           = new ProductsModel($this->container);
            $existentProduct = $model->get($productId);

            if (!count($existentProduct)) {
                return $response->withStatus(500)->withJson([
                    'status'           => 500,
                    'developerMessage' => "Product ({$productId}) does not exist. Due to database capabilities new row can't be added.",
                    'userMessage'      => 'Unexpected error has occurred, try again later.',
                    'errorCode'        => '',
                    'moreInfo'         => ''
                ]);
            }

            $now                   = (new \DateTime())->format(\DateTime::ATOM);
            $product['tags']       = is_array($product['tags']) ? json_encode($product['tags']) : '[]';
            $product['updated_at'] = $now;

            $model->update($productId, $product);

            $product = $model->get($productId);

            $product['tags'] = json_decode($product['tags']);

            return $response->withJson($product)->withStatus(200);
        } catch (\Error $e) {
            $logger->error($e->getMessage());
        } catch (\PDOException $e) {
            $logger->error($e->getMessage());
        } catch (\Exception $e) {
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

    public function delete(Request $request, Response $response, array $args)
    {
        /** @var Logger $logger */
        $logger    = $this->container->get('logger');
        $productId = (int)($args['id'] ?? 0);

        $logger->info('DELETE /products/' . $productId);

        /** @var Response $response */
        $response = $response->withHeader('Content-Type', 'application/json;charset=utf-8');

        try {
            $model           = new ProductsModel($this->container);
            $existentProduct = $model->get($productId);

            if (!count($existentProduct)) {
                return $response->withStatus(500)->withJson([
                    'status'           => 500,
                    'developerMessage' => "Product ({$productId}) does not exist. Due to database capabilities new row can't be added.",
                    'userMessage'      => 'Unexpected error has occurred, try again later.',
                    'errorCode'        => '',
                    'moreInfo'         => ''
                ]);
            }

            $model->delete($productId);

            return $response->withStatus(200);
        } catch (\Error $e) {
            $logger->error($e->getMessage());
        } catch (\PDOException $e) {
            $logger->error($e->getMessage());
        } catch (\Exception $e) {
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
