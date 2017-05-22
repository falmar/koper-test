<?php
declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: falmar
 * Date: 5/10/17
 * Time: 5:36 PM
 */

namespace Tests\Models;

use KoperTest\Models\InvoicesModel;
use Tests\BaseTestCase;
use Tests\Mocks\Container\Container;
use Tests\Mocks\PDO\PDO;
use Tests\Mocks\PDO\PDOStatement;

class InvoicesUnitTest extends BaseTestCase
{
    // get entity
    public function testGetWithBadIdParam()
    {
        $dbh       = new PDO();
        $container = new Container(['dbh' => $dbh]);
        $model     = new InvoicesModel($container);

        $result0 = $model->get(0);
        $result1 = $model->get(-1);

        $this->assertEquals([], $result0);
        $this->assertEquals([], $result1);
    }

    public function testGetPrepareQuery()
    {
        $expectedQuery = $this->inlineSQLString('
          SELECT 
            id, code, status, customer, discount, tax, total , created_at, updated_at
          FROM invoice
          WHERE id = ?;
        ');
        $dbh           = new PDO();
        $container     = new Container(['dbh' => $dbh]);
        $model         = new InvoicesModel($container);

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
        $model          = new InvoicesModel($container);

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
        $model          = new InvoicesModel($container);

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
        $model          = new InvoicesModel($container);

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
        $model          = new InvoicesModel($container);

        $result = $model->get(5);

        $this->assertEquals($expectedResult, $result);
    }

    public function testGetTruthyResult()
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
        $stmt           = new PDOStatement([
            'fetchReturn' => [
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
            ]
        ]);
        $dbh            = new PDO([
            'prepareReturn' => [$stmt]
        ]);
        $container      = new Container(['dbh' => $dbh]);
        $model          = new InvoicesModel($container);

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
        $model     = new InvoicesModel($container);

        $this->expectException(\PDOException::class);

        $model->get(1);
    }

    // count total amount

    public function testCountPrepareQuery()
    {
        $expectedQuery = $this->inlineSQLString('SELECT COUNT(*) FROM invoice;');
        $dbh           = new PDO();
        $container     = new Container(['dbh' => $dbh]);
        $model         = new InvoicesModel($container);

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
        $model          = new InvoicesModel($container);

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
        $model          = new InvoicesModel($container);

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
        $model          = new InvoicesModel($container);

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
        $model          = new InvoicesModel($container);

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
        $model          = new InvoicesModel($container);

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
        $model     = new InvoicesModel($container);

        $this->expectException(\PDOException::class);

        $model->get(1);
    }

    // get collection

    public function testCollectionWithEmptyParams()
    {
        $expectedQuery = $this->inlineSQLString(
            'SELECT id, code, status, customer, discount, tax, total , created_at, updated_at FROM invoice LIMIT 25;'
        );
        // PDO Expectations
        $dbh = new PDO();
        // DI Container
        $container = new Container(['dbh' => $dbh]);
        // class to test
        $model = new InvoicesModel($container);

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
            'SELECT id, code, status, customer, discount, tax, total , created_at, updated_at FROM invoice LIMIT 5;'
        );
        // PDO Expectations
        $dbh = new PDO();
        // DI Container
        $container = new Container(['dbh' => $dbh]);

        // class to test
        $model = new InvoicesModel($container);

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
            'SELECT id, code, status, customer, discount, tax, total , created_at, updated_at FROM invoice LIMIT 5 OFFSET 20;'
        );
        // PDO Expectations
        $dbh = new PDO();
        // DI Container
        $container = new Container(['dbh' => $dbh]);
        // class to test
        $model = new InvoicesModel($container);

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
        $expectedQuery = $this->inlineSQLString(
            'SELECT id, code, status, customer, discount, tax, total , created_at, updated_at FROM invoice LIMIT 25 OFFSET 20;'
        );
        // PDO Expectations
        $dbh = new PDO();
        // DI Container
        $container = new Container(['dbh' => $dbh]);
        // class to test
        $model = new InvoicesModel($container);

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
                'SELECT id, code, status, customer, discount, tax, total , created_at, updated_at FROM invoice ORDER BY name ASC LIMIT 25;'
            ),
            $this->inlineSQLString(
                'SELECT id, code, status, customer, discount, tax, total , created_at, updated_at FROM invoice ORDER BY id DESC LIMIT 25;'
            )
        ];
        // PDO Expectations
        $dbh = new PDO();
        // DI Container
        $container = new Container(['dbh' => $dbh]);
        // class to test
        $model = new InvoicesModel($container);

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
                'SELECT id, code, status, customer, discount, tax, total , created_at, updated_at FROM invoice LIMIT 25;'
            ),
            $this->inlineSQLString(
                'SELECT id, code, status, customer, discount, tax, total , created_at, updated_at FROM invoice LIMIT 25;'
            )
        ];
        // PDO Expectations
        $dbh = new PDO();
        // DI Container
        $container = new Container(['dbh' => $dbh]);

        // class to test
        $model = new InvoicesModel($container);

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
        $model = new InvoicesModel($container);

        $model->collection();

        $this->assertEquals(1, $stmt->getExecuteCallCount());
        $this->assertEquals([null], $stmt->getExecuteParams(0));
    }

    public function testCollectionCallFetchWithAssoc()
    {
        // PDOStatement Expectations
        $stmt = new PDOStatement([
            'fetchReturn' => [
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
                    'status'     => 'PAID',
                    'customer'   => 'David',
                    'discount'   => 0,
                    'tax'        => 0,
                    'total'      => 36.67,
                    'created_at' => '2017-05-14 17:00:00+00',
                    'updated_at' => '2017-05-14 17:00:00+00',
                ],
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
        $model          = new InvoicesModel($container);
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
                'status'     => 'PAID',
                'customer'   => 'David',
                'discount'   => 0,
                'tax'        => 0,
                'total'      => 36.67,
                'created_at' => '2017-05-14 17:00:00+00',
                'updated_at' => '2017-05-14 17:00:00+00',
            ]
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
                    'status'     => 'PAID',
                    'customer'   => 'David',
                    'discount'   => 0,
                    'tax'        => 0,
                    'total'      => 36.67,
                    'created_at' => '2017-05-14 17:00:00+00',
                    'updated_at' => '2017-05-14 17:00:00+00',
                ],
            ]
        ]);
        // PDO Expectations
        $dbh = new PDO([
            'prepareReturn' => [$stmt]
        ]);
        // DI Container
        $container = new Container(['dbh' => $dbh]);
        // class to test
        $model = new InvoicesModel($container);

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
                'status'     => 'PAID',
                'customer'   => 'David',
                'discount'   => 0,
                'tax'        => 0,
                'total'      => 36.67,
                'created_at' => '2017-05-14 17:00:00+00',
                'updated_at' => '2017-05-14 17:00:00+00',
            ],
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
        $model = new InvoicesModel($container);

        $this->expectException(\PDOException::class);

        $model->collection();
    }

    // --------------- add entity

    public function testAddPrepareQuery()
    {
        $expectedQuery = $this->inlineSQLString('
          INSERT INTO invoice
          (code, status, customer, discount, tax, total , created_at, updated_at) 
          VALUES
          (?, ?, ?, ?, ?, ?, ?, ?)
          RETURNING id;
        ');
        // PDO Expectations
        $dbh = new PDO();
        // DI Container
        $container = new Container(['dbh' => $dbh]);
        // model
        $model = new InvoicesModel($container);

        $model->add([
            'code'       => '',
            'status'     => '',
            'customer'   => '',
            'discount'   => 0,
            'tax'        => 0,
            'total'      => 0,
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
        $model          = new InvoicesModel($container);

        $model->add([
            'code'       => '',
            'status'     => '',
            'customer'   => '',
            'discount'   => 0,
            'tax'        => 0,
            'total'      => 0,
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
            [1, 'IV002', \PDO::PARAM_STR],
            [2, 'PAID', \PDO::PARAM_STR],
            [3, 'David', \PDO::PARAM_STR],
            [4, 0, \PDO::PARAM_STR],
            [5, 0, \PDO::PARAM_STR],
            [6, 36.67, \PDO::PARAM_STR],
            [7, '2017-05-14 17:00:00+00', \PDO::PARAM_STR],
            [8, '2017-05-14 17:00:00+00', \PDO::PARAM_STR]
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
        $model = new InvoicesModel($container);

        $model->add([
            'code'       => 'IV002',
            'status'     => 'PAID',
            'customer'   => 'David',
            'discount'   => 0,
            'tax'        => 0,
            'total'      => 36.67,
            'created_at' => '2017-05-14 17:00:00+00',
            'updated_at' => '2017-05-14 17:00:00+00',
        ]);

        $this->assertEquals(8, $stmt->getBindValueCallCount());
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
        $model     = new InvoicesModel($container);

        $result = $model->add([
            'code'       => '',
            'status'     => '',
            'customer'   => '',
            'discount'   => 0,
            'tax'        => 0,
            'total'      => 0,
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
        $model     = new InvoicesModel($container);

        $model->add([
            'code'       => '',
            'status'     => '',
            'customer'   => '',
            'discount'   => 0,
            'tax'        => 0,
            'total'      => 0,
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
        $model     = new InvoicesModel($container);

        $model->add([
            'code'       => '',
            'status'     => '',
            'customer'   => '',
            'discount'   => 0,
            'tax'        => 0,
            'total'      => 0,
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
        $model     = new InvoicesModel($container);

        $this->expectException(\PDOException::class);

        $model->add([]);
    }

    // update entity

    public function testUpdateEarlyReturnOnBadIdParam()
    {
        $container = new Container([]);
        $model     = new InvoicesModel($container);

        $result = $model->update(0, [
            'name' => ''
        ]);

        $this->assertEquals(false, $result);
    }

    public function testUpdateEarlyReturnOnBadDataParam()
    {
        $container = new Container([]);
        $model     = new InvoicesModel($container);

        $result = $model->update(1, []);

        $this->assertEquals(false, $result);
    }

    public function testUpdateQuery()
    {
        $expectedParams = [
            $this->inlineSQLString('
              UPDATE invoice 
              SET code = ?, status = ?, customer = ?, discount = ?, tax = ?, total = ?, updated_at = ?
              WHERE
              id = ?;
            '),
            null
        ];
        $dbh            = new PDO();
        $container      = new Container(['dbh' => $dbh]);
        $model          = new InvoicesModel($container);

        $model->update(1, [
            'code'       => '',
            'status'     => '',
            'customer'   => '',
            'discount'   => 0,
            'tax'        => 0,
            'total'      => 0,
            'created_at' => '',
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
            [1, 'IV001', \PDO::PARAM_STR],
            [2, 'PAID', \PDO::PARAM_STR],
            [3, 'David', \PDO::PARAM_STR],
            [4, 50, \PDO::PARAM_STR],
            [5, 74, \PDO::PARAM_STR],
            [6, 1049.99, \PDO::PARAM_STR],
            [7, '2017-05-15 19:00:00+00', \PDO::PARAM_STR],
            [8, 18, \PDO::PARAM_INT],
        ];
        $stmt           = new PDOStatement();
        $dbh            = new PDO([
            'prepareReturn' => [$stmt]
        ]);
        $container      = new Container(['dbh' => $dbh]);
        $model          = new InvoicesModel($container);

        $model->update(18, [
            'code'       => 'IV001',
            'status'     => 'PAID',
            'customer'   => 'David',
            'discount'   => 50,
            'tax'        => 74,
            'total'      => 1049.99,
            'updated_at' => '2017-05-15 19:00:00+00',
        ]);

        $this->assertEquals(8, $stmt->getBindValueCallCount());
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
        $model          = new InvoicesModel($container);

        $model->update(1, [
            'code'       => '',
            'status'     => '',
            'customer'   => '',
            'discount'   => 0,
            'tax'        => 0,
            'total'      => 0,
            'created_at' => '',
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
        $model     = new InvoicesModel($container);

        $result = $model->update(1, [
            'code'       => '',
            'status'     => '',
            'customer'   => '',
            'discount'   => 0,
            'tax'        => 0,
            'total'      => 0,
            'created_at' => '',
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
        $model     = new InvoicesModel($container);

        $result = $model->update(1, [
            'code'       => '',
            'status'     => '',
            'customer'   => '',
            'discount'   => 0,
            'tax'        => 0,
            'total'      => 0,
            'created_at' => '',
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
        $model     = new InvoicesModel($container);

        $this->expectException(\PDOException::class);

        $model->update(1, ['name' => '']);
    }

    // delete entity

    public function testDeleteWithWrongIdParam()
    {
        $container = new Container([]);
        $model     = new InvoicesModel($container);

        $result1 = $model->delete(0);
        $result2 = $model->delete(-32);

        $this->assertEquals(false, $result1);
        $this->assertEquals(false, $result2);
    }

    public function testDeletePrepareAndQuery()
    {
        $expectedQuery = $this->inlineSQLString('DELETE FROM invoice WHERE id = ?;');
        $dbh           = new PDO();
        $container     = new Container(['dbh' => $dbh]);
        $model         = new InvoicesModel($container);

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
        $model          = new InvoicesModel($container);

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
        $model          = new InvoicesModel($container);

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
        $model     = new InvoicesModel($container);

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
        $model     = new InvoicesModel($container);

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
        $model     = new InvoicesModel($container);

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
        $model     = new InvoicesModel($container);

        $this->expectException(\PDOException::class);

        $model->delete(1);
    }
}
