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
        return [];
    }

    /**
     * Return the total amount of invoices
     * TODO: filtering
     *
     * @return int
     */
    public function count(): int
    {
        return 0;
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
        return [];
    }

    /**
     * Add a new invoice to database
     *
     * @param array $data
     * @return int
     */
    public function add(array $data): int
    {
        return 0;
    }

    /**
     * Update invoice entity
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        return false;
    }

    /**
     * Remove invoice from database
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        return false;
    }
}
