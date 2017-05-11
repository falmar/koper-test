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
        // PDOStatement Expectations
        $stmt = new PDOStatement([]);
        // PDO Expectations
        $dbh = new PDO([
            'PDOStatement' => $stmt,

            'prepareCalls' => [
                ['SELECT id, name FROM products;', null]
            ]
        ]);
        // DI Container
        $container = new Container([
            'dbh' => $dbh
        ]);

        // class to test
        $Products = new Products($container);

        $params = [
            'limit'     => 0,
            'offset'    => 0,
            'sortField' => '',
            'sortOrder' => ''
        ];

        // test data
        $Products->getData($params);

        $this->assertEquals($dbh->getPrepareCallCount(), 1);
    }

    public function testGetDataWithLimit()
    {
        // PDOStatement Expectations
        $stmt = new PDOStatement([]);
        // PDO Expectations
        $dbh = new PDO([
            'PDOStatement' => $stmt,

            'prepareCalls' => [
                ['SELECT id, name FROM products LIMIT 5;', null]
            ]
        ]);
        // DI Container
        $container = new Container([
            'dbh' => $dbh
        ]);

        // class to test
        $Products = new Products($container);

        $params = [
            'limit'     => 5,
            'offset'    => 0,
            'sortField' => '',
            'sortOrder' => ''
        ];

        // test data
        $Products->getData($params);

        $this->assertEquals($dbh->getPrepareCallCount(), 1);
    }

    public function testGetDataWithLimitAndOffset()
    {
        // PDOStatement Expectations
        $stmt = new PDOStatement([]);
        // PDO Expectations
        $dbh = new PDO([
            'PDOStatement' => $stmt,

            'prepareCalls' => [
                ['SELECT id, name FROM products LIMIT 5 OFFSET 20;', null]
            ]
        ]);
        // DI Container
        $container = new Container([
            'dbh' => $dbh
        ]);

        // class to test
        $Products = new Products($container);

        $params = [
            'limit'     => 5,
            'offset'    => 20,
            'sortField' => '',
            'sortOrder' => ''
        ];

        // test data
        $Products->getData($params);

        $this->assertEquals($dbh->getPrepareCallCount(), 1);
    }

    public function testGetDataWithOffsetNoLimit()
    {
        // PDOStatement Expectations
        $stmt = new PDOStatement([]);
        // PDO Expectations
        $dbh = new PDO([
            'PDOStatement' => $stmt,

            'prepareCalls' => [
                ['SELECT id, name FROM products;', null]
            ]
        ]);
        // DI Container
        $container = new Container([
            'dbh' => $dbh
        ]);

        // class to test
        $Products = new Products($container);

        $params = [
            'limit'     => 0,
            'offset'    => 20,
            'sortField' => '',
            'sortOrder' => ''
        ];

        // test data
        $Products->getData($params);

        $this->assertEquals($dbh->getPrepareCallCount(), 1);
    }

    public function testGetDataWithSortFieldAndOrder()
    {
        // PDOStatement Expectations
        $stmt = new PDOStatement([]);
        // PDO Expectations
        $dbh = new PDO([
            'PDOStatement' => $stmt,

            'prepareCalls' => [
                ['SELECT id, name FROM products ORDER BY name ASC;', null],
                ['SELECT id, name FROM products ORDER BY id DESC;', null],
            ]
        ]);
        // DI Container
        $container = new Container([
            'dbh' => $dbh
        ]);

        // class to test
        $Products = new Products($container);

        $params = [
            'limit'     => 0,
            'offset'    => 0,
            'sortField' => 'name',
            'sortOrder' => 'ASC'
        ];

        // test data
        $Products->getData($params);

        $this->assertEquals($dbh->getPrepareCallCount(), 1);

        $params['sortField'] = 'id';
        $params['sortOrder'] = 'DESC';
        $Products->getData($params);

        $this->assertEquals($dbh->getPrepareCallCount(), 2);
    }

    public function testGetDataWithOnlyOneSortFieldOrOrder()
    {
        // PDOStatement Expectations
        $stmt = new PDOStatement([]);
        // PDO Expectations
        $dbh = new PDO([
            'PDOStatement' => $stmt,

            'prepareCalls' => [
                ['SELECT id, name FROM products;', null],
                ['SELECT id, name FROM products;', null],
            ]
        ]);
        // DI Container
        $container = new Container([
            'dbh' => $dbh
        ]);

        // class to test
        $Products = new Products($container);

        $params = [
            'limit'     => 0,
            'offset'    => 0,
            'sortField' => 'name',
            'sortOrder' => ''
        ];

        // test data
        $Products->getData($params);

        $this->assertEquals($dbh->getPrepareCallCount(), 1);

        $params['sortField'] = '';
        $params['sortOrder'] = 'DESC';

        $Products->getData($params);

        $this->assertEquals($dbh->getPrepareCallCount(), 2);
    }
}
