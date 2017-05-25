<?php
declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: falmar
 * Date: 5/15/24
 * Time: 05:03 PM
 */

namespace KoperTest\Controllers;


use KoperTest\Models\InvoiceProductsModel;
use KoperTest\Models\InvoicesModel;
use KoperTest\Models\ProductsModel;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;


class InvoiceProductsController extends BaseController
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
    public function get(Request $request, Response $response, array $args)
    {
        /** @var Logger $logger */
        $logger    = $this->container->get('logger');
        $invoiceId = (int)($args['invoiceId'] ?? 0);
        $productId = (int)($args['productId'] ?? 0);

        $logger->info('GET /invoices/' . $invoiceId . '/products/' . $productId);
        $logger->info('args', $args);

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
            $model   = new InvoiceProductsModel($this->container);
            $invoice = $model->get($invoiceId, $productId);

            if (!$invoice) {
                $logger->info('Invoice does not exist');

                return $response->withStatus(400)->withJson([
                    'status'           => 400,
                    'developerMessage' => "InvoiceProduct ({$invoiceId}, {$productId}) does not exist.",
                    'userMessage'      => 'Invoice does not exist.',
                    'errorCode'        => '',
                    'moreInfo'         => ''
                ]);
            }

            return $response->withJson($invoice);
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
     * @param array $args
     * @return Response
     */
    public function collection(Request $request, Response $response, array $args)
    {
        /** @var Logger $logger */
        $logger    = $this->container->get('logger');
        $invoiceId = (int)($args['invoiceId'] ?? 0);

        $logger->info('GET /invoices/' . $invoiceId . '/products/');

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
            $limit  = $request->getParam('limit', 0);
            $offset = $request->getParam('offset', 0);
            $sort   = $request->getParam('sort', '');

            // sanitize input
            $allowedSortField = ['product_id', 'price', 'quantity'];
            $limit            = (int)preg_replace('/[^0-9]/', '', $limit);
            $offset           = (int)preg_replace('/[^0-9]/', '', $offset);
            $parsedSort       = $this->parseOrder($sort);
            $sortField        = $parsedSort['sortField'];
            $sortOrder        = strtoupper($parsedSort['sortOrder']);

            if (!in_array($sortField, $allowedSortField)) {
                $sortField = '';
            }

            if ($sortOrder !== 'DESC' && $sortOrder !== 'ASC') {
                $sortOrder = '';
            }

            $model = new InvoiceProductsModel($this->container);

            $params = [
                'invoice_id' => $invoiceId,
                'limit'      => $limit > 0 ? $limit : 25,
                'offset'     => $offset,
                'sortField'  => $sortField,
                'sortOrder'  => $sortOrder,
            ];

            $results = $model->collection($params);

            return $response->withJson([
                'metadata' => [
                    'resultset' => [
                        'count'  => $model->count(['invoice_id' => $invoiceId]),
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
     * @param array $args
     * @return Response
     */
    public function add(Request $request, Response $response, array $args)
    {
        /** @var Logger $logger */
        $logger    = $this->container->get('logger');
        $invoiceId = (int)($args['invoiceId'] ?? 0);

        $logger->info('POST /invoices/' . $invoiceId . '/products/');

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

        $invoice = $request->getParsedBody();

        if (
            json_last_error() !== JSON_ERROR_NONE ||
            !is_array($invoice) || (
                !isset($invoice['product_id']) || empty($invoice['product_id']) ||
                !isset($invoice['price']) || empty($invoice['price']) ||
                !isset($invoice['quantity']) || empty($invoice['quantity'])
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
            $productId    = $invoice['product_id'];
            $invoiceModel = new InvoicesModel($this->container);
            $productModel = new ProductsModel($this->container);

            $existentInvoice = $invoiceModel->get($invoiceId);
            $existentProduct = $productModel->get($productId);

            if (!count($existentInvoice) || !count($existentProduct)) {
                return $response->withStatus(500)->withJson([
                    'status'           => 500,
                    'developerMessage' => "InvoiceProduct ({$invoiceId}, {$productId}) does not exist. Due to database capabilities new row can't be added.",
                    'userMessage'      => 'Unexpected error has occurred, try again later.',
                    'errorCode'        => '',
                    'moreInfo'         => ''
                ]);
            }

            $invoice['invoice_id'] = $invoiceId;

            $model = new InvoiceProductsModel($this->container);
            $model->add($invoice);
            $invoice = $model->get($invoiceId, $invoice['product_id']);

            return $response->withJson($invoice)->withStatus(201);
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
     * @param $args
     * @return Response
     */
    public function update(Request $request, Response $response, array $args)
    {
        /** @var Logger $logger */
        $logger    = $this->container->get('logger');
        $invoiceId = (int)($args['invoiceId'] ?? 0);
        $productId = (int)($args['productId'] ?? 0);

        $logger->info('POST /invoices/' . $invoiceId . '/products/' . $productId);

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

        $invoiceProduct = $request->getParsedBody();

        if (
            json_last_error() !== JSON_ERROR_NONE ||
            !is_array($invoiceProduct) || (
                !isset($invoiceProduct['product_id']) || empty($invoiceProduct['product_id']) ||
                !isset($invoiceProduct['price']) || empty($invoiceProduct['price']) ||
                !isset($invoiceProduct['quantity']) || empty($invoiceProduct['quantity'])
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
            $invoiceModel    = new InvoicesModel($this->container);
            $productModel    = new ProductsModel($this->container);
            $model           = new InvoiceProductsModel($this->container);
            $existentInvoice = $invoiceModel->get($invoiceId);
            $existentProduct = $productModel->get($productId);
            $existentIP      = $model->get($invoiceId, $productId);

            if (!count($existentInvoice) || !count($existentProduct) || !count($existentIP)) {
                return $response->withStatus(500)->withJson([
                    'status'           => 500,
                    'developerMessage' => "InvoiceProduct ({$invoiceId}, {$productId}) does not exist. Due to database capabilities new row can't be added.",
                    'userMessage'      => 'Unexpected error has occurred, try again later.',
                    'errorCode'        => '',
                    'moreInfo'         => ''
                ]);
            }

            $model->update($invoiceId, $productId, $invoiceProduct);

            $invoiceProduct = $model->get($invoiceId, $productId);

            return $response->withJson($invoiceProduct)->withStatus(200);
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
     * @param $args
     * @return Response
     */
    public function delete(Request $request, Response $response, array $args)
    {
        /** @var Logger $logger */
        $logger    = $this->container->get('logger');
        $invoiceId = (int)($args['invoiceId'] ?? 0);
        $productId = (int)($args['productId'] ?? 0);

        $logger->info('DELETE /invoices/' . $invoiceId . '/products/' . $productId);

        /** @var Response $response */
        $response = $response->withHeader('Content-Type', 'application/json;charset=utf-8');

        try {
            $model           = new InvoiceProductsModel($this->container);
            $existentInvoice = $model->get($invoiceId, $productId);

            if (!count($existentInvoice)) {
                return $response->withStatus(500)->withJson([
                    'status'           => 500,
                    'developerMessage' => "InvoiceProduct ({$invoiceId}, {$productId}) does not exist.",
                    'userMessage'      => 'Unexpected error has occurred, try again later.',
                    'errorCode'        => '',
                    'moreInfo'         => ''
                ]);
            }

            $model->delete($invoiceId, $productId);

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
