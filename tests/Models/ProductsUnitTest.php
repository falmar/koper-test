<?php
declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: falmar
 * Date: 5/10/17
 * Time: 5:36 PM
 */

namespace Tests\Models;

use KoperTest\Models\Products;
use Tests\Mocks\Container\Container;
use Tests\Mocks\PDO\PDO;
use Tests\Mocks\PDO\PDOStatement;

class ProductsUnitTest extends BaseTestCase
{
    public function testGetListWithEmptyParams()
    {
        $expectedQuery = $this->inlineSQLString('SELECT id, name, tags, price, created_at, updated_at FROM product;');
        // PDO Expectations
        $dbh = new PDO();
        // DI Container
        $container = new Container(['dbh' => $dbh]);
        // class to test
        $model = new Products($container);

        // test data
        $model->getList();

        $params    = $dbh->getPrepareParams(0);
        $params[0] = $this->inlineSQLString($params[0]);

        $this->assertEquals(1, $dbh->getPrepareCallCount());
        $this->assertEquals([$expectedQuery, null], [$params[0], $params[1]]);
    }

    public function testGetListWithLimit()
    {
        $expectedQuery = $this->inlineSQLString(
            'SELECT id, name, tags, price, created_at, updated_at FROM product LIMIT 5;'
        );
        // PDO Expectations
        $dbh = new PDO();
        // DI Container
        $container = new Container(['dbh' => $dbh]);

        // class to test
        $model = new Products($container);

        // test data
        $model->getList(['limit' => 5]);

        $params    = $dbh->getPrepareParams(0);
        $params[0] = $this->inlineSQLString($params[0]);

        $this->assertEquals(1, $dbh->getPrepareCallCount());
        $this->assertEquals([$expectedQuery, null], [$params[0], $params[1]]);
    }

    public function testGetListWithLimitAndOffset()
    {
        $expectedQuery = $this->inlineSQLString(
            'SELECT id, name, tags, price, created_at, updated_at FROM product LIMIT 5 OFFSET 20;'
        );
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

        $params    = $dbh->getPrepareParams(0);
        $params[0] = $this->inlineSQLString($params[0]);

        $this->assertEquals(1, $dbh->getPrepareCallCount());
        $this->assertEquals([$expectedQuery, null], [$params[0], $params[1]]);
    }

    public function testGetListWithOffsetNoLimit()
    {
        $expectedQuery = $this->inlineSQLString('SELECT id, name, tags, price, created_at, updated_at FROM product;');
        // PDO Expectations
        $dbh = new PDO();
        // DI Container
        $container = new Container(['dbh' => $dbh]);
        // class to test
        $model = new Products($container);

        // test data
        $model->getList(['offset' => 20]);

        $params    = $dbh->getPrepareParams(0);
        $params[0] = $this->inlineSQLString($params[0]);

        $this->assertEquals(1, $dbh->getPrepareCallCount());
        $this->assertEquals([$expectedQuery, null], [$params[0], $params[1]]);
    }

    public function testGetListWithSortFieldAndOrder()
    {
        $expectedQueries = [
            $this->inlineSQLString(
                'SELECT id, name, tags, price, created_at, updated_at FROM product ORDER BY name ASC;'
            ),
            $this->inlineSQLString(
                'SELECT id, name, tags, price, created_at, updated_at FROM product ORDER BY id DESC;'
            )
        ];
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
        $model->getList([
            'sortField' => 'id',
            'sortOrder' => 'DESC',
        ]);

        $params0    = $dbh->getPrepareParams(0);
        $params1    = $dbh->getPrepareParams(1);
        $params0[0] = $this->inlineSQLString($params0[0]);
        $params1[0] = $this->inlineSQLString($params1[0]);

        $this->assertEquals(2, $dbh->getPrepareCallCount());
        $this->assertEquals([$expectedQueries[0], null], [$params0[0], $params0[1]]);
        $this->assertEquals([$expectedQueries[1], null], [$params1[0], $params1[1]]);
    }

    public function testGetListWithOnlyOneSortFieldOrOrder()
    {
        $expectedQueries = [
            $this->inlineSQLString(
                'SELECT id, name, tags, price, created_at, updated_at FROM product;'
            ),
            $this->inlineSQLString(
                'SELECT id, name, tags, price, created_at, updated_at FROM product;'
            )
        ];
        // PDO Expectations
        $dbh = new PDO();
        // DI Container
        $container = new Container(['dbh' => $dbh]);

        // class to test
        $model = new Products($container);

        // test data
        $model->getList(['sortField' => 'name']);
        $model->getList(['sortOrder' => 'DESC']);

        $params0    = $dbh->getPrepareParams(0);
        $params1    = $dbh->getPrepareParams(1);
        $params0[0] = $this->inlineSQLString($params0[0]);
        $params1[0] = $this->inlineSQLString($params1[0]);

        $this->assertEquals(2, $dbh->getPrepareCallCount());
        $this->assertEquals([$expectedQueries[0], null], [$params0[0], $params0[1]]);
        $this->assertEquals([$expectedQueries[1], null], [$params1[0], $params1[1]]);
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
