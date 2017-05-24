<?php
declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: falmar
 * Date: 5/14/17
 * Time: 12:39 PM
 */

namespace Tests\Models;


use KoperTest\Migrations\Invoice;
use KoperTest\Models\InvoicesModel;
use Tests\BaseTestCase;
use Tests\Mocks\Container\Container;


class InvoicesIntegrationTest extends BaseTestCase
{
    /** @var \PDO */
    protected static $dbh = null;
    /** @var Invoice */
    protected static $migration = null;

    public static function setUpBeforeClass()
    {
        self::$dbh       = self::getPDO();
        self::$migration = new Invoice(self::$dbh);
    }

    public function setUp()
    {
        self::$migration->up();
        self::$migration->seed();
    }

    public function tearDown()
    {
        self::$migration->down();
    }

    public function testCount()
    {
        $expectedResult = 2;
        $container      = new Container(['dbh' => self::$dbh]);
        $model          = new InvoicesModel($container);

        $result = $model->count();

        $this->assertEquals($expectedResult, $result);
    }

    public function testCountNoRows()
    {
        self::$dbh->exec('DELETE FROM invoice;');

        $expectedResult = 0;
        $container      = new Container(['dbh' => self::$dbh]);
        $model          = new InvoicesModel($container);

        $result = $model->count();

        $this->assertEquals($expectedResult, $result);
    }

    public function testGetExistingRow()
    {
        $expectedResult = [
            'id'         => 1,
            'code'       => 'IV001',
            'status'     => 'PAID',
            'customer'   => 'David',
            'discount'   => 50,
            'tax'        => 74,
            'total'      => 1049.99,
            'created_at' => '2017-05-15 19:00:00+00',
            'updated_at' => '2017-05-15 19:00:00+00',
        ];
        $container      = new Container(['dbh' => self::$dbh]);
        $model          = new InvoicesModel($container);

        $result = $model->get(1);

        $this->assertEquals($expectedResult, $result);
    }

    public function testGetNonExistingRow()
    {
        $expectedResult = [];
        $container      = new Container(['dbh' => self::$dbh]);
        $model          = new InvoicesModel($container);

        $result = $model->get(5);

        $this->assertEquals($expectedResult, $result);
    }

    public function testCollectionAll()
    {
        $expectedResult = [
            [
                'id'         => 1,
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
                'id'         => 2,
                'code'       => 'IV002',
                'status'     => 'PENDING',
                'customer'   => 'David',
                'discount'   => 0,
                'tax'        => 0,
                'total'      => 36.67,
                'created_at' => '2017-05-14 17:00:00+00',
                'updated_at' => '2017-05-14 17:00:00+00',
            ]
        ];

        $container = new Container(['dbh' => self::$dbh]);
        $model     = new InvoicesModel($container);

        $result = $model->collection();

        $this->assertEquals($expectedResult, $result);
    }

    public function testCollectionLimit()
    {
        $expectedResult = [
            [
                'id'         => 1,
                'code'       => 'IV001',
                'status'     => 'PAID',
                'customer'   => 'David',
                'discount'   => 50,
                'tax'        => 74,
                'total'      => 1049.99,
                'created_at' => '2017-05-15 19:00:00+00',
                'updated_at' => '2017-05-15 19:00:00+00',
            ]
        ];

        $container = new Container(['dbh' => self::$dbh]);
        $model     = new InvoicesModel($container);

        $result = $model->collection([
            'limit' => 1
        ]);

        $this->assertEquals($expectedResult, $result);
    }

    public function testCollectionLimitAndOffset()
    {
        $expectedResult = [
            [
                'id'         => 2,
                'code'       => 'IV002',
                'status'     => 'PENDING',
                'customer'   => 'David',
                'discount'   => 0,
                'tax'        => 0,
                'total'      => 36.67,
                'created_at' => '2017-05-14 17:00:00+00',
                'updated_at' => '2017-05-14 17:00:00+00',
            ]
        ];

        $container = new Container(['dbh' => self::$dbh]);
        $model     = new InvoicesModel($container);

        $result = $model->collection([
            'limit'  => 1,
            'offset' => 1
        ]);

        $this->assertEquals($expectedResult, $result);
    }

    public function testCollectionOrderBy()
    {
        $expectedResult = [
            [
                'id'         => 2,
                'code'       => 'IV002',
                'status'     => 'PENDING',
                'customer'   => 'David',
                'discount'   => 0,
                'tax'        => 0,
                'total'      => 36.67,
                'created_at' => '2017-05-14 17:00:00+00',
                'updated_at' => '2017-05-14 17:00:00+00',
            ],
            [
                'id'         => 1,
                'code'       => 'IV001',
                'status'     => 'PAID',
                'customer'   => 'David',
                'discount'   => 50,
                'tax'        => 74,
                'total'      => 1049.99,
                'created_at' => '2017-05-15 19:00:00+00',
                'updated_at' => '2017-05-15 19:00:00+00',
            ]
        ];

        $container = new Container(['dbh' => self::$dbh]);
        $model     = new InvoicesModel($container);

        $result = $model->collection([
            'sortField' => 'total',
            'sortOrder' => 'ASC'
        ]);

        $this->assertEquals($expectedResult, $result);
    }

    public function testAdd()
    {
        $expectedResult = 3;
        $container      = new Container(['dbh' => self::$dbh]);
        $model          = new InvoicesModel($container);

        $result = $model->add([
            'code'       => 'IV003',
            'status'     => 'PENDING',
            'customer'   => 'David',
            'discount'   => 0,
            'tax'        => 0,
            'total'      => 61,
            'created_at' => '2017-05-23 16:00:00+00',
            'updated_at' => '2017-05-23 16:00:00+00',
        ]);

        $this->assertEquals($expectedResult, $result);
    }

    public function testUpdateExistingRow()
    {
        $expectedResult = true;
        $expectedRow    = [
            'id'         => 1,
            'code'       => 'IV001',
            'status'     => 'CANCELED',
            'customer'   => 'David',
            'discount'   => '0.00', // postgres return float as string to keep precisions
            'tax'        => '0.00',
            'total'      => '11.00',
            'created_at' => '2017-05-15 19:00:00+00',
            'updated_at' => '2017-05-23 16:00:00+00',
        ];
        $container      = new Container(['dbh' => self::$dbh]);
        $model          = new InvoicesModel($container);

        $result = $model->update(1, [
            'code'       => 'IV001',
            'status'     => 'CANCELED',
            'customer'   => 'David',
            'discount'   => 0,
            'tax'        => 0,
            'total'      => 11,
            'updated_at' => '2017-05-23 16:00:00+00',
        ]);

        $this->assertEquals($expectedResult, $result);

        $stmt = self::$dbh->prepare(
            'SELECT id, code, status, customer, discount, tax, total, created_at, updated_at FROM invoice WHERE id = 1'
        );

        $stmt->execute();

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        $this->assertEquals($expectedRow, $row);
    }

    public function testUpdateNonExistingRow()
    {
        $expectedResult = false;
        $container      = new Container(['dbh' => self::$dbh]);
        $model          = new InvoicesModel($container);

        $result = $model->update(5, [
            'code'       => 'IV001',
            'status'     => 'CANCELED',
            'customer'   => 'David',
            'discount'   => 0,
            'tax'        => 0,
            'total'      => 11,
            'updated_at' => '2017-05-23 16:00:00+00',
        ]);

        $this->assertEquals($expectedResult, $result);
    }

    public function testDeleteExistingRow()
    {
        $expectedResult = true;
        $expectedCount  = 1;
        $container      = new Container(['dbh' => self::$dbh]);
        $model          = new InvoicesModel($container);

        $result = $model->delete(1);

        $this->assertEquals($expectedResult, $result);

        $stmt = self::$dbh->prepare('SELECT COUNT(*) FROM invoice');

        $stmt->bindColumn(1, $count, \PDO::PARAM_INT);
        $stmt->execute();
        $stmt->fetch();

        $this->assertEquals($expectedCount, $count);
    }

    public function testDeleteNonExistingRow()
    {
        $expectedResult = false;
        $container      = new Container(['dbh' => self::$dbh]);
        $model          = new InvoicesModel($container);

        $result = $model->delete(5);

        $this->assertEquals($expectedResult, $result);
    }
}
