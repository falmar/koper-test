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
    /** @var null|ContainerInterface $container */
    protected $container = null;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Get the products from database
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

    /**
     * Add a new product to database
     *
     * @param array $data
     * @return int
     */
    public function add(array $data): int
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

    /**
     * Update product entity
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        if ($id <= 0 || !count($data)) {
            return false;
        }

        /** @var \PDO $dbh */
        $dbh = $this->container->get('dbh');

        $ssql = "
            UPDATE product 
            SET name = ?, tags = ?, price = ?, updated_at = ?
            WHERE id = ?;                    
        ";

        $stmt = $dbh->prepare($ssql);

        $stmt->bindValue(1, $data['name'], \PDO::PARAM_STR);
        $stmt->bindValue(2, $data['tags'], \PDO::PARAM_STR);
        $stmt->bindValue(3, $data['price'], \PDO::PARAM_STR);
        $stmt->bindValue(4, $data['updated_at'], \PDO::PARAM_STR);
        $stmt->bindValue(5, $id, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    /**
     * Remove product from database
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        if ($id <= 0) {
            return false;
        }

        /** @var \PDO $dbh */
        $dbh  = $this->container->get('dbh');
        $ssql = 'DELETE FROM product WHERE id = ?;';
        $stmt = $dbh->prepare($ssql);

        $stmt->bindValue(1, $id, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
}
