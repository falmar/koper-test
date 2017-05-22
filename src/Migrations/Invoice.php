<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: falmar
 * Date: 5/14/17
 * Time: 2:06 PM
 */

namespace KoperTest\db;


class Invoice implements MigrationInterface
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
        $this->dbh->exec('DROP TABLE IF EXISTS invoice');
        $this->dbh->exec('CREATE TABLE invoice (
                              id         SERIAL PRIMARY KEY,
                              code       VARCHAR(45)    NOT NULL,
                              status     VARCHAR(45)    NOT NULL,
                              customer   VARCHAR(128)   NOT NULL,
                              discount   NUMERIC(12, 2) NOT NULL,
                              tax        NUMERIC(12, 2) NOT NULL,
                              total      NUMERIC(12, 2) NOT NULL,
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
        $this->dbh->exec('DROP TABLE IF EXISTS invoice;');
    }

    /**
     * Fill tables with data
     * @return void
     */
    public function seed()
    {
        $products = [
            [
                'code'       => 'IV001',
                'status'     => 'PAID',
                'customer'   => 'David',
                'discount'   => 50,
                'tax'        => 74,
                'total'      => 1049.99,
                'created_at' => '2017-05-15 19:00:00+00',
                'updated_at' => '2017-05-15 19:00:00+00',
            ],
            [
                'code'       => 'IV002',
                'status'     => 'PAID',
                'customer'   => 'David',
                'discount'   => 0,
                'tax'        => 0,
                'total'      => 36.67,
                'created_at' => '2017-05-14 17:00:00+00',
                'updated_at' => '2017-05-14 17:00:00+00',
            ],
        ];

        $stmt = $this->dbh->prepare(
            'INSERT INTO invoice
            (code, status, customer, discount, tax, total , created_at, updated_at)
            VALUES 
            (?, ?, ?, ?, ?, ?, ?, ?);'
        );

        foreach ($products as $product) {
            $stmt->execute($product);
        }
    }
}
