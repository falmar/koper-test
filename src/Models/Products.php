<?php
/**
 * Created by PhpStorm.
 * User: falmar
 * Date: 5/11/17
 * Time: 3:27 PM
 */

namespace KoperTest\Models;


use Psr\Container\ContainerInterface;

class Products
{
    protected $container = null;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param array $params
     * @return array
     */
    public function getData($params)
    {
        /** @var \PDO $dbh */
        $dbh      = $this->container->get('dbh');
        $results  = [];
        $limitStr = '';
        $orderStr = '';

        $limit     = $params['limit'];
        $offset    = $params['offset'];
        $sortField = $params['sortField'];
        $sortOrder = $params['sortOrder'];

        if ($limit && $offset) {
            $limitStr = " LIMIT {$limit} OFFSET {$offset}";
        } elseif ($limit) {
            $limitStr = " LIMIT {$limit}";
        }

        if ($sortField && $sortOrder) {
            $orderStr = " ORDER BY {$sortField} {$sortOrder}";
        }

        $ssql = "SELECT id, name FROM products" . $orderStr . $limitStr . ";";

        /** @var \PDOStatement $stmt */
        $stmt = $dbh->prepare($ssql);

        $stmt->execute();

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            // perform any formatter if required

            $results[] = $row;
        }

        return $results;
    }
}
