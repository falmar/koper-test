<?php
declare(strict_types = 1);
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


class ProductsTest extends BaseTestCase
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

    public function tearDown()
    {
        self::$migration->down();
    }

    public function testGetJSONContentTypeResponse()
    {
        /** @var Request $request */
        $request  = $this->createRequest('GET', '/products/1');
        $request  = $request->withHeader('Accept', '');
        $response = $this->runApp($request);

        $this->assertContains('application/json', $response->getHeaderLine('Content-Type'));
    }

    public function testGetNotAcceptableWhenNotAcceptHeaderProvided()
    {
        /** @var Request $request */
        $request  = $this->createRequest('GET', '/products/1');
        $request  = $request->withHeader('Accept', '');
        $response = $this->runApp($request);

        $bodyString = (string)$response->getBody();
        $body       = json_decode($bodyString, true);

        $this->assertEquals(406, $response->getStatusCode());
        $this->assertContains('Request must accept media-type: application/json', $body['developerMessage']);
        $this->assertContains('Server couldn\'t provide a valid response.', $body['userMessage']);
    }

    public function testGetNotAcceptableWhenBadAcceptHeaderProvided()
    {
        /** @var Request $request */
        $request  = $this->createRequest('GET', '/products/1');
        $request  = $request->withHeader('Accept', 'application/xml');
        $response = $this->runApp($request);

        $bodyString = (string)$response->getBody();
        $body       = json_decode($bodyString, true);

        $this->assertEquals(406, $response->getStatusCode());
        $this->assertContains('Request must accept media-type: application/json', $body['developerMessage']);
        $this->assertContains('Server couldn\'t provide a valid response.', $body['userMessage']);
    }

    public function testGetAcceptMediaTypeWildcard()
    {
        /** @var Request $request */
        $request  = $this->createRequest('GET', '/products/1');
        $request  = $request->withHeader('Accept', '*/*');
        $response = $this->runApp($request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testGetAcceptMediaTypeJSON()
    {
        /** @var Request $request */
        $request  = $this->createRequest('GET', '/products/1');
        $request  = $request->withHeader('Accept', 'application/json');
        $response = $this->runApp($request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testGetNotFound()
    {
        /** @var Request $request */
        $request  = $this->createRequest('GET', '/products/5');
        $request  = $request->withHeader('Accept', 'application/json');
        $response = $this->runApp($request);

        $bodyString = (string)$response->getBody();
        $body       = json_decode($bodyString, true);

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertContains('Product (5) does not exist', $body['developerMessage']);
        $this->assertContains('Product does not exist', $body['userMessage']);
    }

    public function testGetBody()
    {
        $expectedBody = [
            'id'         => 1,
            'name'       => 'MX-4 Thermal Compound',
            'tags'       => ["Computers", "CPU", "Heat"],
            'price'      => 6.59,
            'created_at' => '2017-05-15 14:00:00+00',
            'updated_at' => '2017-05-15 14:00:00+00'
        ];
        /** @var Request $request */
        $request  = $this->createRequest('GET', '/products/1');
        $request  = $request->withHeader('Accept', 'application/json');
        $response = $this->runApp($request);

        $body = (string)$response->getBody();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($expectedBody, json_decode($body, true));
    }

    public function testGetError500()
    {
        self::$dbh->exec('DROP TABLE product');

        /** @var Request $request */
        $request  = $this->createRequest('GET', '/products/1');
        $request  = $request->withHeader('Accept', 'application/json');
        $response = $this->runApp($request);

        $bodyString = (string)$response->getBody();
        $body       = json_decode($bodyString, true);

        $this->assertEquals(500, $response->getStatusCode());
        $this->assertContains('Internal Server Error.', $body['developerMessage']);
        $this->assertContains('Unexpected error has occurred, try again later.', $body['userMessage']);
    }
}
