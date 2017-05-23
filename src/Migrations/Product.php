<?php
declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: falmar
 * Date: 5/14/17
 * Time: 2:06 PM
 */

namespace KoperTest\Migrations;


class Product implements MigrationInterface
{
    protected $dbh = null;

    public function __construct(\PDO $dbh)
    {
        $this->dbh = $dbh;
    }

    /**
     * Create tables
     * @return void
     */
    public function up()
    {
        $this->dbh->exec('DROP TABLE IF EXISTS product');
        $this->dbh->exec('
            CREATE TABLE product (
              id         SERIAL PRIMARY KEY,
              name       VARCHAR(512)   NOT NULL,
              tags       VARCHAR(1024)  NOT NULL,
              price      NUMERIC(12, 2) NOT NULL,
              created_at TIMESTAMPTZ    NOT NULL,
              updated_at TIMESTAMPTZ    NOT NULL
            );
        ');
    }

    /**
     * Remove tables
     *
     * @return void
     */
    public function down()
    {
        $this->dbh->exec('DROP TABLE IF EXISTS product');
    }

    /**
     * Fill tables with data
     * @return void
     */
    public function seed()
    {
        $products = [
            [
                'name'       => 'MX-4 Thermal Compound',
                'tags'       => '["Computers", "CPU", "Heat"]',
                'price'      => 6.59,
                'created_at' => '2017-05-15T14:00:00Z',
                'updated_at' => '2017-05-15T14:00:00Z'
            ],
            [
                'name'       => 'Acer Aspire VX15',
                'tags'       => '["Computers"]',
                'price'      => 1049.99,
                'created_at' => '2017-05-15T15:00:00Z',
                'updated_at' => '2017-05-15T15:00:00Z'
            ]
        ];

        $stmt = $this->dbh->prepare(
            'INSERT INTO product 
              (name, tags, price, created_at, updated_at)
             VALUES
              (?, ?, ?, ?, ?)'
        );

        foreach ($products as $product) {
            $stmt->execute(array_values($product));
        }
    }
}
