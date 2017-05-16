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
        $request  = $this->createRequest('GET', '/products/1');
        $request  = $request->withHeader('Accept', '');
        $response = $this->runApp($request);

        $this->assertContains('application/json', $response->getHeaderLine('Content-Type'));
    }

    public function testGetNotAcceptableWhenNotAcceptHeaderProvided()
    {
        $request  = $this->createRequest('GET', '/products/1');
        $request  = $request->withHeader('Accept', '');
        $response = $this->runApp($request);

        $bodyString = (string)$response->getBody();
        $body       = json_decode($bodyString, true);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertContains('Request must accept media-type: application/json', $body['developerMessage']);
        $this->assertContains('Server couldn\'t provide a valid response.', $body['userMessage']);
    }

    public function testGetNotAcceptableWhenBadAcceptHeaderProvided()
    {
        $request  = $this->createRequest('GET', '/products/1');
        $request  = $request->withHeader('Accept', 'application/xml');
        $response = $this->runApp($request);

        $bodyString = (string)$response->getBody();
        $body       = json_decode($bodyString, true);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertContains('Request must accept media-type: application/json', $body['developerMessage']);
        $this->assertContains('Server couldn\'t provide a valid response.', $body['userMessage']);
    }

    public function testGetAcceptMediaTypeWildcard()
    {
        $request  = $this->createRequest('GET', '/products/1');
        $request  = $request->withHeader('Accept', '*/*');
        $response = $this->runApp($request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testGetAcceptMediaTypeJSON()
    {
        $request  = $this->createRequest('GET', '/products/1');
        $request  = $request->withHeader('Accept', 'application/json');
        $response = $this->runApp($request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testGetNotFound()
    {
        $request  = $this->createRequest('GET', '/products/5');
        $request  = $request->withHeader('Accept', 'application/json');
        $response = $this->runApp($request);

        $bodyString = (string)$response->getBody();
        $body       = json_decode($bodyString, true);

        $this->assertEquals(400, $response->getStatusCode());
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
        $request      = $this->createRequest('GET', '/products/1');
        $request      = $request->withHeader('Accept', 'application/json');
        $response     = $this->runApp($request);

        $body = (string)$response->getBody();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($expectedBody, json_decode($body, true));
    }

    public function testGetError500()
    {
        self::$dbh->exec('DROP TABLE product');

        $request  = $this->createRequest('GET', '/products/1');
        $request  = $request->withHeader('Accept', 'application/json');
        $response = $this->runApp($request);

        $bodyString = (string)$response->getBody();
        $body       = json_decode($bodyString, true);

        $this->assertEquals(500, $response->getStatusCode());
        $this->assertContains('Internal Server Error.', $body['developerMessage']);
        $this->assertContains('Unexpected error has occurred, try again later.', $body['userMessage']);
    }

    public function testAddResponseContentTypeJSON()
    {
        $request  = $this->createRequest('POST', '/products');
        $response = $this->runApp($request);

        $this->assertContains('application/json', $response->getHeaderLine('Content-Type'));
    }

    public function testAddNotAcceptableWithBadAcceptHeader()
    {
        $request  = $this->createRequest('GET', '/products');
        $request  = $request->withHeader('Accept', '');
        $response = $this->runApp($request);

        $bodyString = (string)$response->getBody();
        $body       = json_decode($bodyString, true);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertContains('Request must accept media-type: application/json.', $body['developerMessage']);
        $this->assertContains('Bad Request.', $body['userMessage']);
    }

    public function testAddRequestContentTypeJSON()
    {
        $request  = $this->createRequest('GET', '/products');
        $request  = $request->withHeader('Accept', 'application/json');
        $response = $this->runApp($request);

        $bodyString = (string)$response->getBody();
        $body       = json_decode($bodyString, true);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertContains('Request must have Content-Type: application/json.', $body['developerMessage']);
        $this->assertContains('Bad Request.', $body['userMessage']);
    }

    public function testAddBadRequestEmptyBody()
    {
        $request  = $this->createRequest('POST', '/products');
        $request  = $request->withHeader('Accept', 'application/json');
        $request  = $request->withHeader('Content-Type', 'application/json');
        $response = $this->runApp($request);

        $bodyString = (string)$response->getBody();
        $body       = json_decode($bodyString, true);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertContains('Request body cannot be empty.', $body['developerMessage']);
        $this->assertContains('Bad Request.', $body['userMessage']);
    }

    public function testAddBadRequestEmptyBodyValues()
    {
        $body     = [];
        $request  = $this->createRequest('POST', '/products', $body);
        $request  = $request->withHeader('Accept', 'application/json');
        $request  = $request->withHeader('Content-Type', 'application/json');
        $response = $this->runApp($request);

        $bodyString = (string)$response->getBody();
        $body       = json_decode($bodyString, true);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertContains('Request body values cannot be empty.', $body['developerMessage']);
        $this->assertContains('Bad Request.', $body['userMessage']);
    }

    public function testAddError500()
    {
        self::$dbh->exec('DROP TABLE product');

        $body     = [
            'name'  => 'ArtiClean 1&2 30ml',
            'tags'  => ['CPU'],
            'price' => 6.12
        ];
        $request  = $this->createRequest('POST', '/products', $body);
        $request  = $request->withHeader('Accept', 'application/json');
        $request  = $request->withHeader('Content-Type', 'application/json');
        $response = $this->runApp($request);

        $bodyString = (string)$response->getBody();
        $body       = json_decode($bodyString, true);

        $this->assertEquals(500, $response->getStatusCode());
        $this->assertContains('Internal Server Error.', $body['developerMessage']);
        $this->assertContains('Unexpected error has occurred, try again later.', $body['userMessage']);
    }

    public function testAddAcceptMediaTypeJSON()
    {
        $body    = [
            'name'  => 'ArtiClean 1&2 30ml',
            'tags'  => ['CPU'],
            'price' => 6.12
        ];
        $request = $this->createRequest('POST', '/products', $body);
        $request = $request->withHeader('Accept', 'application/json');
        $request = $request->withHeader('Content-Type', 'application/json');

        $response = $this->runApp($request);

        $this->assertEquals(201, $response->getStatusCode());
    }

    public function testAddAcceptMediaTypeWildcard()
    {
        $body    = [
            'name'  => 'ArtiClean 1&2 30ml',
            'tags'  => ['CPU'],
            'price' => 6.12
        ];
        $request = $this->createRequest('POST', '/products', $body);
        $request = $request->withHeader('Accept', '*/*');
        $request = $request->withHeader('Content-Type', 'application/json');

        $response = $this->runApp($request);

        $this->assertEquals(201, $response->getStatusCode());
    }

    public function testAddBodyResponse()
    {
        $body                = [
            'name'  => 'ArtiClean 1&2 30ml',
            'tags'  => ['CPU'],
            'price' => 6.12
        ];
        $expectedDate        = new \DateTime();
        $request             = $this->createRequest('POST', '/products', $body);
        $request             = $request->withHeader('Accept', 'application/json');
        $request             = $request->withHeader('Content-Type', 'application/json');
        $response            = $this->runApp($request);
        $expectedDateForward = new \DateTime();

        $bodyString   = (string)$response->getBody();
        $responseBody = json_decode($bodyString, true);

        $createdAt   = $responseBody['created_at'];
        $updatedAt   = $responseBody['updated_at'];
        $createdDate = new \DateTime($createdAt);

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertArraySubset($body, $responseBody);
        $this->assertEquals($createdAt, $updatedAt);
        $this->assertTrue(
            $expectedDate < $createdDate &&
            $expectedDateForward > $createdDate
        );
    }
}
