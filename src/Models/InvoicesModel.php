<?php
declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: falmar
 * Date: 5/22/17
 * Time: 4:53 PM
 */

namespace KoperTest\Models;


use Psr\Container\ContainerInterface;

class InvoicesModel
{
    /** @var null|ContainerInterface $container */
    protected $container = null;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Get an entity from database
     * @param int $id
     * @return array
     */
    public function get(int $id): array
    {
        if ($id <= 0) {
            return [];
        }

        /** @var \PDO $dbh */
        $dbh = $this->container->get('dbh');

        $stmt = $dbh->prepare(
            'SELECT id, code, status, customer, discount, tax, total , created_at, updated_at FROM invoice WHERE id = ?;'
        );

        $stmt->bindValue(1, $id, \PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$result) {
            return [];
        }

        $result['discount'] = (float)($result['discount'] ?? 0);
        $result['tax']      = (float)($result['tax'] ?? 0);
        $result['total']    = (float)($result['total'] ?? 0);

        return $result;
    }

    /**
     * Return the total amount of invoices
     * TODO: filtering
     *
     * @return int
     */
    public function count(): int
    {
        $count = 0;
        /** @var \PDO $dbh */
        $dbh = $this->container->get('dbh');

        $stmt = $dbh->prepare('SELECT COUNT(*) FROM invoice;');

        $stmt->bindColumn(1, $count, \PDO::PARAM_INT);
        $stmt->execute();
        $stmt->fetch();

        return $count;
    }

    /**
     * Get the invoices from database
     * TODO: filtering
     *
     * @param array $params
     * @return array
     */
    public function collection(array $params = []): array
    {
        /** @var \PDO $dbh */
        $dbh      = $this->container->get('dbh');
        $results  = [];
        $limitStr = '';
        $orderStr = '';

        $limit     = $params['limit'] ?? 25;
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
              id, code, status, customer, discount, tax, total , created_at, updated_at 
          FROM invoice
          {$orderStr}
          {$limitStr}
        ;";

        /** @var \PDOStatement $stmt */
        $stmt = $dbh->prepare($ssql);

        $stmt->execute();

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            // perform any formatter if required
            $row['discount'] = (float)($row['discount'] ?? 0);
            $row['tax']      = (float)($row['tax'] ?? 0);
            $row['total']    = (float)($row['total'] ?? 0);

            $results[] = $row;
        }

        return $results;
    }

    /**
     * Add a new invoice to database
     *
     * @param array $data
     * @return int
     */
    public function add(array $data): int
    {
        /** @var \PDO $dbh */
        $dbh  = $this->container->get('dbh');
        $ssql = '
            INSERT INTO invoice
            (code, status, customer, discount, tax, total , created_at, updated_at)
            VALUES 
            (?, ?, ?, ?, ?, ?, ?, ?)
            RETURNING id;
        ';
        $stmt = $dbh->prepare($ssql);

        $stmt->bindColumn('id', $id, \PDO::PARAM_INT);

        $stmt->bindValue(1, $data['code'], \PDO::PARAM_STR);
        $stmt->bindValue(2, $data['status'], \PDO::PARAM_STR);
        $stmt->bindValue(3, $data['customer'], \PDO::PARAM_STR);
        $stmt->bindValue(4, $data['discount'], \PDO::PARAM_STR);
        $stmt->bindValue(5, $data['tax'], \PDO::PARAM_STR);
        $stmt->bindValue(6, $data['total'], \PDO::PARAM_STR);
        $stmt->bindValue(7, $data['created_at'], \PDO::PARAM_STR);
        $stmt->bindValue(8, $data['updated_at'], \PDO::PARAM_STR);

        $stmt->execute();
        $stmt->fetch();

        return $id ?? 0;
    }

    /**
     * Update invoice entity
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
            UPDATE invoice 
            SET code = ?, status = ?, customer = ?, discount = ?, tax = ?, total = ?, updated_at = ?
            WHERE id = ?;                    
        ";

        $stmt = $dbh->prepare($ssql);

        $stmt->bindValue(1, $data['code'], \PDO::PARAM_STR);
        $stmt->bindValue(2, $data['status'], \PDO::PARAM_STR);
        $stmt->bindValue(3, $data['customer'], \PDO::PARAM_STR);
        $stmt->bindValue(4, $data['discount'], \PDO::PARAM_STR);
        $stmt->bindValue(5, $data['tax'], \PDO::PARAM_STR);
        $stmt->bindValue(6, $data['total'], \PDO::PARAM_STR);
        $stmt->bindValue(7, $data['updated_at'], \PDO::PARAM_STR);
        $stmt->bindValue(8, $id, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    /**
     * Remove invoice from database
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
        $ssql = 'DELETE FROM invoice WHERE id = ?;';
        $stmt = $dbh->prepare($ssql);

        $stmt->bindValue(1, $id, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
}
