<?php
/**
 * Created by PhpStorm.
 * User: falmar
 * Date: 5/10/17
 * Time: 5:28 PM
 */

namespace KoperTest\Controllers\Products;

use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class ProductList
{
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response)
    {
        /** @var Logger $logger */
        $logger = $this->container->get('logger');
    }

    /**
     * @param array $params
     * @return array
     */
    public function getData($params)
    {
        $dbh      = $this->container->get('dbh');
        $results  = [];
        $limitStr = '';
        $orderStr = '';

        $limit     = $params['limit'];
        $offset    = $params['offset'];
        $sortField = $params['sortField'];
        $sortOrder = $params['sortOrder'];

        if ($limit && $offset) {
            $limitStr = "LIMIT {$limit} OFFSET {$offset}";
        } elseif ($limit) {
            $limitStr = "LIMIT {$limit}";
        }

        if ($sortField && $sortOrder) {
            $orderStr = "ORDER BY {$sortField} {$sortOrder}";
        }

        $ssql = trim("SELECT id, name FROM products {$limitStr} {$orderStr}") . ';';
        $stmt = $dbh->prepare($ssql);

        $stmt->execute();

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            // perform any formatter if required

            $results[] = $row;
        }

        return $results;
    }
}
