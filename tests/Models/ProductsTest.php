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

        $params = [
            'limit'     => 0,
            'offset'    => 0,
            'sortField' => '',
            'sortOrder' => ''
        ];

        // test data
        $model->getData($params);

        $this->assertEquals($dbh->getPrepareCallCount(), 1);
        $this->assertEquals($dbh->getPrepareParams(0), ['SELECT id, name FROM products;', null]);
    }

    public function testGetDataWithLimit()
    {
        // PDO Expectations
        $dbh = new PDO();
        // DI Container
        $container = new Container(['dbh' => $dbh]);

        // class to test
        $model = new Products($container);

        $params = [
            'limit'     => 5,
            'offset'    => 0,
            'sortField' => '',
            'sortOrder' => ''
        ];

        // test data
        $model->getData($params);

        $this->assertEquals($dbh->getPrepareCallCount(), 1);
        $this->assertEquals($dbh->getPrepareParams(0), ['SELECT id, name FROM products LIMIT 5;', null]);
    }

    public function testGetDataWithLimitAndOffset()
    {
        // PDO Expectations
        $dbh = new PDO();
        // DI Container
        $container = new Container(['dbh' => $dbh]);

        // class to test
        $model = new Products($container);

        $params = [
            'limit'     => 5,
            'offset'    => 20,
            'sortField' => '',
            'sortOrder' => ''
        ];

        // test data
        $model->getData($params);

        $this->assertEquals($dbh->getPrepareCallCount(), 1);
        $this->assertEquals($dbh->getPrepareParams(0), ['SELECT id, name FROM products LIMIT 5 OFFSET 20;', null]);
    }

    public function testGetDataWithOffsetNoLimit()
    {
        // PDO Expectations
        $dbh = new PDO();
        // DI Container
        $container = new Container(['dbh' => $dbh]);

        // class to test
        $model = new Products($container);

        $params = [
            'limit'     => 0,
            'offset'    => 20,
            'sortField' => '',
            'sortOrder' => ''
        ];

        // test data
        $model->getData($params);

        $this->assertEquals($dbh->getPrepareCallCount(), 1);
        $this->assertEquals($dbh->getPrepareParams(0), ['SELECT id, name FROM products;', null]);
    }

    public function testGetDataWithSortFieldAndOrder()
    {
        // PDO Expectations
        $dbh = new PDO();
        // DI Container
        $container = new Container(['dbh' => $dbh]);

        // class to test
        $model = new Products($container);

        $params = [
            'limit'     => 0,
            'offset'    => 0,
            'sortField' => 'name',
            'sortOrder' => 'ASC'
        ];

        // test data
        $model->getData($params);

        $params['sortField'] = 'id';
        $params['sortOrder'] = 'DESC';
        $model->getData($params);

        $this->assertEquals($dbh->getPrepareCallCount(), 2);
        $this->assertEquals($dbh->getPrepareParams(0), ['SELECT id, name FROM products ORDER BY name ASC;', null]);
        $this->assertEquals($dbh->getPrepareParams(1), ['SELECT id, name FROM products ORDER BY id DESC;', null]);
    }

    public function testGetDataWithOnlyOneSortFieldOrOrder()
    {
        // PDO Expectations
        $dbh = new PDO();
        // DI Container
        $container = new Container(['dbh' => $dbh]);

        // class to test
        $model = new Products($container);

        $params = [
            'limit'     => 0,
            'offset'    => 0,
            'sortField' => 'name',
            'sortOrder' => ''
        ];

        // test data
        $model->getData($params);

        $params['sortField'] = '';
        $params['sortOrder'] = 'DESC';

        $model->getData($params);

        $this->assertEquals($dbh->getPrepareCallCount(), 2);
        $this->assertEquals($dbh->getPrepareParams(0), ['SELECT id, name FROM products;', null]);
        $this->assertEquals($dbh->getPrepareParams(1), ['SELECT id, name FROM products;', null]);
    }

    public function testGetDataCallExecuteStatement()
    {
        // PDOStatement Expectations
        $stmt = new PDOStatement();
        // PDO Expectations
        $dbh = new PDO([
            'prepareReturn' => [$stmt]
        ]);
        // DI Conainer
        $container = new Container([
            'dbh' => $dbh
        ]);

        $model = new Products($container);

        $params = [
            'limit'     => 0,
            'offset'    => 0,
            'sortField' => 'name',
            'sortOrder' => ''
        ];

        $model->getData($params);

        $this->assertEquals($stmt->getExecuteCallCount(), 1);
        $this->assertEquals($stmt->getExecuteParams(0), [null]);
    }
}
