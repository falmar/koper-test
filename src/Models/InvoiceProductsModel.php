<?php
declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: falmar
 * Date: 5/22/23
 * Time: 11:20 PM
 */

namespace KoperTest\Models;


use Psr\Container\ContainerInterface;


class InvoiceProductsModel
{
    /** @var null|ContainerInterface $container */
    protected $container = null;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Get an entity from database
     *
     * @param int $invoiceId
     * @param int $productId
     * @return array
     */
    public function get(int $invoiceId, int $productId): array
    {
        if ($invoiceId <= 0 || $productId <= 0) {
            return [];
        }

        /** @var \PDO $dbh */
        $dbh = $this->container->get('dbh');

        $stmt = $dbh->prepare(
            'SELECT invoice_id, product_id, price, quantity FROM invoice_products WHERE invoice_id = ? AND product_id = ?;'
        );

        $stmt->bindValue(1, $invoiceId, \PDO::PARAM_INT);
        $stmt->bindValue(2, $productId, \PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$result) {
            return [];
        }

        $result['price'] = (float)($result['price'] ?? 0);

        return $result;
    }

    /**
     * Return the total amount of invoiceProducts
     * TODO: filtering
     *
     * @param array $params
     * @return int
     */
    public function count(array $params): int
    {
        /** @var \PDO $dbh */
        $count     = 0;
        $dbh       = $this->container->get('dbh');
        $invoiceId = $params['invoice_id'];

        if ($invoiceId <= 0) {
            return 0;
        }

        $stmt = $dbh->prepare('SELECT COUNT(*) FROM invoice_products WHERE invoice_id = ?;');

        $stmt->bindColumn(1, $count, \PDO::PARAM_INT);

        $stmt->bindValue(1, $params['invoice_id'], \PDO::PARAM_INT);

        $stmt->execute();
        $stmt->fetch();

        return $count;
    }

    /**
     * Get the invoiceProducts from database
     * TODO: filtering
     *
     * @param array $params
     * @return array
     */
    public function collection(array $params): array
    {
        /** @var \PDO $dbh */
        $dbh      = $this->container->get('dbh');
        $results  = [];
        $limitStr = '';
        $orderStr = '';

        $invoiceId = $params['invoice_id'];
        $limit     = $params['limit'] ?? 25;
        $offset    = $params['offset'] ?? 0;
        $sortField = $params['sortField'] ?? '';
        $sortOrder = $params['sortOrder'] ?? '';

        if ($invoiceId <= 0) {
            return [];
        }

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
              invoice_id, product_id, price, quantity 
          FROM invoice_products
          WHERE invoice_id = ?
          {$orderStr}
          {$limitStr}
        ;";

        /** @var \PDOStatement $stmt */
        $stmt = $dbh->prepare($ssql);

        $stmt->bindValue(1, $invoiceId, \PDO::PARAM_INT);
        $stmt->execute();

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            // perform any formatter if required
            $row['price'] = (float)($row['price'] ?? 0);

            $results[] = $row;
        }

        return $results;
    }

    /**
     * Add a new invoiceProduct to database
     *
     * @param array $data
     * @return bool
     */
    public function add(array $data): bool
    {
        /** @var \PDO $dbh */
        $dbh  = $this->container->get('dbh');
        $ssql = '
            INSERT INTO invoice_products
            (invoice_id, product_id, price, quantity)
            VALUES 
            (?, ?, ?, ?);
        ';
        $stmt = $dbh->prepare($ssql);

        $stmt->bindValue(1, $data['invoice_id'], \PDO::PARAM_STR);
        $stmt->bindValue(2, $data['product_id'], \PDO::PARAM_STR);
        $stmt->bindValue(3, $data['price'], \PDO::PARAM_STR);
        $stmt->bindValue(4, $data['quantity'], \PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    /**
     * Update invoiceProduct entity
     *
     * @param int $invoiceId
     * @param int $productId
     * @param array $data
     * @return bool
     */
    public function update(int $invoiceId, int $productId, array $data): bool
    {
        if ($invoiceId <= 0 || $productId <= 0 || !count($data)) {
            return false;
        }

        /** @var \PDO $dbh */
        $dbh = $this->container->get('dbh');

        $ssql = "
            UPDATE invoice_products 
            SET price = ?, quantity = ?
            WHERE invoice_id = ? AND product_id = ?;                    
        ";

        $stmt = $dbh->prepare($ssql);

        $stmt->bindValue(1, $data['price'], \PDO::PARAM_STR);
        $stmt->bindValue(2, $data['quantity'], \PDO::PARAM_INT);
        $stmt->bindValue(3, $invoiceId, \PDO::PARAM_INT);
        $stmt->bindValue(4, $productId, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    /**
     * Remove invoiceProduct from database
     *
     * @param int $invoiceId
     * @param int $productId
     * @return bool
     */
    public function delete(int $invoiceId, int $productId): bool
    {
        if ($invoiceId <= 0 || $productId <= 0) {
            return false;
        }

        /** @var \PDO $dbh */
        $dbh  = $this->container->get('dbh');
        $ssql = 'DELETE FROM invoice_products WHERE invoice_id = ? AND product_id = ?;';
        $stmt = $dbh->prepare($ssql);

        $stmt->bindValue(1, $invoiceId, \PDO::PARAM_INT);
        $stmt->bindValue(2, $productId, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
}
