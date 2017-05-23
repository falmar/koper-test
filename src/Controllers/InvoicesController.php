<?php
declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: falmar
 * Date: 5/15/23
 * Time: 18:14 PM
 */

namespace KoperTest\Controllers;


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
     * @param array $args
     * @return Response
     */
    public function get(Request $request, Response $response, $args)
    {
        return $response;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function collection(Request $request, Response $response)
    {
        return $response;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function add(Request $request, Response $response)
    {
        return $response;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function update(Request $request, Response $response, array $args)
    {
        return $response;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function delete(Request $request, Response $response, array $args)
    {
        return $response;
    }
}
