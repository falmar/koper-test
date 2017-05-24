<?php
declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: falmar
 * Date: 5/14/24
 * Time: 10:26 PM
 */

namespace KoperTest\Migrations;


class InvoiceProduct implements MigrationInterface
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
        $this->dbh->exec('DROP TABLE IF EXISTS invoice_products');
        $this->dbh->exec('CREATE TABLE invoice_products (
                              invoice_id INT NOT NULL,
                              product_id INT NOT NULL,
                              price      NUMERIC(12,2) NOT NULL,
                              quantity    BIGINT NOT NULL,
                              FOREIGN KEY (invoice_id) REFERENCES invoice(id) ON DELETE CASCADE,
                              FOREIGN KEY (product_id) REFERENCES product(id) ON DELETE CASCADE
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
        $this->dbh->exec('DROP TABLE IF EXISTS invoice_products;');
    }

    /**
     * Fill tables with data
     * @return void
     */
    public function seed()
    {
        $products = [
            [
                'invoice_id' => 1,
                'product_id' => 2,
                'price'      => 1049.99,
                'quantity'   => 1
            ],
            [
                'invoice_id' => 1,
                'product_id' => 1,
                'price'      => 12.22,
                'quantity'   => 2
            ],
            [
                'invoice_id' => 2,
                'product_id' => 1,
                'price'      => 6.11,
                'quantity'   => 6
            ]
        ];

        $stmt = $this->dbh->prepare(
            'INSERT INTO invoice_products
            (invoice_id, product_id, price, quantity)
            VALUES 
            (?, ?, ?, ?);'
        );

        foreach ($products as $product) {
            $stmt->execute(array_values($product));
        }
    }
}
