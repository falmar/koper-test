<?php
declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: falmar
 * Date: 5/14/17
 * Time: 12:39 PM
 */

namespace Tests\Models;


use KoperTest\db\Product;
use KoperTest\Models\Products;
use Tests\Mocks\Container\Container;


class ProductsIntegrationTest extends BaseTestCase
{
    /** @var \PDO */
    protected static $dbh = null;
    /** @var Product */
    protected static $migration = null;

    public static function setUpBeforeClass()
    {
        self::$dbh       = self::getPDO();
        self::$migration = new Product(self::$dbh);
    }

    public function setUp()
    {
        self::$migration->up();
        self::$migration->seed();
    }

    public function tearDown()
    {
        // self::$migration->down();
    }

    public function testCount()
    {
        $expectedResult = 2;
        $container      = new Container(['dbh' => self::$dbh]);
        $model          = new Products($container);

        $result = $model->count();

        $this->assertEquals($expectedResult, $result);
    }

    public function testCountNoRows()
    {
        self::$dbh->exec('DELETE FROM product;');

        $expectedResult = 0;
        $container      = new Container(['dbh' => self::$dbh]);
        $model          = new Products($container);

        $result = $model->count();

        $this->assertEquals($expectedResult, $result);
    }

    public function testGetExistingRow()
    {
        $expectedResult = [
            'id'         => 1,
            'name'       => 'MX-4 Thermal Compound',
            'tags'       => '["Computers", "CPU", "Heat"]',
            'price'      => 6.59,
            'created_at' => '2017-05-15 14:00:00+00',
            'updated_at' => '2017-05-15 14:00:00+00'
        ];
        $container      = new Container(['dbh' => self::$dbh]);
        $model          = new Products($container);

        $result = $model->get(1);

        $this->assertEquals($expectedResult, $result);
    }

    public function testGetNonExistingRow()
    {
        $expectedResult = [];
        $container      = new Container(['dbh' => self::$dbh]);
        $model          = new Products($container);

        $result = $model->get(5);

        $this->assertEquals($expectedResult, $result);
    }

    public function testCollectionAll()
    {
        $expectedResult = [
            [
                'id'         => 1,
                'name'       => 'MX-4 Thermal Compound',
                'tags'       => [
                    'Computers',
                    'CPU',
                    'Heat'
                ],
                'images'     => [],
                'price'      => 6.59,
                'created_at' => '2017-05-15 14:00:00+00',
                'updated_at' => '2017-05-15 14:00:00+00'
            ],
            [
                'id'         => 2,
                'name'       => 'Acer Aspire VX15',
                'tags'       => [
                    'Computers'
                ],
                'images'     => [],
                'price'      => 1049.99,
                'created_at' => '2017-05-15 15:00:00+00',
                'updated_at' => '2017-05-15 15:00:00+00'
            ]
        ];

        $container = new Container(['dbh' => self::$dbh]);
        $model     = new Products($container);

        $result = $model->collection();

        $this->assertEquals($expectedResult, $result);
    }

    public function testCollectionLimit()
    {
        $expectedResult = [
            [
                'id'         => 1,
                'name'       => 'MX-4 Thermal Compound',
                'tags'       => [
                    'Computers',
                    'CPU',
                    'Heat'
                ],
                'images'     => [],
                'price'      => 6.59,
                'created_at' => '2017-05-15 14:00:00+00',
                'updated_at' => '2017-05-15 14:00:00+00'
            ]
        ];

        $container = new Container(['dbh' => self::$dbh]);
        $model     = new Products($container);

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
                'name'       => 'Acer Aspire VX15',
                'tags'       => [
                    'Computers'
                ],
                'images'     => [],
                'price'      => 1049.99,
                'created_at' => '2017-05-15 15:00:00+00',
                'updated_at' => '2017-05-15 15:00:00+00'
            ]
        ];

        $container = new Container(['dbh' => self::$dbh]);
        $model     = new Products($container);

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
                'name'       => 'Acer Aspire VX15',
                'tags'       => [
                    'Computers'
                ],
                'images'     => [],
                'price'      => 1049.99,
                'created_at' => '2017-05-15 15:00:00+00',
                'updated_at' => '2017-05-15 15:00:00+00'
            ],
            [
                'id'         => 1,
                'name'       => 'MX-4 Thermal Compound',
                'tags'       => [
                    'Computers',
                    'CPU',
                    'Heat'
                ],
                'images'     => [],
                'price'      => 6.59,
                'created_at' => '2017-05-15 14:00:00+00',
                'updated_at' => '2017-05-15 14:00:00+00'
            ]
        ];

        $container = new Container(['dbh' => self::$dbh]);
        $model     = new Products($container);

        $result = $model->collection([
            'sortField' => 'updated_at',
            'sortOrder' => 'DESC'
        ]);

        $this->assertEquals($expectedResult, $result);
    }

    public function testAdd()
    {
        $expectedResult = 3;
        $container      = new Container(['dbh' => self::$dbh]);
        $model          = new Products($container);

        $result = $model->add([
            'name'       => 'Marvo CoolingPad',
            'tags'       => '[]',
            'price'      => 13.78,
            'created_at' => '2017-05-15 14:00:00+00',
            'updated_at' => '2017-05-15 14:00:00+00'
        ]);

        $this->assertEquals($expectedResult, $result);
    }

    public function testUpdateExistingRow()
    {
        $expectedResult = true;
        $expectedRow    = [
            'id'         => 1,
            'name'       => 'Marvo CoolingPad',
            'tags'       => '["Electronics"]',
            'price'      => 16.0,
            'created_at' => '2017-05-15 14:00:00+00',
            'updated_at' => '2017-05-16 14:00:00+00'
        ];
        $container      = new Container(['dbh' => self::$dbh]);
        $model          = new Products($container);

        $result = $model->update(1, [
            'name'       => 'Marvo CoolingPad',
            'tags'       => '["Electronics"]',
            'price'      => 16,
            'updated_at' => '2017-05-16T14:00:00Z'
        ]);

        $this->assertEquals($expectedResult, $result);

        $stmt = self::$dbh->prepare('SELECT id, name, tags, price, created_at, updated_at FROM product WHERE id = 1');

        $stmt->execute();

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        $this->assertEquals($expectedRow, $row);
    }

    public function testUpdateNonExistingRow()
    {
        $expectedResult = false;
        $container      = new Container(['dbh' => self::$dbh]);
        $model          = new Products($container);

        $result = $model->update(5, [
            'name'       => 'Marvo CoolingPad',
            'tags'       => '["Electronics"]',
            'price'      => 16,
            'updated_at' => '2017-05-16T14:00:00Z'
        ]);

        $this->assertEquals($expectedResult, $result);
    }

    public function testDeleteExistingRow()
    {
        $expectedResult = true;
        $expectedCount  = 1;
        $container      = new Container(['dbh' => self::$dbh]);
        $model          = new Products($container);

        $result = $model->delete(1);

        $this->assertEquals($expectedResult, $result);

        $stmt = self::$dbh->prepare('SELECT COUNT(*) FROM product');

        $stmt->bindColumn(1, $count, \PDO::PARAM_INT);
        $stmt->execute();
        $stmt->fetch();

        $this->assertEquals($expectedCount, $count);
    }

    public function testDeleteNonExistingRow()
    {
        $expectedResult = false;
        $container      = new Container(['dbh' => self::$dbh]);
        $model          = new Products($container);

        $result = $model->delete(5);

        $this->assertEquals($expectedResult, $result);
    }
}
