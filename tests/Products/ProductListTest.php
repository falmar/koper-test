<?php
/**
 * Created by PhpStorm.
 * User: falmar
 * Date: 5/10/17
 * Time: 5:36 PM
 */

namespace Tests\Products;


use KoperTest\Controllers\Products\ProductList;
use KoperTest\Mocks\Container\ContainerMock;
use KoperTest\Mocks\PDO\PDOMock;
use KoperTest\Mocks\PDO\PDOStatementMock;


class ProductListTest extends \PHPUnit_Framework_TestCase
{
    public function testGetDataWithEmptyParams()
    {
        // PDOStatement Expectations
        $stmt = new PDOStatementMock([]);
        // PDO Expectations
        $dbh = new PDOMock([
            'PDOStatement' => $stmt,

            'prepareCalls' => [
                ['SELECT id, name FROM products;', null]
            ]
        ]);
        // DI Container
        $container = new ContainerMock(['dbh' => $dbh]);

        // class to test
        $productList = new ProductList($container);

        $params = [
            'limit'     => 0,
            'offset'    => 0,
            'sortField' => '',
            'sortOrder' => ''
        ];

        // test data
        $productList->getData($params);

        $this->equalTo($dbh->getPrepareCallCount(), 1);
    }
}
