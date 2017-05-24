<?php
declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: falmar
 * Date: 5/15/17
 * Time: 21:15 PM
 */

namespace KoperTest\Controllers;


use KoperTest\Models\InvoicesModel;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;


class InvoicesController extends BaseController
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
        $invoiceId = (int)($args['id'] ?? 0);

        $logger->info('GET /invoices/' . $invoiceId);

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
            $model   = new InvoicesModel($this->container);
            $invoice = $model->get($invoiceId);

            if (!$invoice) {
                $logger->info('Invoice does not exist');

                return $response->withStatus(400)->withJson([
                    'status'           => 400,
                    'developerMessage' => "Invoice ({$invoiceId}) does not exist.",
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
     * @return Response
     */
    public function collection(Request $request, Response $response)
    {
        /** @var Logger $logger */
        $logger = $this->container->get('logger');

        $logger->info('GET /invoices');

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
            $allowedSortField = ['id', 'code', 'status', 'customer', 'discount', 'tax', 'total', 'updated_at'];
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

            $model = new InvoicesModel($this->container);

            $params = [
                'limit'     => $limit > 0 ? $limit : 25,
                'offset'    => $offset,
                'sortField' => $sortField,
                'sortOrder' => $sortOrder,
            ];

            $results = $model->collection($params);

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

        $logger->info('POST /invoices');

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
                !isset($invoice['code']) || empty($invoice['code']) ||
                !isset($invoice['status']) || empty($invoice['status']) ||
                !isset($invoice['customer']) || empty($invoice['customer']) ||
                !isset($invoice['discount']) || (empty($invoice['discount']) && $invoice['discount'] !== 0) ||
                !isset($invoice['tax']) || (empty($invoice['tax']) && $invoice['tax'] !== 0) ||
                !isset($invoice['total']) || empty($invoice['total'])
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
            $invoice['created_at'] = $now;
            $invoice['updated_at'] = $now;

            $model     = new InvoicesModel($this->container);
            $invoiceId = $model->add($invoice);
            $invoice   = $model->get($invoiceId);

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
        $invoiceId = (int)($args['id'] ?? 0);

        $logger->info('POST /invoices');

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
                !isset($invoice['code']) || empty($invoice['code']) ||
                !isset($invoice['status']) || empty($invoice['status']) ||
                !isset($invoice['customer']) || empty($invoice['customer']) ||
                !isset($invoice['discount']) || (empty($invoice['discount']) && $invoice['discount'] !== 0) ||
                !isset($invoice['tax']) || (empty($invoice['tax']) && $invoice['tax'] !== 0) ||
                !isset($invoice['total']) || empty($invoice['total'])
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
            $model           = new InvoicesModel($this->container);
            $existentInvoice = $model->get($invoiceId);

            if (!count($existentInvoice)) {
                return $response->withStatus(500)->withJson([
                    'status'           => 500,
                    'developerMessage' => "Invoice ({$invoiceId}) does not exist. Due to database capabilities new row can't be added.",
                    'userMessage'      => 'Unexpected error has occurred, try again later.',
                    'errorCode'        => '',
                    'moreInfo'         => ''
                ]);
            }

            $now                   = (new \DateTime())->format(\DateTime::ATOM);
            $invoice['updated_at'] = $now;

            $model->update($invoiceId, $invoice);

            $invoice = $model->get($invoiceId);

            return $response->withJson($invoice)->withStatus(200);
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
        $invoiceId = (int)($args['id'] ?? 0);

        $logger->info('DELETE /invoices/' . $invoiceId);

        /** @var Response $response */
        $response = $response->withHeader('Content-Type', 'application/json;charset=utf-8');

        try {
            $model           = new InvoicesModel($this->container);
            $existentInvoice = $model->get($invoiceId);

            if (!count($existentInvoice)) {
                return $response->withStatus(500)->withJson([
                    'status'           => 500,
                    'developerMessage' => "Invoice ({$invoiceId}) does not exist.",
                    'userMessage'      => 'Unexpected error has occurred, try again later.',
                    'errorCode'        => '',
                    'moreInfo'         => ''
                ]);
            }

            $model->delete($invoiceId);

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
