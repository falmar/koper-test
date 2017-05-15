<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: falmar
 * Date: 5/15/17
 * Time: 5:23 PM
 */

namespace Tests\Controllers;


use KoperTest\db\Product;
use Slim\Http\Request;
use Tests\BaseTestCase;


class ProductsGetTest extends BaseTestCase
{
    /** @var \PDO */
    protected static $dbh = null;
    /** @var Product */
    protected static $migration = null;

    public static function setUpBeforeClass()
    {
        self::$dbh       = self::getPDO();
        self::$migration = new Product(self::$dbh);
    }

    public function setUp()
    {
        self::$migration->up();
        self::$migration->seed();
    }

    public function testNotAcceptableIfBadAcceptHeader()
    {
        /** @var Request $request */
        $request = $this->createRequest('GET', '/products/1');

        $request = $request->withHeader('Accept', '');

        $response = $this->runApp($request);

        $this->assertEquals(406, $response->getStatusCode());
    }
}
