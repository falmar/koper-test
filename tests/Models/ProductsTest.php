<?php
/**
 * Created by PhpStorm.
 * User: falmar
 * Date: 5/10/17
 * Time: 5:36 PM
 */

namespace Tests\Models;


use KoperTest\Models\Products;
use KoperTest\Mocks\Container\Container;
use KoperTest\Mocks\PDO\PDO;
use KoperTest\Mocks\PDO\PDOStatement;


class ProductsTest extends \PHPUnit_Framework_TestCase
{
    public function testGetDataWithEmptyParams()
    {
        // PDO Expectations
        $dbh = new PDO();
        // DI Container
        $container = new Container(['dbh' => $dbh]);
        // class to test
        $model = new Products($container);

        // test data
        $model->getData($this->getDataDefaultParams());

        $this->assertEquals(1, $dbh->getPrepareCallCount());
        $this->assertEquals(['SELECT id, name FROM products;', null], $dbh->getPrepareParams(0));
    }

    public function testGetDataWithLimit()
    {
        // PDO Expectations
        $dbh = new PDO();
        // DI Container
        $container = new Container(['dbh' => $dbh]);

        // class to test
        $model = new Products($container);

        $params = $this->getDataDefaultParams(['limit' => 5]);

        // test data
        $model->getData($params);

        $this->assertEquals(1, $dbh->getPrepareCallCount());
        $this->assertEquals(['SELECT id, name FROM products LIMIT 5;', null], $dbh->getPrepareParams(0));
    }

    public function testGetDataWithLimitAndOffset()
    {
        // PDO Expectations
        $dbh = new PDO();
        // DI Container
        $container = new Container(['dbh' => $dbh]);

        // class to test
        $model = new Products($container);

        $params = $this->getDataDefaultParams([
            'limit'  => 5,
            'offset' => 20
        ]);

        // test data
        $model->getData($params);

        $this->assertEquals(1, $dbh->getPrepareCallCount());
        $this->assertEquals(['SELECT id, name FROM products LIMIT 5 OFFSET 20;', null], $dbh->getPrepareParams(0));
    }

    public function testGetDataWithOffsetNoLimit()
    {
        // PDO Expectations
        $dbh = new PDO();
        // DI Container
        $container = new Container(['dbh' => $dbh]);

        // class to test
        $model = new Products($container);

        $params = $this->getDataDefaultParams(['offset' => 20]);

        // test data
        $model->getData($params);

        $this->assertEquals(1, $dbh->getPrepareCallCount());
        $this->assertEquals(['SELECT id, name FROM products;', null], $dbh->getPrepareParams(0));
    }

    public function testGetDataWithSortFieldAndOrder()
    {
        // PDO Expectations
        $dbh = new PDO();
        // DI Container
        $container = new Container(['dbh' => $dbh]);

        // class to test
        $model = new Products($container);

        // get params
        $params = $this->getDataDefaultParams([
            'sortField' => 'name',
            'sortOrder' => 'ASC',
        ]);

        // test data
        $model->getData($params);

        // get params
        $params = $this->getDataDefaultParams([
            'sortField' => 'id',
            'sortOrder' => 'DESC',
        ]);

        // test data
        $model->getData($params);

        $this->assertEquals(2, $dbh->getPrepareCallCount());
        $this->assertEquals(['SELECT id, name FROM products ORDER BY name ASC;', null], $dbh->getPrepareParams(0));
        $this->assertEquals(['SELECT id, name FROM products ORDER BY id DESC;', null], $dbh->getPrepareParams(1));
    }

    public function testGetDataWithOnlyOneSortFieldOrOrder()
    {
        // PDO Expectations
        $dbh = new PDO();
        // DI Container
        $container = new Container(['dbh' => $dbh]);

        // class to test
        $model = new Products($container);

        $params = $this->getDataDefaultParams(['sortField' => 'name']);

        // test data
        $model->getData($params);

        $params['sortField'] = '';
        $params['sortOrder'] = 'DESC';

        $model->getData($params);

        $this->assertEquals(2, $dbh->getPrepareCallCount());
        $this->assertEquals(['SELECT id, name FROM products;', null], $dbh->getPrepareParams(0));
        $this->assertEquals(['SELECT id, name FROM products;', null], $dbh->getPrepareParams(1));
    }

    public function testGetDataCallExecuteStatement()
    {
        // PDOStatement Expectations
        $stmt = new PDOStatement();
        // PDO Expectations
        $dbh = new PDO([
            'prepareReturn' => [$stmt]
        ]);
        // DI Container
        $container = new Container(['dbh' => $dbh]);

        $model = new Products($container);

        $model->getData($this->getDataDefaultParams());

        $this->assertEquals(1, $stmt->getExecuteCallCount());
        $this->assertEquals([null], $stmt->getExecuteParams(0));
    }

    public function testGetDataCallFetchWidthAssoc()
    {
        // PDOStatement Expectations
        $stmt = new PDOStatement([
            'fetchReturn' => [true, true, false]
        ]);
        // PDO Expectations
        $dbh = new PDO([
            'prepareReturn' => [$stmt]
        ]);
        // DI Container
        $container = new Container(['dbh' => $dbh]);

        $model = new Products($container);

        $result = $model->getData($this->getDataDefaultParams());

        $this->assertEquals(3, $stmt->getFetchCallCount());
        $this->assertEquals([\PDO::FETCH_ASSOC, \PDO::FETCH_ORI_NEXT, 0], $stmt->getFetchParams(0));
        $this->assertEquals([\PDO::FETCH_ASSOC, \PDO::FETCH_ORI_NEXT, 0], $stmt->getFetchParams(1));
        $this->assertEquals([\PDO::FETCH_ASSOC, \PDO::FETCH_ORI_NEXT, 0], $stmt->getFetchParams(2));
        $this->assertEquals([true, true], $result);
    }

    public function testGetDataResult()
    {
        $stmt  = new PDOStatement([
            'fetchReturn' => [
                ['id' => 1, 'name' => 'MX-4 Thermal Compound'],
                ['id' => 2, 'name' => 'ArtiClean 1 & 2 30ml']
            ]
        ]);
        $dbh   = new PDO([
            'prepareReturn' => [$stmt]
        ]);
        $model = new Products(new Container(['dbh' => $dbh]));

        $expectedResult = [
            ['id' => 1, 'name' => 'MX-4 Thermal Compound'],
            ['id' => 2, 'name' => 'ArtiClean 1 & 2 30ml']
        ];

        $result = $model->getData($this->getDataDefaultParams());

        $this->assertEquals($expectedResult, $result);
    }

    protected function getDataDefaultParams(array $arr = [])
    {
        return array_merge([
            'limit'     => 0,
            'offset'    => 0,
            'sortField' => 'name',
            'sortOrder' => ''
        ], $arr);
    }
}
