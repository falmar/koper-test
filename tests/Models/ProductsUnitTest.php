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

        $params = $dbh->getPrepareParams(0);

        $this->assertEquals(2, count($params));

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

        $params = $dbh->getPrepareParams(0);

        $this->assertEquals(2, count($params));

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

        $params = $dbh->getPrepareParams(0);

        $this->assertEquals(2, count($params));

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

        $params = $dbh->getPrepareParams(0);

        $this->assertEquals(2, count($params));

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

        $params0 = $dbh->getPrepareParams(0);
        $params1 = $dbh->getPrepareParams(1);

        $this->assertEquals(2, count($params0));
        $this->assertEquals(2, count($params1));

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

        $params0 = $dbh->getPrepareParams(0);
        $params1 = $dbh->getPrepareParams(1);

        $this->assertEquals(2, count($params0));
        $this->assertEquals(2, count($params1));

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

    // --------------- new entity

    public function testNewPrepareQuery()
    {
        $expectedQuery = $this->inlineSQLString('
          INSERT INTO product
          (name, tags, price, created_at, updated_at) 
          VALUES
          (?, ?, ?, ?, ?)
          RETURNING id;
        ');
        // PDO Expectations
        $dbh = new PDO();
        // DI Container
        $container = new Container(['dbh' => $dbh]);
        // model
        $model = new Products($container);

        $model->newProduct([
            'name'       => '',
            'tags'       => '',
            'price'      => 0,
            'created_at' => '',
            'updated_at' => ''
        ]);

        $params = $dbh->getPrepareParams(0);

        $this->assertEquals(2, count($params));

        $params[0] = $this->inlineSQLString($params[0]);

        $this->assertEquals(1, $dbh->getPrepareCallCount());
        $this->assertEquals([$expectedQuery, null], [$params[0], $params[1]]);
    }

    public function testNewBindColumnParams()
    {
        // expectation
        $expectedParams = ['id', null, null, null, null];
        $stmt           = new PDOStatement();
        $dbh            = new PDO([
            'prepareReturn' => [$stmt]
        ]);
        $container      = new Container(['dbh' => $dbh]);
        $model          = new Products($container);

        $model->newProduct([
            'name'       => '',
            'tags'       => '',
            'price'      => 0,
            'created_at' => '',
            'updated_at' => ''
        ]);

        $this->assertEquals(1, $stmt->getBindColumnCallCount());
        $this->assertEquals($expectedParams, $stmt->getBindColumnParams(0));
    }

    public function testNewBindValueParams()
    {
        // expectation
        $expectedParams = [
            [1, 'MX-4 Thermal Compound', \PDO::PARAM_STR],
            [2, '["Computers", "CPU", "GPU"]', \PDO::PARAM_STR],
            [3, 6.79, \PDO::PARAM_STR],
            [4, '2017-05-05T18:45:00Z', \PDO::PARAM_STR],
            [5, '2017-05-05T18:45:00Z', \PDO::PARAM_STR]
        ];
        // PDOStatement Expectations
        $stmt = new PDOStatement();
        // PDO Expectations
        $dbh = new PDO([
            'prepareReturn' => [$stmt]
        ]);
        // DI Container
        $container = new Container(['dbh' => $dbh]);
        // model
        $model = new Products($container);

        $model->newProduct([
            'name'       => 'MX-4 Thermal Compound',
            'tags'       => '["Computers", "CPU", "GPU"]',
            'price'      => 6.79,
            'created_at' => '2017-05-05T18:45:00Z',
            'updated_at' => '2017-05-05T18:45:00Z'
        ]);

        $this->assertEquals(5, $stmt->getBindValueCallCount());
        $this->assertEquals($expectedParams, $stmt->getBindValueParamsAll());
    }

    public function testNewResult()
    {
        $stmt      = new PDOStatement([
            'bindColumnReference' => [
                [1]
            ]
        ]);
        $dbh       = new PDO([
            'prepareReturn' => [$stmt]
        ]);
        $container = new Container(['dbh' => $dbh]);
        $model     = new Products($container);

        $result = $model->newProduct([
            'name'       => '',
            'tags'       => '',
            'price'      => 0,
            'created_at' => '',
            'updated_at' => ''
        ]);

        $this->assertEquals(1, $result);
    }

    public function testNewCallsExecute()
    {
        $stmt      = new PDOStatement();
        $dbh       = new PDO([
            'prepareReturn' => [$stmt]
        ]);
        $container = new Container(['dbh' => $dbh]);
        $model     = new Products($container);

        $model->newProduct([
            'name'       => '',
            'tags'       => '',
            'price'      => 0,
            'created_at' => '',
            'updated_at' => ''
        ]);

        $this->assertEquals(1, $stmt->getExecuteCallCount());
        $this->assertEquals([null], $stmt->getExecuteParams(0));
    }

    public function testNewCallsFetch()
    {
        $stmt      = new PDOStatement();
        $dbh       = new PDO([
            'prepareReturn' => [$stmt]
        ]);
        $container = new Container(['dbh' => $dbh]);
        $model     = new Products($container);

        $model->newProduct([
            'name'       => '',
            'tags'       => '',
            'price'      => 0,
            'created_at' => '',
            'updated_at' => ''
        ]);

        $this->assertEquals(1, $stmt->getFetchCallCount());
        $this->assertEquals([null, \PDO::FETCH_ORI_NEXT, 0], $stmt->getFetchParams(0));
    }
}
