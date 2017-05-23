<?php
declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: falmar
 * Date: 5/15/23
 * Time: 18:16 PM
 */

namespace Tests\Controllers;


use KoperTest\Migrations\Invoice;
use Slim\Http\Request;
use Tests\BaseTestCase;


class InvoicesControllerTest extends BaseTestCase
{
    /** @var \PDO */
    protected static $dbh = null;
    /** @var Invoice */
    protected static $migration = null;

    public static function setUpBeforeClass()
    {
        self::$dbh       = self::getPDO();
        self::$migration = new Invoice(self::$dbh);
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
        $request  = $this->createRequest('GET', '/invoices/1');
        $request  = $request->withHeader('Accept', '');
        $response = $this->runApp($request);

        $this->assertContains('application/json', $response->getHeaderLine('Content-Type'));
    }

    public function testGetNotAcceptableWhenNotAcceptHeaderProvided()
    {
        $request  = $this->createRequest('GET', '/invoices/1');
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
        $request  = $this->createRequest('GET', '/invoices/1');
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
        $request  = $this->createRequest('GET', '/invoices/1');
        $request  = $request->withHeader('Accept', '*/*');
        $response = $this->runApp($request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testGetAcceptMediaTypeJSON()
    {
        $request  = $this->createRequest('GET', '/invoices/1');
        $request  = $request->withHeader('Accept', 'application/json');
        $response = $this->runApp($request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testGetNotFound()
    {
        $request  = $this->createRequest('GET', '/invoices/5');
        $request  = $request->withHeader('Accept', 'application/json');
        $response = $this->runApp($request);

        $bodyString = (string)$response->getBody();
        $body       = json_decode($bodyString, true);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertContains('Invoice (5) does not exist', $body['developerMessage']);
        $this->assertContains('Invoice does not exist', $body['userMessage']);
    }

    public function testGetBody()
    {
        $expectedBody = [
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
        $request      = $this->createRequest('GET', '/invoices/1');
        $request      = $request->withHeader('Accept', 'application/json');
        $response     = $this->runApp($request);

        $body = (string)$response->getBody();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($expectedBody, json_decode($body, true));
    }

    public function testGetError500()
    {
        self::$dbh->exec('DROP TABLE invoice');

        $request  = $this->createRequest('GET', '/invoices/1');
        $request  = $request->withHeader('Accept', 'application/json');
        $response = $this->runApp($request);

        $bodyString = (string)$response->getBody();
        $body       = json_decode($bodyString, true);

        $this->assertEquals(500, $response->getStatusCode());
        $this->assertContains('Internal Server Error.', $body['developerMessage']);
        $this->assertContains('Unexpected error has occurred, try again later.', $body['userMessage']);
    }

    // add entity

    public function testAddResponseContentTypeJSON()
    {
        $request  = $this->createRequest('POST', '/invoices');
        $response = $this->runApp($request);

        $this->assertContains('application/json', $response->getHeaderLine('Content-Type'));
    }

    public function testAddNotAcceptableWithBadAcceptHeader()
    {
        $request  = $this->createRequest('POST', '/invoices');
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
        $request  = $this->createRequest('POST', '/invoices');
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
        $request  = $this->createRequest('POST', '/invoices');
        $request  = $request->withHeader('Accept', 'application/json');
        $request  = $request->withAddedHeader('Content-Type', 'application/json');
        $response = $this->runApp($request);

        $bodyString = (string)$response->getBody();
        $body       = json_decode($bodyString, true);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertContains('Request body or fields cannot be empty.', $body['developerMessage']);
        $this->assertContains('Bad Request.', $body['userMessage']);
    }

    public function testAddBadRequestEmptyBodyValues()
    {
        $body     = [];
        $request  = $this->createRequest('POST', '/invoices', $body);
        $request  = $request->withHeader('Accept', 'application/json');
        $request  = $request->withHeader('Content-Type', 'application/json');
        $response = $this->runApp($request);

        $bodyString = (string)$response->getBody();
        $body       = json_decode($bodyString, true);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertContains('Request body or fields cannot be empty.', $body['developerMessage']);
        $this->assertContains('Bad Request.', $body['userMessage']);
    }

    public function testAddBadRequestBadBodyValues()
    {
        $body     = [
            'code'     => 'IV003',
            'status'   => 'PENDING',
            'customer' => 'David',
            'discount' => 0,
            'tax'      => 0,
            'total'    => 'not-a-number'
        ];
        $request  = $this->createRequest('POST', '/invoices', $body);
        $request  = $request->withHeader('Accept', 'application/json');
        $request  = $request->withHeader('Content-Type', 'application/json');
        $response = $this->runApp($request);

        $bodyString = (string)$response->getBody();
        $body       = json_decode($bodyString, true);

        $this->assertEquals(500, $response->getStatusCode());
        $this->assertContains('Internal Server Error.', $body['developerMessage']);
        $this->assertContains('Unexpected error has occurred, try again later.', $body['userMessage']);
    }

    public function testAddError500()
    {
        /** @var Request $request */
        self::$dbh->exec('DROP TABLE invoice');

        $body     = [
            'code'     => 'IV003',
            'status'   => 'PENDING',
            'customer' => 'David',
            'discount' => 5,
            'tax'      => 0,
            'total'    => 18
        ];
        $request  = $this->createRequest('POST', '/invoices', $body);
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
            'code'     => 'IV003',
            'status'   => 'PENDING',
            'customer' => 'David',
            'discount' => 0,
            'tax'      => 0,
            'total'    => 61,
        ];
        $request = $this->createRequest('POST', '/invoices', $body);
        $request = $request->withHeader('Accept', 'application/json');
        $request = $request->withHeader('Content-Type', 'application/json');

        $response = $this->runApp($request);

        $this->assertEquals(201, $response->getStatusCode());
    }

    public function testAddAcceptMediaTypeWildcard()
    {
        $body    = [
            'code'     => 'IV003',
            'status'   => 'PENDING',
            'customer' => 'David',
            'discount' => 0,
            'tax'      => 0,
            'total'    => 61,
        ];
        $request = $this->createRequest('POST', '/invoices', $body);
        $request = $request->withHeader('Accept', '*/*');
        $request = $request->withHeader('Content-Type', 'application/json');

        $response = $this->runApp($request);

        $this->assertEquals(201, $response->getStatusCode());
    }

    public function testAddBodyResponse()
    {
        $body                = [
            'code'     => 'IV003',
            'status'   => 'PENDING',
            'customer' => 'David',
            'discount' => 0,
            'tax'      => 0,
            'total'    => 61,
        ];
        $expectedDate        = new \DateTime();
        $request             = $this->createRequest('POST', '/invoices', $body);
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
            $expectedDate <= $createdDate &&
            $expectedDateForward >= $createdDate
        );
    }

    // update entity

    public function testUpdateResponseContentTypeJSON()
    {
        $request  = $this->createRequest('PUT', '/invoices/1');
        $response = $this->runApp($request);

        $this->assertContains('application/json', $response->getHeaderLine('Content-Type'));
    }

    public function testUpdateNotAcceptableWithBadAcceptHeader()
    {
        $request  = $this->createRequest('PUT', '/invoices/1');
        $request  = $request->withHeader('Accept', '');
        $response = $this->runApp($request);

        $bodyString = (string)$response->getBody();
        $body       = json_decode($bodyString, true);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertContains('Request must accept media-type: application/json.', $body['developerMessage']);
        $this->assertContains('Bad Request.', $body['userMessage']);
    }

    public function testUpdateRequestContentTypeJSON()
    {
        $request  = $this->createRequest('PUT', '/invoices/1');
        $request  = $request->withHeader('Accept', 'application/json');
        $response = $this->runApp($request);

        $bodyString = (string)$response->getBody();
        $body       = json_decode($bodyString, true);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertContains('Request must have Content-Type: application/json.', $body['developerMessage']);
        $this->assertContains('Bad Request.', $body['userMessage']);
    }

    public function testUpdateBadRequestEmptyBody()
    {
        $request  = $this->createRequest('PUT', '/invoices/1');
        $request  = $request->withHeader('Accept', 'application/json');
        $request  = $request->withAddedHeader('Content-Type', 'application/json');
        $response = $this->runApp($request);

        $bodyString = (string)$response->getBody();
        $body       = json_decode($bodyString, true);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertContains('Request body or fields cannot be empty.', $body['developerMessage']);
        $this->assertContains('Bad Request.', $body['userMessage']);
    }

    public function testUpdateBadRequestEmptyBodyValues()
    {
        $body     = [];
        $request  = $this->createRequest('PUT', '/invoices/1', $body);
        $request  = $request->withHeader('Accept', 'application/json');
        $request  = $request->withHeader('Content-Type', 'application/json');
        $response = $this->runApp($request);

        $bodyString = (string)$response->getBody();
        $body       = json_decode($bodyString, true);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertContains('Request body or fields cannot be empty.', $body['developerMessage']);
        $this->assertContains('Bad Request.', $body['userMessage']);
    }

    public function testUpdateBadRequestBadBodyValues()
    {
        $body     = [
            'code'     => 'IV003',
            'status'   => 'PENDING',
            'customer' => 'David',
            'discount' => 0,
            'tax'      => 'not-a-number',
            'total'    => 61,
        ];
        $request  = $this->createRequest('PUT', '/invoices/2', $body);
        $request  = $request->withHeader('Accept', 'application/json');
        $request  = $request->withHeader('Content-Type', 'application/json');
        $response = $this->runApp($request);

        $bodyString = (string)$response->getBody();
        $body       = json_decode($bodyString, true);

        $this->assertEquals(500, $response->getStatusCode());
        $this->assertContains('Internal Server Error.', $body['developerMessage']);
        $this->assertContains('Unexpected error has occurred, try again later.', $body['userMessage']);
    }

    public function testUpdateBodyResponseNotExistentRow()
    {
        $body     = [
            'code'     => 'IV003',
            'status'   => 'PENDING',
            'customer' => 'David',
            'discount' => 0,
            'tax'      => 0,
            'total'    => 61,
        ];
        $request  = $this->createRequest('PUT', '/invoices/5', $body);
        $request  = $request->withHeader('Accept', 'application/json');
        $request  = $request->withHeader('Content-Type', 'application/json');
        $response = $this->runApp($request);

        $bodyString   = (string)$response->getBody();
        $responseBody = json_decode($bodyString, true);

        $this->assertEquals(500, $response->getStatusCode());
        $this->assertContains(
            'Invoice (5) does not exist. Due to database capabilities new row can\'t be added.',
            $responseBody['developerMessage']
        );
        $this->assertContains('Unexpected error has occurred, try again later.', $responseBody['userMessage']);
    }

    public function testUpdateError500()
    {
        self::$dbh->exec('DROP TABLE invoice');

        $body     = [
            'code'     => 'IV003',
            'status'   => 'PENDING',
            'customer' => 'David',
            'discount' => 0,
            'tax'      => 0,
            'total'    => 61,
        ];
        $request  = $this->createRequest('PUT', '/invoices/1', $body);
        $request  = $request->withHeader('Accept', 'application/json');
        $request  = $request->withHeader('Content-Type', 'application/json');
        $response = $this->runApp($request);

        $bodyString = (string)$response->getBody();
        $body       = json_decode($bodyString, true);

        $this->assertEquals(500, $response->getStatusCode());
        $this->assertContains('Internal Server Error.', $body['developerMessage']);
        $this->assertContains('Unexpected error has occurred, try again later.', $body['userMessage']);
    }

    public function testUpdateAcceptMediaTypeJSON()
    {
        $body    = [
            'code'     => 'IV003',
            'status'   => 'PENDING',
            'customer' => 'David',
            'discount' => 0,
            'tax'      => 0,
            'total'    => 61,
        ];
        $request = $this->createRequest('PUT', '/invoices/1', $body);
        $request = $request->withHeader('Accept', 'application/json');
        $request = $request->withHeader('Content-Type', 'application/json');

        $response = $this->runApp($request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testUpdateAcceptMediaTypeWildcard()
    {
        $body    = [
            'code'     => 'IV003',
            'status'   => 'PENDING',
            'customer' => 'David',
            'discount' => 0,
            'tax'      => 0,
            'total'    => 61,
        ];
        $request = $this->createRequest('PUT', '/invoices/1', $body);
        $request = $request->withHeader('Accept', '*/*');
        $request = $request->withHeader('Content-Type', 'application/json');

        $response = $this->runApp($request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testUpdateBodyResponse()
    {
        $body                = [
            'code'     => 'IV003',
            'status'   => 'PENDING',
            'customer' => 'David',
            'discount' => 0,
            'tax'      => 0,
            'total'    => 61,
        ];
        $expectedDate        = new \DateTime();
        $request             = $this->createRequest('PUT', '/invoices/1', $body);
        $request             = $request->withHeader('Accept', 'application/json');
        $request             = $request->withHeader('Content-Type', 'application/json');
        $response            = $this->runApp($request);
        $expectedDateForward = new \DateTime();

        $body['id']   = 1;
        $bodyString   = (string)$response->getBody();
        $responseBody = json_decode($bodyString, true);

        $createdAt   = $responseBody['created_at'];
        $updatedAt   = $responseBody['updated_at'];
        $updatedDate = new \DateTime($updatedAt);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArraySubset($body, $responseBody);
        $this->assertNotEquals($createdAt, $updatedAt);
        $this->assertTrue(
            $expectedDate <= $updatedDate &&
            $expectedDateForward >= $updatedDate
        );
    }

    // delete entity

    public function testDeleteBodyResponseNotExistentRow()
    {
        $request  = $this->createRequest('DELETE', '/invoices/5');
        $response = $this->runApp($request);

        $bodyString   = (string)$response->getBody();
        $responseBody = json_decode($bodyString, true);

        $this->assertEquals(500, $response->getStatusCode());
        $this->assertContains('Invoice (5) does not exist.', $responseBody['developerMessage']);
        $this->assertContains('Unexpected error has occurred, try again later.', $responseBody['userMessage']);
    }

    public function testDeleteError500()
    {
        self::$dbh->exec('DROP TABLE invoice');

        $request  = $this->createRequest('DELETE', '/invoices/1');
        $response = $this->runApp($request);

        $bodyString = (string)$response->getBody();
        $body       = json_decode($bodyString, true);

        $this->assertEquals(500, $response->getStatusCode());
        $this->assertContains('Internal Server Error.', $body['developerMessage']);
        $this->assertContains('Unexpected error has occurred, try again later.', $body['userMessage']);
    }

    public function testDeleteBodyResponse()
    {
        $request  = $this->createRequest('DELETE', '/invoices/1');
        $response = $this->runApp($request);

        $body = (string)$response->getBody();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEmpty($body);
    }

    // test collection

    public function testCollectionJSONContentTypeResponse()
    {
        $request  = $this->createRequest('GET', '/invoices');
        $request  = $request->withHeader('Accept', '');
        $response = $this->runApp($request);

        $this->assertContains('application/json', $response->getHeaderLine('Content-Type'));
    }

    public function testCollectionNotAcceptableWhenNotAcceptHeaderProvided()
    {
        $request  = $this->createRequest('GET', '/invoices');
        $request  = $request->withHeader('Accept', '');
        $response = $this->runApp($request);

        $bodyString = (string)$response->getBody();
        $body       = json_decode($bodyString, true);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertContains('Request must accept media-type: application/json', $body['developerMessage']);
        $this->assertContains('Server couldn\'t provide a valid response.', $body['userMessage']);
    }

    public function testCollectionNotAcceptableWhenBadAcceptHeaderProvided()
    {
        $request  = $this->createRequest('GET', '/invoices');
        $request  = $request->withHeader('Accept', 'application/xml');
        $response = $this->runApp($request);

        $bodyString = (string)$response->getBody();
        $body       = json_decode($bodyString, true);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertContains('Request must accept media-type: application/json', $body['developerMessage']);
        $this->assertContains('Server couldn\'t provide a valid response.', $body['userMessage']);
    }

    public function testCollectionAcceptMediaTypeWildcard()
    {
        $request  = $this->createRequest('GET', '/invoices');
        $request  = $request->withHeader('Accept', '*/*');
        $response = $this->runApp($request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testCollectionAcceptMediaTypeJSON()
    {
        $request  = $this->createRequest('GET', '/invoices');
        $request  = $request->withHeader('Accept', 'application/json');
        $response = $this->runApp($request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testCollectionError500()
    {
        self::$dbh->exec('DROP TABLE invoice');

        $request  = $this->createRequest('GET', '/invoices');
        $request  = $request->withHeader('Accept', 'application/json');
        $response = $this->runApp($request);

        $bodyString = (string)$response->getBody();
        $body       = json_decode($bodyString, true);

        $this->assertEquals(500, $response->getStatusCode());
        $this->assertContains('Internal Server Error.', $body['developerMessage']);
        $this->assertContains('Unexpected error has occurred, try again later.', $body['userMessage']);
    }

    public function testCollectionBody()
    {
        $expectedBody = [
            'metadata' => [
                'resultset' => [
                    'count'  => 2,
                    'limit'  => 25,
                    'offset' => 0
                ]
            ],
            'results'  => [
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
            ]
        ];
        $request      = $this->createRequest('GET', '/invoices');
        $request      = $request->withHeader('Accept', 'application/json');
        $response     = $this->runApp($request);

        $body = (string)$response->getBody();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($expectedBody, json_decode($body, true));
    }

    public function testCollectionBodyLimitAndSort()
    {
        $expectedBody = [
            'metadata' => [
                'resultset' => [
                    'count'  => 2,
                    'limit'  => 1,
                    'offset' => 1
                ]
            ],
            'results'  => [
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
        ];
        $request      = $this->createRequest('GET', '/invoices?limit=1&offset=1&sort=tax,asc');
        $request      = $request->withHeader('Accept', 'application/json');
        $response     = $this->runApp($request);

        $body = (string)$response->getBody();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($expectedBody, json_decode($body, true));
    }

    public function testCollectionBodySanitizedInput()
    {
        $expectedBody = [
            'metadata' => [
                'resultset' => [
                    'count'  => 2,
                    'limit'  => 25,
                    'offset' => 0
                ]
            ],
            'results'  => [
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
            ]
        ];
        $request      = $this->createRequest('GET', '/invoices?sort=weirdStuff,sheeep&limit=limitless');
        $request      = $request->withHeader('Accept', 'application/json');
        $response     = $this->runApp($request);

        $body = (string)$response->getBody();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($expectedBody, json_decode($body, true));
    }
}
