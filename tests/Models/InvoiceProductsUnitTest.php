<?php
declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: falmar
 * Date: 5/10/24
 * Time: 11:19 PM
 */

namespace Tests\Models;

use KoperTest\Models\InvoiceProductsModel;
use Tests\BaseTestCase;
use Tests\Mocks\Container\Container;
use Tests\Mocks\PDO\PDO;
use Tests\Mocks\PDO\PDOStatement;

class InvoiceProductsUnitTest extends BaseTestCase
{
    // get entity
    public function testGetWithBadIdParam()
    {
        $dbh       = new PDO();
        $container = new Container(['dbh' => $dbh]);
        $model     = new InvoiceProductsModel($container);

        $result0 = $model->get(0, 1);
        $result1 = $model->get(1, -1);

        $this->assertEquals([], $result0);
        $this->assertEquals([], $result1);
    }

    public function testGetPrepareQuery()
    {
        $expectedQuery = $this->inlineSQLString('
          SELECT
            invoice_id, product_id, price, quantity
          FROM invoice_products
          WHERE invoice_id = ? AND product_id = ?;
        ');
        $dbh           = new PDO();
        $container     = new Container(['dbh' => $dbh]);
        $model         = new InvoiceProductsModel($container);

        $model->get(1, 1);

        $params = $dbh->getPrepareParams(0);

        $this->assertEquals(1, $dbh->getPrepareCallCount());
        $this->assertEquals(2, count($params));

        $params[0] = $this->inlineSQLString($params[0]);

        $this->assertEquals([$expectedQuery, null], $params);
    }

    public function testGetBindValue()
    {
        $expectedParams = [
            [1, 495, \PDO::PARAM_INT],
            [2, 3942, \PDO::PARAM_INT],
        ];
        $stmt           = new PDOStatement();
        $dbh            = new PDO([
            'prepareReturn' => [$stmt]
        ]);
        $container      = new Container(['dbh' => $dbh]);
        $model          = new InvoiceProductsModel($container);

        $model->get(495, 3942);

        $this->assertEquals(2, $stmt->getBindValueCallCount());
        $this->assertEquals($expectedParams, $stmt->getBindValueParamsAll());
    }

    public function testGetExecute()
    {
        $expectedParams = [null];
        $stmt           = new PDOStatement();
        $dbh            = new PDO([
            'prepareReturn' => [$stmt]
        ]);
        $container      = new Container(['dbh' => $dbh]);
        $model          = new InvoiceProductsModel($container);

        $model->get(20, 50);

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
        $model          = new InvoiceProductsModel($container);

        $model->get(1, 5);

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
        $model          = new InvoiceProductsModel($container);

        $result = $model->get(1, 5);

        $this->assertEquals($expectedResult, $result);
    }

    public function testGetTruthyResult()
    {
        $expectedResult = [
            'invoice_id' => 1,
            'product_id' => 2,
            'price'      => 1049.99,
            'quantity'   => 1
        ];
        $stmt           = new PDOStatement([
            'fetchReturn' => [
                [
                    'invoice_id' => 1,
                    'product_id' => 2,
                    'price'      => 1049.99,
                    'quantity'   => 1
                ]
            ]
        ]);
        $dbh            = new PDO([
            'prepareReturn' => [$stmt]
        ]);
        $container      = new Container(['dbh' => $dbh]);
        $model          = new InvoiceProductsModel($container);

        $result = $model->get(1, 2);

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
        $model     = new InvoiceProductsModel($container);

        $this->expectException(\PDOException::class);

        $model->get(1, 1);
    }

    // count total amount

    public function testCountEarlyReturnBadParams()
    {
        $dbh       = new PDO();
        $container = new Container(['dbh' => $dbh]);
        $model     = new InvoiceProductsModel($container);

        $result1 = $model->count([
            'invoice_id' => 0
        ]);

        $this->assertEquals(0, $result1);
    }

    public function testCountPrepareQuery()
    {
        $expectedQuery = $this->inlineSQLString('SELECT COUNT(*) FROM invoice_products WHERE invoice_id = ?;');
        $dbh           = new PDO();
        $container     = new Container(['dbh' => $dbh]);
        $model         = new InvoiceProductsModel($container);

        $model->count([
            'invoice_id' => 1
        ]);

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
        $model          = new InvoiceProductsModel($container);

        $model->count([
            'invoice_id' => 1
        ]);

        $this->assertEquals(1, $stmt->getBindColumnCallCount());
        $this->assertEquals($expectedParams, $stmt->getBindColumnParams(0));
    }

    public function testCountBindValue()
    {
        $expectedParams = [
            [1, 459, \PDO::PARAM_INT]
        ];
        $stmt           = new PDOStatement();
        $dbh            = new PDO([
            'prepareReturn' => [$stmt]
        ]);
        $container      = new Container(['dbh' => $dbh]);
        $model          = new InvoiceProductsModel($container);

        $model->count([
            'invoice_id' => 459
        ]);

        $this->assertEquals(1, $stmt->getBindValueCallCount());
        $this->assertEquals($expectedParams, $stmt->getBindValueParamsAll());
    }

    public function testCountExecute()
    {
        $expectedParams = [null];
        $stmt           = new PDOStatement();
        $dbh            = new PDO([
            'prepareReturn' => [$stmt]
        ]);
        $container      = new Container(['dbh' => $dbh]);
        $model          = new InvoiceProductsModel($container);

        $model->count([
            'invoice_id' => 1
        ]);

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
        $model          = new InvoiceProductsModel($container);

        $model->count([
            'invoice_id' => 1
        ]);

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
        $model          = new InvoiceProductsModel($container);

        $result = $model->count([
            'invoice_id' => 1
        ]);

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
        $model          = new InvoiceProductsModel($container);

        $result = $model->count([
            'invoice_id' => 1
        ]);

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
        $model     = new InvoiceProductsModel($container);

        $this->expectException(\PDOException::class);

        $model->count([
            'invoice_id' => 1
        ]);
    }

    // get collection

    public function testCollectionEarlyReturnBadParams()
    {
        // PDO Expectations
        $dbh = new PDO();
        // DI Container
        $container = new Container(['dbh' => $dbh]);
        // class to test
        $model = new InvoiceProductsModel($container);

        // test data
        $result1 = $model->collection([
            'invoice_id' => 0
        ]);

        $this->assertEquals([], $result1);
    }

    public function testCollectionWithEmptyParams()
    {
        $expectedQuery = $this->inlineSQLString(
            'SELECT 
                invoice_id, product_id, price, quantity 
              FROM invoice_products 
              WHERE invoice_id = ?
              LIMIT 25;'
        );
        // PDO Expectations
        $dbh = new PDO();
        // DI Container
        $container = new Container(['dbh' => $dbh]);
        // class to test
        $model = new InvoiceProductsModel($container);

        // test data
        $model->collection([
            'invoice_id' => 1
        ]);

        $params = $dbh->getPrepareParams(0);

        $this->assertEquals(2, count($params));

        $params[0] = $this->inlineSQLString($params[0]);

        $this->assertEquals(1, $dbh->getPrepareCallCount());
        $this->assertEquals([$expectedQuery, null], $params);
    }

    public function testCollectionWithLimit()
    {
        $expectedQuery = $this->inlineSQLString(
            'SELECT 
                invoice_id, product_id, price, quantity 
              FROM invoice_products 
              WHERE invoice_id = ?
              LIMIT 5;'
        );
        // PDO Expectations
        $dbh = new PDO();
        // DI Container
        $container = new Container(['dbh' => $dbh]);

        // class to test
        $model = new InvoiceProductsModel($container);

        // test data
        $model->collection([
            'invoice_id' => 1,
            'limit'      => 5
        ]);

        $params = $dbh->getPrepareParams(0);

        $this->assertEquals(2, count($params));

        $params[0] = $this->inlineSQLString($params[0]);

        $this->assertEquals(1, $dbh->getPrepareCallCount());
        $this->assertEquals([$expectedQuery, null], $params);
    }

    public function testCollectionWithLimitAndOffset()
    {
        $expectedQuery = $this->inlineSQLString(
            'SELECT 
                invoice_id, product_id, price, quantity 
              FROM invoice_products 
              WHERE invoice_id = ? LIMIT 5 OFFSET 20;'
        );
        // PDO Expectations
        $dbh = new PDO();
        // DI Container
        $container = new Container(['dbh' => $dbh]);
        // class to test
        $model = new InvoiceProductsModel($container);

        // test data
        $model->collection([
            'invoice_id' => 1,
            'limit'      => 5,
            'offset'     => 20
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
            'SELECT 
                invoice_id, product_id, price, quantity 
              FROM invoice_products 
              WHERE invoice_id = ? LIMIT 25 OFFSET 20;'
        );
        // PDO Expectations
        $dbh = new PDO();
        // DI Container
        $container = new Container(['dbh' => $dbh]);
        // class to test
        $model = new InvoiceProductsModel($container);

        // test data
        $model->collection([
            'invoice_id' => 1,
            'offset'     => 20
        ]);

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
                'SELECT 
                invoice_id, product_id, price, quantity 
              FROM invoice_products 
              WHERE invoice_id = ? ORDER BY price ASC LIMIT 25;'
            ),
            $this->inlineSQLString(
                'SELECT 
                invoice_id, product_id, price, quantity 
              FROM invoice_products 
              WHERE invoice_id = ? ORDER BY id DESC LIMIT 25;'
            )
        ];
        // PDO Expectations
        $dbh = new PDO();
        // DI Container
        $container = new Container(['dbh' => $dbh]);
        // class to test
        $model = new InvoiceProductsModel($container);

        // test data
        $model->collection([
            'invoice_id' => 1,
            'sortField'  => 'price',
            'sortOrder'  => 'ASC',
        ]);
        $model->collection([
            'invoice_id' => 1,
            'sortField'  => 'id',
            'sortOrder'  => 'DESC',
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
                'SELECT 
                invoice_id, product_id, price, quantity 
              FROM invoice_products 
              WHERE invoice_id = ? LIMIT 25;'
            ),
            $this->inlineSQLString(
                'SELECT 
                invoice_id, product_id, price, quantity 
              FROM invoice_products 
              WHERE invoice_id = ? LIMIT 25;'
            )
        ];
        // PDO Expectations
        $dbh = new PDO();
        // DI Container
        $container = new Container(['dbh' => $dbh]);

        // class to test
        $model = new InvoiceProductsModel($container);

        // test data
        $model->collection([
            'invoice_id' => 1,
            'sortField'  => 'name'
        ]);
        $model->collection([
            'invoice_id' => 1,
            'sortOrder'  => 'DESC'
        ]);

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
        $model = new InvoiceProductsModel($container);

        $model->collection([
            'invoice_id' => 1,
            'product_id' => 1
        ]);

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
                    'invoice_id' => 1,
                    'product_id' => 2,
                    'price'      => 1049.99,
                    'quantity'   => 1
                ],
                [
                    'id'         => 2,
                    'invoice_id' => 2,
                    'product_id' => 1,
                    'price'      => 6.117,
                    'quantity'   => 6
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
        $model          = new InvoiceProductsModel($container);
        $expectedResult = [
            [
                'id'         => 1,
                'invoice_id' => 1,
                'product_id' => 2,
                'price'      => 1049.99,
                'quantity'   => 1
            ],
            [
                'id'         => 2,
                'invoice_id' => 2,
                'product_id' => 1,
                'price'      => 6.117,
                'quantity'   => 6
            ]
        ];

        $result = $model->collection([
            'invoice_id' => 1
        ]);

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
                    'invoice_id' => 1,
                    'product_id' => 2,
                    'price'      => 1049.99,
                    'quantity'   => 1
                ],
                [
                    'id'         => 2,
                    'invoice_id' => 2,
                    'product_id' => 1,
                    'price'      => 6.117,
                    'quantity'   => 6
                ]
            ]
        ]);
        // PDO Expectations
        $dbh = new PDO([
            'prepareReturn' => [$stmt]
        ]);
        // DI Container
        $container = new Container(['dbh' => $dbh]);
        // class to test
        $model = new InvoiceProductsModel($container);

        $expectedResult = [
            [
                'id'         => 1,
                'invoice_id' => 1,
                'product_id' => 2,
                'price'      => 1049.99,
                'quantity'   => 1
            ],
            [
                'id'         => 2,
                'invoice_id' => 2,
                'product_id' => 1,
                'price'      => 6.117,
                'quantity'   => 6
            ]
        ];

        $result = $model->collection([
            'invoice_id' => 1
        ]);

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
        $model = new InvoiceProductsModel($container);

        $this->expectException(\PDOException::class);

        $model->collection([
            'invoice_id' => 1
        ]);
    }

    // --------------- add entity

    public function testAddPrepareQuery()
    {
        $expectedQuery = $this->inlineSQLString('
          INSERT INTO invoice_products
          (invoice_id, product_id, price, quantity) 
          VALUES
          (?, ?, ?, ?);
        ');
        // PDO Expectations
        $dbh = new PDO();
        // DI Container
        $container = new Container(['dbh' => $dbh]);
        // model
        $model = new InvoiceProductsModel($container);

        $model->add([
            'invoice_id' => 0,
            'product_id' => 0,
            'price'      => 0,
            'quantity'   => 0
        ]);

        $params = $dbh->getPrepareParams(0);

        $this->assertEquals(2, count($params));

        $params[0] = $this->inlineSQLString($params[0]);

        $this->assertEquals(1, $dbh->getPrepareCallCount());
        $this->assertEquals([$expectedQuery, null], $params);
    }

    public function testAddBindValueParams()
    {
        // expectation
        $expectedParams = [
            [1, 2, \PDO::PARAM_STR],
            [2, 1, \PDO::PARAM_STR],
            [3, 6.117, \PDO::PARAM_STR],
            [4, 6, \PDO::PARAM_INT]
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
        $model = new InvoiceProductsModel($container);

        $model->add([
            'invoice_id' => 2,
            'product_id' => 1,
            'price'      => 6.117,
            'quantity'   => 6
        ]);

        $this->assertEquals(4, $stmt->getBindValueCallCount());
        $this->assertEquals($expectedParams, $stmt->getBindValueParamsAll());
    }

    public function testAddResult()
    {
        $stmt      = new PDOStatement([
            'rowCountReturn' => [
                [1]
            ]
        ]);
        $dbh       = new PDO([
            'prepareReturn' => [$stmt]
        ]);
        $container = new Container(['dbh' => $dbh]);
        $model     = new InvoiceProductsModel($container);

        $result = $model->add([
            'invoice_id' => 0,
            'product_id' => 0,
            'price'      => 0,
            'quantity'   => 0
        ]);

        $this->assertEquals(true, $result);
    }

    public function testAddCallsExecute()
    {
        $stmt      = new PDOStatement();
        $dbh       = new PDO([
            'prepareReturn' => [$stmt]
        ]);
        $container = new Container(['dbh' => $dbh]);
        $model     = new InvoiceProductsModel($container);

        $model->add([
            'invoice_id' => 0,
            'product_id' => 0,
            'price'      => 0,
            'quantity'   => 0
        ]);

        $this->assertEquals(1, $stmt->getExecuteCallCount());
        $this->assertEquals([null], $stmt->getExecuteParams(0));
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
        $model     = new InvoiceProductsModel($container);

        $this->expectException(\PDOException::class);

        $model->add([]);
    }

    // update entity

    public function testUpdateEarlyReturnOnBadIdParam()
    {
        $container = new Container([]);
        $model     = new InvoiceProductsModel($container);

        $result1 = $model->update(0, 1, [
            'price' => 0
        ]);

        $result2 = $model->update(1, 0, [
            'price' => 0
        ]);

        $this->assertEquals(false, $result1);
        $this->assertEquals(false, $result2);
    }

    public function testUpdateEarlyReturnOnBadDataParam()
    {
        $container = new Container([]);
        $model     = new InvoiceProductsModel($container);

        $result = $model->update(1, 1, []);

        $this->assertEquals(false, $result);
    }

    public function testUpdateQuery()
    {
        $expectedParams = [
            $this->inlineSQLString('
              UPDATE invoice_products 
              SET price = ?, quantity = ?
              WHERE
              invoice_id = ? AND product_id = ?;
            '),
            null
        ];
        $dbh            = new PDO();
        $container      = new Container(['dbh' => $dbh]);
        $model          = new InvoiceProductsModel($container);

        $model->update(1, 1, [
            'invoice_id' => 0,
            'product_id' => 0,
            'price'      => 0,
            'quantity'   => 0
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
            [1, 6.117, \PDO::PARAM_STR],
            [2, 6, \PDO::PARAM_INT],
            [3, 1, \PDO::PARAM_INT],
            [4, 18, \PDO::PARAM_INT],
        ];
        $stmt           = new PDOStatement();
        $dbh            = new PDO([
            'prepareReturn' => [$stmt]
        ]);
        $container      = new Container(['dbh' => $dbh]);
        $model          = new InvoiceProductsModel($container);

        $model->update(1, 18, [
            'invoice_id' => 0,
            'product_id' => 0,
            'price'      => 6.117,
            'quantity'   => 6
        ]);

        $this->assertEquals(4, $stmt->getBindValueCallCount());
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
        $model          = new InvoiceProductsModel($container);

        $model->update(1, 1, [
            'invoice_id' => 0,
            'product_id' => 0,
            'price'      => 0,
            'quantity'   => 0
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
        $model     = new InvoiceProductsModel($container);

        $result = $model->update(1, 1, [
            'invoice_id' => 0,
            'product_id' => 0,
            'price'      => 0,
            'quantity'   => 0
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
        $model     = new InvoiceProductsModel($container);

        $result = $model->update(1, 1, [
            'invoice_id' => 0,
            'product_id' => 0,
            'price'      => 0,
            'quantity'   => 0
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
        $model     = new InvoiceProductsModel($container);

        $this->expectException(\PDOException::class);

        $model->update(1, 1, ['name' => '']);
    }

    // delete entity

    public function testDeleteWithWrongIdParam()
    {
        $container = new Container([]);
        $model     = new InvoiceProductsModel($container);

        $result1 = $model->delete(1, 0);
        $result2 = $model->delete(-32, 1);

        $this->assertEquals(false, $result1);
        $this->assertEquals(false, $result2);
    }

    public function testDeletePrepareAndQuery()
    {
        $expectedQuery = $this->inlineSQLString('DELETE FROM invoice_products WHERE invoice_id = ? AND product_id = ?;');
        $dbh           = new PDO();
        $container     = new Container(['dbh' => $dbh]);
        $model         = new InvoiceProductsModel($container);

        $model->delete(1, 1);

        $params = $dbh->getPrepareParams(0);

        $this->assertEquals(2, count($params));

        $params[0] = $this->inlineSQLString($params[0]);

        $this->assertEquals(1, $dbh->getPrepareCallCount());
        $this->assertEquals([$expectedQuery, null], $params);
    }

    public function testDeleteBindValue()
    {
        $expectedParams = [
            [1, 1, \PDO::PARAM_INT],
            [2, 5, \PDO::PARAM_INT]
        ];
        $stmt           = new PDOStatement();
        $dbh            = new PDO([
            'prepareReturn' => [$stmt]
        ]);
        $container      = new Container(['dbh' => $dbh]);
        $model          = new InvoiceProductsModel($container);

        $model->delete(1, 5);

        $this->assertEquals(2, $stmt->getBindValueCallCount());
        $this->assertEquals($expectedParams, $stmt->getBindValueParamsAll());
    }

    public function testDeleteExecute()
    {
        $expectedParams = [null];
        $stmt           = new PDOStatement();
        $dbh            = new PDO([
            'prepareReturn' => [$stmt]
        ]);
        $container      = new Container(['dbh' => $dbh]);
        $model          = new InvoiceProductsModel($container);

        $model->delete(1, 1);

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
        $model     = new InvoiceProductsModel($container);

        $model->delete(1, 1);

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
        $model     = new InvoiceProductsModel($container);

        $result = $model->delete(1, 1);

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
        $model     = new InvoiceProductsModel($container);

        $result = $model->delete(1, 1);

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
        $model     = new InvoiceProductsModel($container);

        $this->expectException(\PDOException::class);

        $model->delete(1, 1);
    }
}
