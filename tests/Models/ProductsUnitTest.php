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
use Tests\BaseTestCase;
use Tests\Mocks\Container\Container;
use Tests\Mocks\PDO\PDO;
use Tests\Mocks\PDO\PDOStatement;

class ProductsUnitTest extends BaseTestCase
{
    // get entity

    public function testGetWithBadIdParam()
    {
        $dbh       = new PDO();
        $container = new Container(['dbh' => $dbh]);
        $model     = new Products($container);

        $result0 = $model->get(0);
        $result1 = $model->get(-1);

        $this->assertEquals([], $result0);
        $this->assertEquals([], $result1);
    }

    public function testGetPrepareQuery()
    {
        $expectedQuery = $this->inlineSQLString('
          SELECT 
            id, name, tags, price, created_at, updated_at 
          FROM product
          WHERE id = ?;
        ');
        $dbh           = new PDO();
        $container     = new Container(['dbh' => $dbh]);
        $model         = new Products($container);

        $model->get(1);

        $params = $dbh->getPrepareParams(0);

        $this->assertEquals(1, $dbh->getPrepareCallCount());
        $this->assertEquals(2, count($params));

        $params[0] = $this->inlineSQLString($params[0]);

        $this->assertEquals([$expectedQuery, null], $params);
    }

    public function testGetBindValue()
    {
        $expectedParams = [1, 3942, \PDO::PARAM_INT];
        $stmt           = new PDOStatement();
        $dbh            = new PDO([
            'prepareReturn' => [$stmt]
        ]);
        $container      = new Container(['dbh' => $dbh]);
        $model          = new Products($container);

        $model->get(3942);

        $this->assertEquals(1, $stmt->getBindValueCallCount());
        $this->assertEquals($expectedParams, $stmt->getBindValueParams(0));
    }

    public function testGetExecute()
    {
        $expectedParams = [null];
        $stmt           = new PDOStatement();
        $dbh            = new PDO([
            'prepareReturn' => [$stmt]
        ]);
        $container      = new Container(['dbh' => $dbh]);
        $model          = new Products($container);

        $model->get(50);

        $this->assertEquals(1, $stmt->getExecuteCallCount());
        $this->assertEquals($expectedParams, $stmt->getExecuteParams(0));
    }

    public function testGetFetch()
    {
        $expectedParams = [\PDO::FETCH_ASSOC, \PDO::FETCH_ORI_NEXT, 0];
        $stmt           = new PDOStatement();
        $dbh            = new PDO([
            'prepareReturn' => [$stmt]
        ]);
        $container      = new Container(['dbh' => $dbh]);
        $model          = new Products($container);

        $model->get(5);

        $this->assertEquals(1, $stmt->getFetchCallCount());
        $this->assertEquals($expectedParams, $stmt->getFetchParams(0));
    }

    public function testGetFalsyResult()
    {
        $expectedResult = [];
        $stmt           = new PDOStatement([
            'fetchReturn' => [
                false
            ]
        ]);
        $dbh            = new PDO([
            'prepareReturn' => [$stmt]
        ]);
        $container      = new Container(['dbh' => $dbh]);
        $model          = new Products($container);

        $result = $model->get(5);

        $this->assertEquals($expectedResult, $result);
    }

    public function testGetTruthyResult()
    {
        $expectedResult = ['id' => 1, 'name' => 'Marvo CoolingPad', 'tags' => '[]', 'price' => 0.0];
        $stmt           = new PDOStatement([
            'fetchReturn' => [
                ['id' => 1, 'name' => 'Marvo CoolingPad']
            ]
        ]);
        $dbh            = new PDO([
            'prepareReturn' => [$stmt]
        ]);
        $container      = new Container(['dbh' => $dbh]);
        $model          = new Products($container);

        $result = $model->get(5);

        $this->assertEquals($expectedResult, $result);
    }

    public function testGetLetExceptionsBeThrown()
    {
        $dbh       = new PDO([
            'prepareThrowable' => [
                function () {
                    throw new \PDOException('');
                }
            ]
        ]);
        $container = new Container(['dbh' => $dbh]);
        $model     = new Products($container);

        $this->expectException(\PDOException::class);

        $model->get(1);
    }

    // count total amount

    public function testCountPrepareQuery()
    {
        $expectedQuery = $this->inlineSQLString('SELECT COUNT(*) FROM product;');
        $dbh           = new PDO();
        $container     = new Container(['dbh' => $dbh]);
        $model         = new Products($container);

        $model->count();

        $params = $dbh->getPrepareParams(0);

        $this->assertEquals(1, $dbh->getPrepareCallCount());
        $this->assertEquals(2, count($params));

        $params[0] = $this->inlineSQLString($params[0]);

        $this->assertEquals([$expectedQuery, null], $params);
    }

    public function testCountBindColumn()
    {
        $expectedParams = [1, 0, \PDO::PARAM_INT, null, null];
        $stmt           = new PDOStatement();
        $dbh            = new PDO([
            'prepareReturn' => [$stmt]
        ]);
        $container      = new Container(['dbh' => $dbh]);
        $model          = new Products($container);

        $model->count();

        $this->assertEquals(1, $stmt->getBindColumnCallCount());
        $this->assertEquals($expectedParams, $stmt->getBindColumnParams(0));
    }

    public function testCountExecute()
    {
        $expectedParams = [null];
        $stmt           = new PDOStatement();
        $dbh            = new PDO([
            'prepareReturn' => [$stmt]
        ]);
        $container      = new Container(['dbh' => $dbh]);
        $model          = new Products($container);

        $model->count();

        $this->assertEquals(1, $stmt->getExecuteCallCount());
        $this->assertEquals($expectedParams, $stmt->getExecuteParams(0));
    }

    public function testCountFetch()
    {
        $expectedParams = [null, \PDO::FETCH_ORI_NEXT, 0];
        $stmt           = new PDOStatement();
        $dbh            = new PDO([
            'prepareReturn' => [$stmt]
        ]);
        $container      = new Container(['dbh' => $dbh]);
        $model          = new Products($container);

        $model->count();

        $this->assertEquals(1, $stmt->getFetchCallCount());
        $this->assertEquals($expectedParams, $stmt->getFetchParams(0));
    }

    public function testCountFalsyResult()
    {
        $expectedResult = 0;
        $stmt           = new PDOStatement([
            'bindColumnReference' => [
                [0]
            ]
        ]);
        $dbh            = new PDO([
            'prepareReturn' => [$stmt]
        ]);
        $container      = new Container(['dbh' => $dbh]);
        $model          = new Products($container);

        $result = $model->count();

        $this->assertEquals($expectedResult, $result);
    }

    public function testCountTruthyResult()
    {
        $expectedResult = 76;
        $stmt           = new PDOStatement([
            'bindColumnReference' => [
                [76]
            ]
        ]);
        $dbh            = new PDO([
            'prepareReturn' => [$stmt]
        ]);
        $container      = new Container(['dbh' => $dbh]);
        $model          = new Products($container);

        $result = $model->count();

        $this->assertEquals($expectedResult, $result);
    }

    public function testCountLetExceptionsBeThrown()
    {
        $dbh       = new PDO([
            'prepareThrowable' => [
                function () {
                    throw new \PDOException('');
                }
            ]
        ]);
        $container = new Container(['dbh' => $dbh]);
        $model     = new Products($container);

        $this->expectException(\PDOException::class);

        $model->get(1);
    }

    // get collection

    public function testCollectionWithEmptyParams()
    {
        $expectedQuery = $this->inlineSQLString('SELECT id, name, tags, price, created_at, updated_at FROM product;');
        // PDO Expectations
        $dbh = new PDO();
        // DI Container
        $container = new Container(['dbh' => $dbh]);
        // class to test
        $model = new Products($container);

        // test data
        $model->collection();

        $params = $dbh->getPrepareParams(0);

        $this->assertEquals(2, count($params));

        $params[0] = $this->inlineSQLString($params[0]);

        $this->assertEquals(1, $dbh->getPrepareCallCount());
        $this->assertEquals([$expectedQuery, null], $params);
    }

    public function testCollectionWithLimit()
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
        $model->collection(['limit' => 5]);

        $params = $dbh->getPrepareParams(0);

        $this->assertEquals(2, count($params));

        $params[0] = $this->inlineSQLString($params[0]);

        $this->assertEquals(1, $dbh->getPrepareCallCount());
        $this->assertEquals([$expectedQuery, null], $params);
    }

    public function testCollectionWithLimitAndOffset()
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
        $model->collection([
            'limit'  => 5,
            'offset' => 20
        ]);

        $params = $dbh->getPrepareParams(0);

        $this->assertEquals(2, count($params));

        $params[0] = $this->inlineSQLString($params[0]);

        $this->assertEquals(1, $dbh->getPrepareCallCount());
        $this->assertEquals([$expectedQuery, null], $params);
    }

    public function testCollectionWithOffsetNoLimit()
    {
        $expectedQuery = $this->inlineSQLString('SELECT id, name, tags, price, created_at, updated_at FROM product;');
        // PDO Expectations
        $dbh = new PDO();
        // DI Container
        $container = new Container(['dbh' => $dbh]);
        // class to test
        $model = new Products($container);

        // test data
        $model->collection(['offset' => 20]);

        $params = $dbh->getPrepareParams(0);

        $this->assertEquals(2, count($params));

        $params[0] = $this->inlineSQLString($params[0]);

        $this->assertEquals(1, $dbh->getPrepareCallCount());
        $this->assertEquals([$expectedQuery, null], $params);
    }

    public function testCollectionWithSortFieldAndOrder()
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
        $model->collection([
            'sortField' => 'name',
            'sortOrder' => 'ASC',
        ]);
        $model->collection([
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

    public function testCollectionWithOnlyOneSortFieldOrOrder()
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
        $model->collection(['sortField' => 'name']);
        $model->collection(['sortOrder' => 'DESC']);

        $params0 = $dbh->getPrepareParams(0);
        $params1 = $dbh->getPrepareParams(1);

        $this->assertEquals(2, count($params0));
        $this->assertEquals(2, count($params1));

        $params0[0] = $this->inlineSQLString($params0[0]);
        $params1[0] = $this->inlineSQLString($params1[0]);

        $this->assertEquals(2, $dbh->getPrepareCallCount());
        $this->assertEquals([$expectedQueries[0], null], $params0);
        $this->assertEquals([$expectedQueries[1], null], $params1);
    }

    public function testCollectionCallExecuteStatement()
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

        $model->collection();

        $this->assertEquals(1, $stmt->getExecuteCallCount());
        $this->assertEquals([null], $stmt->getExecuteParams(0));
    }

    public function testCollectionCallFetchWithAssoc()
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
            ['id' => 1, 'tags' => '[]', 'price' => 0.0],
            ['id' => 2, 'tags' => '[]', 'price' => 0.0]
        ];

        $result = $model->collection();

        $this->assertEquals(3, $stmt->getFetchCallCount());
        $this->assertEquals([\PDO::FETCH_ASSOC, \PDO::FETCH_ORI_NEXT, 0], $stmt->getFetchParams(0));
        $this->assertEquals([\PDO::FETCH_ASSOC, \PDO::FETCH_ORI_NEXT, 0], $stmt->getFetchParams(1));
        $this->assertEquals([\PDO::FETCH_ASSOC, \PDO::FETCH_ORI_NEXT, 0], $stmt->getFetchParams(2));
        $this->assertEquals($expectedResult, $result);
    }

    public function testCollectionResult()
    {
        // PDOStatement Expectations
        $stmt = new PDOStatement([
            'fetchReturn' => [
                ['id' => 1, 'name' => 'MX-4 Thermal Compound', 'tags' => '["Thermal", "Computers"]'],
                ['id' => 2, 'name' => 'ArtiClean 1 & 2 30ml', 'tags' => '[]']
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
            ['id' => 1, 'name' => 'MX-4 Thermal Compound', 'tags' => '["Thermal", "Computers"]', 'price' => 0.0],
            ['id' => 2, 'name' => 'ArtiClean 1 & 2 30ml', 'tags' => '[]', 'price' => 0.0]
        ];

        $result = $model->collection();

        $this->assertEquals($expectedResult, $result);
    }

    public function testCollectionLetExceptionsBeThrown()
    {
        // PDO Expectations
        $dbh = new PDO([
            'prepareThrowable' => [
                function () {
                    throw new \PDOException('');
                }
            ]
        ]);
        // DI Container
        $container = new Container(['dbh' => $dbh]);
        // class to test
        $model = new Products($container);

        $this->expectException(\PDOException::class);

        $model->collection();
    }

    // --------------- add entity

    public function testAddPrepareQuery()
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

        $model->add([
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
        $this->assertEquals([$expectedQuery, null], $params);
    }

    public function testAddBindColumnParams()
    {
        // expectation
        $expectedParams = ['id', null, 1, null, null];
        $stmt           = new PDOStatement();
        $dbh            = new PDO([
            'prepareReturn' => [$stmt]
        ]);
        $container      = new Container(['dbh' => $dbh]);
        $model          = new Products($container);

        $model->add([
            'name'       => '',
            'tags'       => '',
            'price'      => 0,
            'created_at' => '',
            'updated_at' => ''
        ]);

        $this->assertEquals(1, $stmt->getBindColumnCallCount());
        $this->assertEquals($expectedParams, $stmt->getBindColumnParams(0));
    }

    public function testAddBindValueParams()
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

        $model->add([
            'name'       => 'MX-4 Thermal Compound',
            'tags'       => '["Computers", "CPU", "GPU"]',
            'price'      => 6.79,
            'created_at' => '2017-05-05T18:45:00Z',
            'updated_at' => '2017-05-05T18:45:00Z'
        ]);

        $this->assertEquals(5, $stmt->getBindValueCallCount());
        $this->assertEquals($expectedParams, $stmt->getBindValueParamsAll());
    }

    public function testAddResult()
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

        $result = $model->add([
            'name'       => '',
            'tags'       => '',
            'price'      => 0,
            'created_at' => '',
            'updated_at' => ''
        ]);

        $this->assertEquals(1, $result);
    }

    public function testAddCallsExecute()
    {
        $stmt      = new PDOStatement();
        $dbh       = new PDO([
            'prepareReturn' => [$stmt]
        ]);
        $container = new Container(['dbh' => $dbh]);
        $model     = new Products($container);

        $model->add([
            'name'       => '',
            'tags'       => '',
            'price'      => 0,
            'created_at' => '',
            'updated_at' => ''
        ]);

        $this->assertEquals(1, $stmt->getExecuteCallCount());
        $this->assertEquals([null], $stmt->getExecuteParams(0));
    }

    public function testAddCallsFetch()
    {
        $stmt      = new PDOStatement();
        $dbh       = new PDO([
            'prepareReturn' => [$stmt]
        ]);
        $container = new Container(['dbh' => $dbh]);
        $model     = new Products($container);

        $model->add([
            'name'       => '',
            'tags'       => '',
            'price'      => 0,
            'created_at' => '',
            'updated_at' => ''
        ]);

        $this->assertEquals(1, $stmt->getFetchCallCount());
        $this->assertEquals([null, \PDO::FETCH_ORI_NEXT, 0], $stmt->getFetchParams(0));
    }

    public function testAddLetExceptionsBeThrown()
    {
        $dbh       = new PDO([
            'prepareThrowable' => [
                function () {
                    throw new \PDOException('');
                }
            ]
        ]);
        $container = new Container(['dbh' => $dbh]);
        $model     = new Products($container);

        $this->expectException(\PDOException::class);

        $model->add([]);
    }

    // update entity

    public function testUpdateEarlyReturnOnBadIdParam()
    {
        $container = new Container([]);
        $model     = new Products($container);

        $result = $model->update(0, [
            'name' => ''
        ]);

        $this->assertEquals(false, $result);
    }

    public function testUpdateEarlyReturnOnBadDataParam()
    {
        $container = new Container([]);
        $model     = new Products($container);

        $result = $model->update(1, []);

        $this->assertEquals(false, $result);
    }

    public function testUpdateQuery()
    {
        $expectedParams = [
            $this->inlineSQLString('
              UPDATE product 
              SET name = ?, tags = ?, price = ?, updated_at = ?
              WHERE
              id = ?;
            '),
            null
        ];
        $dbh            = new PDO();
        $container      = new Container(['dbh' => $dbh]);
        $model          = new Products($container);

        $model->update(1, [
            'name'       => '',
            'tags'       => '',
            'price'      => 0,
            'updated_at' => ''
        ]);

        $params = $dbh->getPrepareParams(0);

        $this->assertEquals(2, count($params));

        $params[0] = $this->inlineSQLString($params[0]);

        $this->assertEquals(1, $dbh->getPrepareCallCount());
        $this->assertEquals($expectedParams, $params);
    }

    public function testUpdateBindParams()
    {
        $expectedParams = [
            [1, 'Acer Aspire VX15 ', \PDO::PARAM_STR],
            [2, '["Laptops", "Electronics", "Gaming"]', \PDO::PARAM_STR],
            [3, 1049.99, \PDO::PARAM_STR],
            [4, '2017-03-06T11:34:56Z', \PDO::PARAM_STR],
            [5, 18, \PDO::PARAM_INT],
        ];
        $stmt           = new PDOStatement();
        $dbh            = new PDO([
            'prepareReturn' => [$stmt]
        ]);
        $container      = new Container(['dbh' => $dbh]);
        $model          = new Products($container);

        $model->update(18, [
            'name'       => 'Acer Aspire VX15 ',
            'tags'       => '["Laptops", "Electronics", "Gaming"]',
            'price'      => 1049.99,
            'updated_at' => '2017-03-06T11:34:56Z'
        ]);

        $this->assertEquals(5, $stmt->getBindValueCallCount());
        $this->assertEquals($expectedParams, $stmt->getBindValueParamsAll());
    }

    public function testUpdateCallsExecute()
    {
        $expectedParams = [null];
        $stmt           = new PDOStatement();
        $dbh            = new PDO([
            'prepareReturn' => [$stmt]
        ]);
        $container      = new Container(['dbh' => $dbh]);
        $model          = new Products($container);

        $model->update(1, [
            'name'       => '',
            'tags'       => '',
            'price'      => 0,
            'updated_at' => ''
        ]);

        $this->assertEquals(1, $stmt->getExecuteCallCount());
        $this->assertEquals($expectedParams, $stmt->getExecuteParams(0));
    }

    public function testUpdateFalsyResultWhenNoRowsAffected()
    {
        $stmt      = new PDOStatement([
            'rowCountReturn' => [
                0
            ]
        ]);
        $dbh       = new PDO([
            'prepareReturn' => [$stmt]
        ]);
        $container = new Container(['dbh' => $dbh]);
        $model     = new Products($container);

        $result = $model->update(1, [
            'name'       => '',
            'tags'       => '',
            'price'      => 0,
            'updated_at' => ''
        ]);

        $this->assertEquals(false, $result);
    }

    public function testUpdateTruthyResultWhenRowsAffected()
    {
        $stmt      = new PDOStatement([
            'rowCountReturn' => [
                62
            ]
        ]);
        $dbh       = new PDO([
            'prepareReturn' => [$stmt]
        ]);
        $container = new Container(['dbh' => $dbh]);
        $model     = new Products($container);

        $result = $model->update(1, [
            'name'       => '',
            'tags'       => '',
            'price'      => 0,
            'updated_at' => ''
        ]);

        $this->assertEquals(true, $result);
    }

    public function testUpdateLetExceptionsBeThrown()
    {
        $dbh       = new PDO([
            'prepareThrowable' => [
                function () {
                    throw new \PDOException('');
                }
            ]
        ]);
        $container = new Container(['dbh' => $dbh]);
        $model     = new Products($container);

        $this->expectException(\PDOException::class);

        $model->update(1, ['name' => '']);
    }

    // delete entity

    public function testDeleteWithWrongIdParam()
    {
        $container = new Container([]);
        $model     = new Products($container);

        $result1 = $model->delete(0);
        $result2 = $model->delete(-32);

        $this->assertEquals(false, $result1);
        $this->assertEquals(false, $result2);
    }

    public function testDeletePrepareAndQuery()
    {
        $expectedQuery = $this->inlineSQLString('DELETE FROM product WHERE id = ?;');
        $dbh           = new PDO();
        $container     = new Container(['dbh' => $dbh]);
        $model         = new Products($container);

        $model->delete(1);

        $params = $dbh->getPrepareParams(0);

        $this->assertEquals(2, count($params));

        $params[0] = $this->inlineSQLString($params[0]);

        $this->assertEquals(1, $dbh->getPrepareCallCount());
        $this->assertEquals([$expectedQuery, null], $params);
    }

    public function testDeleteBindValue()
    {
        $expectedParams = [1, 1, \PDO::PARAM_INT];
        $stmt           = new PDOStatement();
        $dbh            = new PDO([
            'prepareReturn' => [$stmt]
        ]);
        $container      = new Container(['dbh' => $dbh]);
        $model          = new Products($container);

        $model->delete(1);

        $this->assertEquals(1, $stmt->getBindValueCallCount());
        $this->assertEquals($expectedParams, $stmt->getBindValueParams(0));
    }

    public function testDeleteExecute()
    {
        $expectedParams = [null];
        $stmt           = new PDOStatement();
        $dbh            = new PDO([
            'prepareReturn' => [$stmt]
        ]);
        $container      = new Container(['dbh' => $dbh]);
        $model          = new Products($container);

        $model->delete(1);

        $this->assertEquals(1, $stmt->getExecuteCallCount());
        $this->assertEquals($expectedParams, $stmt->getExecuteParams(0));
    }

    public function testDeleteRowCount()
    {
        $stmt      = new PDOStatement();
        $dbh       = new PDO([
            'prepareReturn' => [$stmt]
        ]);
        $container = new Container(['dbh' => $dbh]);
        $model     = new Products($container);

        $model->delete(1);

        $this->assertEquals(1, $stmt->getRowCountCallCount());
        $this->assertEquals([], $stmt->getRowCountParams(0));
    }

    public function testDeleteFalsyWhenNoRowsAffected()
    {
        $stmt      = new PDOStatement([
            'rowCountReturn' => [0]
        ]);
        $dbh       = new PDO([
            'prepareReturn' => [$stmt]
        ]);
        $container = new Container(['dbh' => $dbh]);
        $model     = new Products($container);

        $result = $model->delete(1);

        $this->assertFalse($result);
    }

    public function testDeleteTruthyWhenRowsAffected()
    {
        $stmt      = new PDOStatement([
            'rowCountReturn' => [1]
        ]);
        $dbh       = new PDO([
            'prepareReturn' => [$stmt]
        ]);
        $container = new Container(['dbh' => $dbh]);
        $model     = new Products($container);

        $result = $model->delete(1);

        $this->assertTrue($result);
    }

    public function testDeleteLetExceptionsBeThrown()
    {
        $dbh       = new PDO([
            'prepareThrowable' => [
                function () {
                    throw new \PDOException('');
                }
            ]
        ]);
        $container = new Container(['dbh' => $dbh]);
        $model     = new Products($container);

        $this->expectException(\PDOException::class);

        $model->delete(1);
    }
}
