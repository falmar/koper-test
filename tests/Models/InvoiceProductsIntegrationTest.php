<?php
declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: falmar
 * Date: 5/14/24
 * Time: 01:21 PM
 */

namespace Tests\Models;


use KoperTest\Migrations\Invoice;
use KoperTest\Migrations\InvoiceProduct;
use KoperTest\Migrations\Product;
use KoperTest\Models\InvoiceProductsModel;
use Tests\BaseTestCase;
use Tests\Mocks\Container\Container;


class InvoiceProductsIntegrationTest extends BaseTestCase
{
    /** @var \PDO */
    protected static $dbh = null;
    /** @var InvoiceProduct */
    protected static $migration = null;
    /** @var Product */
    protected static $migrationProduct = null;
    /** @var Invoice */
    protected static $migrationInvoice = null;

    public static function setUpBeforeClass()
    {
        self::$dbh              = self::getPDO();
        self::$migration        = new InvoiceProduct(self::$dbh);
        self::$migrationProduct = new Product(self::$dbh);
        self::$migrationInvoice = new Invoice(self::$dbh);
    }

    public function setUp()
    {
        self::$migrationProduct->up();
        self::$migrationProduct->seed();

        self::$migrationInvoice->up();
        self::$migrationInvoice->seed();

        self::$migration->up();
        self::$migration->seed();
    }

    public function tearDown()
    {
        self::$migration->down();
        self::$migrationProduct->down();
        self::$migrationInvoice->down();
    }

    public function testCount()
    {
        $expectedResult = 2;
        $container      = new Container(['dbh' => self::$dbh]);
        $model          = new InvoiceProductsModel($container);

        $result = $model->count([
            'invoice_id' => 1
        ]);

        $this->assertEquals($expectedResult, $result);
    }

    public function testCountNoRows()
    {
        self::$dbh->exec('DELETE FROM invoice_products;');

        $expectedResult = 0;
        $container      = new Container(['dbh' => self::$dbh]);
        $model          = new InvoiceProductsModel($container);

        $result = $model->count([
            'invoice_id' => 1
        ]);

        $this->assertEquals($expectedResult, $result);
    }

    public function testGetExistingRow()
    {
        $expectedResult = [
            'invoice_id' => 1,
            'product_id' => 2,
            'price'      => 1049.99,
            'quantity'   => 1
        ];
        $container      = new Container(['dbh' => self::$dbh]);
        $model          = new InvoiceProductsModel($container);

        $result = $model->get(1, 2);

        $this->assertEquals($expectedResult, $result);
    }

    public function testGetNonExistingRow()
    {
        $expectedResult = [];
        $container      = new Container(['dbh' => self::$dbh]);
        $model          = new InvoiceProductsModel($container);

        $result = $model->get(1, 5);

        $this->assertEquals($expectedResult, $result);
    }

    public function testCollectionAll()
    {
        $expectedResult = [
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
            ]
        ];

        $container = new Container(['dbh' => self::$dbh]);
        $model     = new InvoiceProductsModel($container);

        $result = $model->collection([
            'invoice_id' => 1
        ]);

        $this->assertEquals($expectedResult, $result);
    }

    public function testCollectionLimit()
    {
        $expectedResult = [
            [
                'invoice_id' => 1,
                'product_id' => 2,
                'price'      => 1049.99,
                'quantity'   => 1
            ]
        ];

        $container = new Container(['dbh' => self::$dbh]);
        $model     = new InvoiceProductsModel($container);

        $result = $model->collection([
            'invoice_id' => 1,
            'limit'      => 1
        ]);

        $this->assertEquals($expectedResult, $result);
    }

    public function testCollectionLimitAndOffset()
    {
        $expectedResult = [
            [
                'invoice_id' => 1,
                'product_id' => 1,
                'price'      => 12.22,
                'quantity'   => 2
            ]
        ];

        $container = new Container(['dbh' => self::$dbh]);
        $model     = new InvoiceProductsModel($container);

        $result = $model->collection([
            'invoice_id' => 1,
            'limit'      => 1,
            'offset'     => 1
        ]);

        $this->assertEquals($expectedResult, $result);
    }

    public function testCollectionOrderBy()
    {
        $expectedResult = [
            [
                'invoice_id' => 1,
                'product_id' => 1,
                'price'      => 12.22,
                'quantity'   => 2
            ],
            [
                'invoice_id' => 1,
                'product_id' => 2,
                'price'      => 1049.99,
                'quantity'   => 1
            ]
        ];

        $container = new Container(['dbh' => self::$dbh]);
        $model     = new InvoiceProductsModel($container);

        $result = $model->collection([
            'invoice_id' => 1,
            'sortField'  => 'quantity',
            'sortOrder'  => 'DESC'
        ]);

        $this->assertEquals($expectedResult, $result);
    }

    public function testAdd()
    {
        $expectedResult = 3;
        $container      = new Container(['dbh' => self::$dbh]);
        $model          = new InvoiceProductsModel($container);

        $result = $model->add([
            'invoice_id' => 1,
            'product_id' => 2,
            'price'      => 1049.99,
            'quantity'   => 3
        ]);

        $this->assertEquals($expectedResult, $result);
    }

    public function testUpdateExistingRow()
    {
        $expectedResult = true;
        $expectedRow    = [
            'invoice_id' => 1,
            'product_id' => 2,
            'price'      => '1049.99',
            'quantity'   => 3
        ];
        $container      = new Container(['dbh' => self::$dbh]);
        $model          = new InvoiceProductsModel($container);

        $result = $model->update(1, 2, [
            'price'    => 1049.99,
            'quantity' => 3
        ]);

        $this->assertEquals($expectedResult, $result);

        $stmt = self::$dbh->prepare(
            'SELECT invoice_id, product_id, price, quantity FROM invoice_products WHERE invoice_id = 1 AND product_id = 2'
        );

        $stmt->execute();

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        $this->assertEquals($expectedRow, $row);
    }

    public function testUpdateNonExistingRow()
    {
        $expectedResult = false;
        $container      = new Container(['dbh' => self::$dbh]);
        $model          = new InvoiceProductsModel($container);

        $result = $model->update(1, 5, [
            'price'    => 1049.99,
            'quantity' => 3
        ]);

        $this->assertEquals($expectedResult, $result);
    }

    public function testDeleteExistingRow()
    {
        $expectedResult = true;
        $expectedCount  = 1;
        $container      = new Container(['dbh' => self::$dbh]);
        $model          = new InvoiceProductsModel($container);

        $result = $model->delete(1, 1);

        $this->assertEquals($expectedResult, $result);

        $stmt = self::$dbh->prepare('SELECT COUNT(*) FROM invoice_products WHERE invoice_id = 1');

        $stmt->bindColumn(1, $count, \PDO::PARAM_INT);
        $stmt->execute();
        $stmt->fetch();

        $this->assertEquals($expectedCount, $count);
    }

    public function testDeleteNonExistingRow()
    {
        $expectedResult = false;
        $container      = new Container(['dbh' => self::$dbh]);
        $model          = new InvoiceProductsModel($container);

        $result = $model->delete(1, 5);

        $this->assertEquals($expectedResult, $result);
    }
}
