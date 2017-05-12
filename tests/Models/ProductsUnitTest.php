<?php
declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: falmar
 * Date: 5/10/17
 * Time: 5:36 PM
 */

namespace Tests\Models;

use KoperTest\Mocks\Container\Container;
use KoperTest\Mocks\PDO\PDO;
use KoperTest\Mocks\PDO\PDOStatement;
use KoperTest\Models\Products;

class ProductsUnitTest extends \PHPUnit_Framework_TestCase
{
    public function testGetListWithEmptyParams()
    {
        // PDO Expectations
        $dbh = new PDO();
        // DI Container
        $container = new Container(['dbh' => $dbh]);
        // class to test
        $model = new Products($container);

        // test data
        $model->getList();

        $this->assertEquals(1, $dbh->getPrepareCallCount());
        $this->assertEquals(
            [
                'SELECT id, name, tags, price, created_at, updated_at FROM product;',
                null
            ], $dbh->getPrepareParams(0)
        );
    }

    public function testGetListWithLimit()
    {
        // PDO Expectations
        $dbh = new PDO();
        // DI Container
        $container = new Container(['dbh' => $dbh]);

        // class to test
        $model = new Products($container);

        // test data
        $model->getList(['limit' => 5]);

        $this->assertEquals(1, $dbh->getPrepareCallCount());
        $this->assertEquals(
            ['SELECT id, name, tags, price, created_at, updated_at FROM product LIMIT 5;', null],
            $dbh->getPrepareParams(0)
        );
    }

    public function testGetListWithLimitAndOffset()
    {
        // PDO Expectations
        $dbh = new PDO();
        // DI Container
        $container = new Container(['dbh' => $dbh]);

        // class to test
        $model = new Products($container);

        // test data
        $model->getList([
            'limit'  => 5,
            'offset' => 20
        ]);

        $this->assertEquals(1, $dbh->getPrepareCallCount());
        $this->assertEquals(
            [
                'SELECT id, name, tags, price, created_at, updated_at FROM product LIMIT 5 OFFSET 20;',
                null
            ], $dbh->getPrepareParams(0)
        );
    }

    public function testGetListWithOffsetNoLimit()
    {
        // PDO Expectations
        $dbh = new PDO();
        // DI Container
        $container = new Container(['dbh' => $dbh]);

        // class to test
        $model = new Products($container);

        // test data
        $model->getList(['offset' => 20]);

        $this->assertEquals(1, $dbh->getPrepareCallCount());
        $this->assertEquals(
            ['SELECT id, name, tags, price, created_at, updated_at FROM product;', null],
            $dbh->getPrepareParams(0)
        );
    }

    public function testGetListWithSortFieldAndOrder()
    {
        // PDO Expectations
        $dbh = new PDO();
        // DI Container
        $container = new Container(['dbh' => $dbh]);
        // class to test
        $model = new Products($container);

        // test data
        $model->getList([
            'sortField' => 'name',
            'sortOrder' => 'ASC',
        ]);

        // test data
        $model->getList([
            'sortField' => 'id',
            'sortOrder' => 'DESC',
        ]);

        $this->assertEquals(2, $dbh->getPrepareCallCount());
        $this->assertEquals(
            [
                'SELECT id, name, tags, price, created_at, updated_at FROM product ORDER BY name ASC;',
                null
            ], $dbh->getPrepareParams(0)
        );
        $this->assertEquals(
            [
                'SELECT id, name, tags, price, created_at, updated_at FROM product ORDER BY id DESC;',
                null
            ], $dbh->getPrepareParams(1)
        );
    }

    public function testGetListWithOnlyOneSortFieldOrOrder()
    {
        // PDO Expectations
        $dbh = new PDO();
        // DI Container
        $container = new Container(['dbh' => $dbh]);

        // class to test
        $model = new Products($container);

        // test data
        $model->getList(['sortField' => 'name']);
        $model->getList(['sortOrder' => 'DESC']);

        $this->assertEquals(2, $dbh->getPrepareCallCount());
        $this->assertEquals(
            ['SELECT id, name, tags, price, created_at, updated_at FROM product;', null],
            $dbh->getPrepareParams(0)
        );
        $this->assertEquals(
            ['SELECT id, name, tags, price, created_at, updated_at FROM product;', null],
            $dbh->getPrepareParams(1)
        );
    }

    public function testGetListCallExecuteStatement()
    {
        // PDOStatement Expectations
        $stmt = new PDOStatement();
        // PDO Expectations
        $dbh = new PDO([
            'prepareReturn' => [$stmt]
        ]);
        // DI Container
        $container = new Container(['dbh' => $dbh]);
        // class to test
        $model = new Products($container);

        $model->getList();

        $this->assertEquals(1, $stmt->getExecuteCallCount());
        $this->assertEquals([null], $stmt->getExecuteParams(0));
    }

    public function testGetListCallFetchWidthAssoc()
    {
        // PDOStatement Expectations
        $stmt = new PDOStatement([
            'fetchReturn' => [
                ['id' => 1],
                ['id' => 2],
                false
            ]
        ]);
        // PDO Expectations
        $dbh = new PDO([
            'prepareReturn' => [$stmt]
        ]);
        // DI Container
        $container = new Container(['dbh' => $dbh]);
        // class to test
        $model          = new Products($container);
        $expectedResult = [
            ['id' => 1, 'tags' => [], 'images' => []],
            ['id' => 2, 'tags' => [], 'images' => []],
        ];

        $result = $model->getList();

        $this->assertEquals(3, $stmt->getFetchCallCount());
        $this->assertEquals([\PDO::FETCH_ASSOC, \PDO::FETCH_ORI_NEXT, 0], $stmt->getFetchParams(0));
        $this->assertEquals([\PDO::FETCH_ASSOC, \PDO::FETCH_ORI_NEXT, 0], $stmt->getFetchParams(1));
        $this->assertEquals([\PDO::FETCH_ASSOC, \PDO::FETCH_ORI_NEXT, 0], $stmt->getFetchParams(2));
        $this->assertEquals($expectedResult, $result);
    }

    public function testGetListResult()
    {
        // PDOStatement Expectations
        $stmt = new PDOStatement([
            'fetchReturn' => [
                ['id' => 1, 'name' => 'MX-4 Thermal Compound', 'tags' => '["Thermal", "Computers"]'],
                ['id' => 2, 'name' => 'ArtiClean 1 & 2 30ml']
            ]
        ]);
        // PDO Expectations
        $dbh = new PDO([
            'prepareReturn' => [$stmt]
        ]);
        // DI Container
        $container = new Container(['dbh' => $dbh]);
        // class to test
        $model = new Products($container);

        $expectedResult = [
            ['id' => 1, 'name' => 'MX-4 Thermal Compound', 'tags' => ['Thermal', 'Computers'], 'images' => []],
            ['id' => 2, 'name' => 'ArtiClean 1 & 2 30ml', 'tags' => [], 'images' => []]
        ];

        $result = $model->getList();

        $this->assertEquals($expectedResult, $result);
    }
}
