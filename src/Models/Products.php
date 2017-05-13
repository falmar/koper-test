<?php
declare(strict_types = 1);
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
    public function getList(array $params = []): array
    {
        /** @var \PDO $dbh */
        $dbh      = $this->container->get('dbh');
        $results  = [];
        $limitStr = '';
        $orderStr = '';

        $limit     = $params['limit'] ?? 0;
        $offset    = $params['offset'] ?? 0;
        $sortField = $params['sortField'] ?? '';
        $sortOrder = $params['sortOrder'] ?? '';

        if ($limit && $offset) {
            $limitStr = " LIMIT {$limit} OFFSET {$offset}";
        } elseif ($limit) {
            $limitStr = " LIMIT {$limit}";
        }

        if ($sortField && $sortOrder) {
            $orderStr = " ORDER BY {$sortField} {$sortOrder}";
        }

        $ssql = "
          SELECT 
              id, 
              name, 
              tags, 
              price, 
              created_at, 
              updated_at 
          FROM product
          {$orderStr}
          {$limitStr}
        ;";

        /** @var \PDOStatement $stmt */
        $stmt = $dbh->prepare($ssql);

        $stmt->execute();

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            // perform any formatter if required
            $row['tags']   = json_decode($row['tags'] ?? '[]');
            $row['images'] = [];

            $results[] = $row;
        }

        return $results;
    }

    public function new(array $data): int
    {
        /** @var \PDO $dbh */
        $dbh  = $this->container->get('dbh');
        $ssql = '
            INSERT INTO product
            (name, tags, price, created_at, updated_at)
            VALUES 
            (?, ?, ?, ?, ?)
            RETURNING id;
        ';
        $stmt = $dbh->prepare($ssql);

        $stmt->bindColumn('id', $id, \PDO::PARAM_INT);

        $stmt->bindValue(1, $data['name'], \PDO::PARAM_STR);
        $stmt->bindValue(2, $data['tags'], \PDO::PARAM_STR);
        $stmt->bindValue(3, $data['price'], \PDO::PARAM_STR);
        $stmt->bindValue(4, $data['created_at'], \PDO::PARAM_STR);
        $stmt->bindValue(5, $data['updated_at'], \PDO::PARAM_STR);

        $stmt->execute();
        $stmt->fetch();

        return $id ?? 0;
    }
}
