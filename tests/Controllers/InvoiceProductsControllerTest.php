<?php
declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: falmar
 * Date: 5/15/24
 * Time: 05:10 PM
 */

namespace Tests\Controllers;


use KoperTest\Migrations\Invoice;
use KoperTest\Migrations\InvoiceProduct;
use KoperTest\Migrations\Product;
use Slim\Http\Request;
use Tests\BaseTestCase;


class InvoiceProductsControllerTest extends BaseTestCase
{
    /** @var \PDO */
    protected static $dbh = null;
    /** @var InvoiceProduct */
    protected static $migration = null;
    /** @var Product */
    protected static $migrationProduct = null;
    /** @var Invoice */
    protected static $migrationInvoice = null;

    public static function setUpBeforeClass()
    {
        self::$dbh              = self::getPDO();
        self::$migration        = new InvoiceProduct(self::$dbh);
        self::$migrationProduct = new Product(self::$dbh);
        self::$migrationInvoice = new Invoice(self::$dbh);
    }

    public function setUp()
    {
        self::$migrationProduct->up();
        self::$migrationProduct->seed();

        self::$migrationInvoice->up();
        self::$migrationInvoice->seed();

        self::$migration->up();
        self::$migration->seed();
    }

    public function tearDown()
    {
        self::$migration->down();
        self::$migrationProduct->down();
        self::$migrationInvoice->down();
    }

    public function testGetJSONContentTypeResponse()
    {
        $request  = $this->createRequest('GET', '/invoices/1/products/1');
        $request  = $request->withHeader('Accept', '');
        $response = $this->runApp($request);

        $this->assertContains('application/json', $response->getHeaderLine('Content-Type'));
    }

    public function testGetNotAcceptableWhenNotAcceptHeaderProvided()
    {
        $request  = $this->createRequest('GET', '/invoices/1/products/1');
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
        $request  = $this->createRequest('GET', '/invoices/1/products/1');
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
        $request  = $this->createRequest('GET', '/invoices/1/products/1');
        $request  = $request->withHeader('Accept', '*/*');
        $response = $this->runApp($request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testGetAcceptMediaTypeJSON()
    {
        $request  = $this->createRequest('GET', '/invoices/1/products/1');
        $request  = $request->withHeader('Accept', 'application/json');
        $response = $this->runApp($request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testGetNotFound()
    {
        $request  = $this->createRequest('GET', '/invoices/1/products/5');
        $request  = $request->withHeader('Accept', 'application/json');
        $response = $this->runApp($request);

        $bodyString = (string)$response->getBody();
        $body       = json_decode($bodyString, true);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertContains('InvoiceProduct (1, 5) does not exist', $body['developerMessage']);
        $this->assertContains('Invoice does not exist', $body['userMessage']);
    }

    public function testGetBody()
    {
        $expectedBody = [
            'invoice_id' => 1,
            'product_id' => 1,
            'price'      => 12.22,
            'quantity'   => 2
        ];
        $request      = $this->createRequest('GET', '/invoices/1/products/1');
        $request      = $request->withHeader('Accept', 'application/json');
        $response     = $this->runApp($request);

        $body = (string)$response->getBody();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($expectedBody, json_decode($body, true));
    }

    public function testGetError500()
    {
        self::$dbh->exec('DROP TABLE invoice_products');

        $request  = $this->createRequest('GET', '/invoices/1/products/1');
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
        $request  = $this->createRequest('POST', '/invoices/1/products');
        $response = $this->runApp($request);

        $this->assertContains('application/json', $response->getHeaderLine('Content-Type'));
    }

    public function testAddNotAcceptableWithBadAcceptHeader()
    {
        $request  = $this->createRequest('POST', '/invoices/1/products');
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
        $request  = $this->createRequest('POST', '/invoices/1/products');
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
        $request  = $this->createRequest('POST', '/invoices/1/products');
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
        $request  = $this->createRequest('POST', '/invoices/1/products', $body);
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
            'invoice_id' => 1,
            'product_id' => 1,
            'price'      => 'not-numeric',
            'quantity'   => 2
        ];
        $request  = $this->createRequest('POST', '/invoices/1/products', $body);
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
        self::$dbh->exec('DROP TABLE invoice_products');

        $body     = [
            'invoice_id' => 1,
            'product_id' => 1,
            'price'      => 12.22,
            'quantity'   => 2
        ];
        $request  = $this->createRequest('POST', '/invoices/1/products', $body);
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
            'invoice_id' => 2,
            'product_id' => 2,
            'price'      => 12.22,
            'quantity'   => 2
        ];
        $request = $this->createRequest('POST', '/invoices/2/products', $body);
        $request = $request->withHeader('Accept', 'application/json');
        $request = $request->withHeader('Content-Type', 'application/json');

        $response = $this->runApp($request);

        $this->assertEquals(201, $response->getStatusCode());
    }

    public function testAddAcceptMediaTypeWildcard()
    {
        $body    = [
            'invoice_id' => 2,
            'product_id' => 2,
            'price'      => 12.22,
            'quantity'   => 2
        ];
        $request = $this->createRequest('POST', '/invoices/2/products', $body);
        $request = $request->withHeader('Accept', '*/*');
        $request = $request->withHeader('Content-Type', 'application/json');

        $response = $this->runApp($request);

        $this->assertEquals(201, $response->getStatusCode());
    }

    public function testAddBodyResponse()
    {
        $body     = [
            'invoice_id' => 2,
            'product_id' => 2,
            'price'      => 12.22,
            'quantity'   => 2
        ];
        $request  = $this->createRequest('POST', '/invoices/2/products', $body);
        $request  = $request->withHeader('Accept', 'application/json');
        $request  = $request->withHeader('Content-Type', 'application/json');
        $response = $this->runApp($request);

        $bodyString   = (string)$response->getBody();
        $responseBody = json_decode($bodyString, true);

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertArraySubset($body, $responseBody);
    }

    // update entity

    public function testUpdateResponseContentTypeJSON()
    {
        $request  = $this->createRequest('PUT', '/invoices/1/products/1');
        $response = $this->runApp($request);

        $this->assertContains('application/json', $response->getHeaderLine('Content-Type'));
    }

    public function testUpdateNotAcceptableWithBadAcceptHeader()
    {
        $request  = $this->createRequest('PUT', '/invoices/1/products/1');
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
        $request  = $this->createRequest('PUT', '/invoices/1/products/1');
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
        $request  = $this->createRequest('PUT', '/invoices/1/products/1');
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
        $request  = $this->createRequest('PUT', '/invoices/1/products/1', $body);
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
            'product_id' => 2,
            'price'      => 'not-numeric',
            'quantity'   => 2
        ];
        $request  = $this->createRequest('PUT', '/invoices/1/products/2', $body);
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
            'product_id' => 2,
            'price'      => 500,
            'quantity'   => 2
        ];
        $request  = $this->createRequest('PUT', '/invoices/1/products/5', $body);
        $request  = $request->withHeader('Accept', 'application/json');
        $request  = $request->withHeader('Content-Type', 'application/json');
        $response = $this->runApp($request);

        $bodyString   = (string)$response->getBody();
        $responseBody = json_decode($bodyString, true);

        $this->assertEquals(500, $response->getStatusCode());
        $this->assertContains(
            'InvoiceProduct (1, 5) does not exist. Due to database capabilities new row can\'t be added.',
            $responseBody['developerMessage']
        );
        $this->assertContains('Unexpected error has occurred, try again later.', $responseBody['userMessage']);
    }

    public function testUpdateError500()
    {
        self::$dbh->exec('DROP TABLE invoice_products');

        $body     = [
            'product_id' => 2,
            'price'      => 500,
            'quantity'   => 2
        ];
        $request  = $this->createRequest('PUT', '/invoices/1/products/1', $body);
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
            'product_id' => 1,
            'price'      => 500,
            'quantity'   => 1
        ];
        $request = $this->createRequest('PUT', '/invoices/1/products/1', $body);
        $request = $request->withHeader('Accept', 'application/json');
        $request = $request->withHeader('Content-Type', 'application/json');

        $response = $this->runApp($request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testUpdateAcceptMediaTypeWildcard()
    {
        $body    = [
            'product_id' => 1,
            'price'      => 500,
            'quantity'   => 1
        ];
        $request = $this->createRequest('PUT', '/invoices/1/products/1', $body);
        $request = $request->withHeader('Accept', '*/*');
        $request = $request->withHeader('Content-Type', 'application/json');

        $response = $this->runApp($request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testUpdateBodyResponse()
    {
        $body     = [
            'product_id' => 1,
            'price'      => 500,
            'quantity'   => 1
        ];
        $request  = $this->createRequest('PUT', '/invoices/1/products/1', $body);
        $request  = $request->withHeader('Accept', 'application/json');
        $request  = $request->withHeader('Content-Type', 'application/json');
        $response = $this->runApp($request);

        $bodyString   = (string)$response->getBody();
        $responseBody = json_decode($bodyString, true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArraySubset($body, $responseBody);
    }

    // delete entity

    public function testDeleteBodyResponseNotExistentRow()
    {
        $request  = $this->createRequest('DELETE', '/invoices/1/products/5');
        $response = $this->runApp($request);

        $bodyString   = (string)$response->getBody();
        $responseBody = json_decode($bodyString, true);

        $this->assertEquals(500, $response->getStatusCode());
        $this->assertContains('InvoiceProduct (1, 5) does not exist.', $responseBody['developerMessage']);
        $this->assertContains('Unexpected error has occurred, try again later.', $responseBody['userMessage']);
    }

    public function testDeleteError500()
    {
        self::$dbh->exec('DROP TABLE invoice_products');

        $request  = $this->createRequest('DELETE', '/invoices/1/products/1');
        $response = $this->runApp($request);

        $bodyString = (string)$response->getBody();
        $body       = json_decode($bodyString, true);

        $this->assertEquals(500, $response->getStatusCode());
        $this->assertContains('Internal Server Error.', $body['developerMessage']);
        $this->assertContains('Unexpected error has occurred, try again later.', $body['userMessage']);
    }

    public function testDeleteBodyResponse()
    {
        $request  = $this->createRequest('DELETE', '/invoices/1/products/1');
        $response = $this->runApp($request);

        $body = (string)$response->getBody();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEmpty($body);
    }

    // test collection

    public function testCollectionJSONContentTypeResponse()
    {
        $request  = $this->createRequest('GET', '/invoices/1/products');
        $request  = $request->withHeader('Accept', '');
        $response = $this->runApp($request);

        $this->assertContains('application/json', $response->getHeaderLine('Content-Type'));
    }

    public function testCollectionNotAcceptableWhenNotAcceptHeaderProvided()
    {
        $request  = $this->createRequest('GET', '/invoices/1/products');
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
        $request  = $this->createRequest('GET', '/invoices/1/products');
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
        $request  = $this->createRequest('GET', '/invoices/1/products');
        $request  = $request->withHeader('Accept', '*/*');
        $response = $this->runApp($request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testCollectionAcceptMediaTypeJSON()
    {
        $request  = $this->createRequest('GET', '/invoices/1/products');
        $request  = $request->withHeader('Accept', 'application/json');
        $response = $this->runApp($request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testCollectionError500()
    {
        self::$dbh->exec('DROP TABLE invoice_products');

        $request  = $this->createRequest('GET', '/invoices/1/products');
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
                    'invoice_id' => 1,
                    'product_id' => 2,
                    'price'      => 1049.99,
                    'quantity'   => 1
                ],
                [
                    'invoice_id' => 1,
                    'product_id' => 1,
                    'price'      => 12.22,
                    'quantity'   => 2
                ]
            ]
        ];
        $request      = $this->createRequest('GET', '/invoices/1/products');
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
                    'invoice_id' => 1,
                    'product_id' => 2,
                    'price'      => 1049.99,
                    'quantity'   => 1
                ]
            ]
        ];
        $request      = $this->createRequest('GET', '/invoices/1/products?limit=1&offset=1&sort=quantity,desc');
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
                    'invoice_id' => 1,
                    'product_id' => 2,
                    'price'      => 1049.99,
                    'quantity'   => 1
                ],
                [
                    'invoice_id' => 1,
                    'product_id' => 1,
                    'price'      => 12.22,
                    'quantity'   => 2
                ]
            ]
        ];
        $request      = $this->createRequest('GET', '/invoices/1/products?sort=weirdStuff,sheeep&limit=limitless');
        $request      = $request->withHeader('Accept', 'application/json');
        $response     = $this->runApp($request);

        $body = (string)$response->getBody();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($expectedBody, json_decode($body, true));
    }
}
