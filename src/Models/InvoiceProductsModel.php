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
        return [];
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
        return 0;
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
        return [];
    }

    /**
     * Add a new invoiceProduct to database
     *
     * @param array $data
     * @return bool
     */
    public function add(array $data): bool
    {
        return false;
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
        return false;
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
        return false;
    }
}
