<?php
declare(strict_types = 1);

namespace Tests;

use Slim\App;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * This is an example class that shows how you could set up a method that
 * runs the application. Note that it doesn't cover all use-cases and is
 * tuned to the specifics of this skeleton app, so if your needs are
 * different, you'll need to change it.
 */
class BaseTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * Use middleware when running application?
     *
     * @var bool
     */
    protected $withMiddleware = true;

    /**
     * Create a new request and returns it
     * @param string $requestMethod
     * @param string $requestUri
     * @param object|array|null $requestData
     *
     * @return Request
     */
    public function createRequest($requestMethod, $requestUri, $requestData = null)
    {
        // Create a mock environment for testing with
        $environment = Environment::mock(
            [
                'REQUEST_METHOD' => $requestMethod,
                'REQUEST_URI'    => '/v1' . $requestUri
            ]
        );

        // Set up a request object based on the environment
        $request = Request::createFromEnvironment($environment);

        // Add request data, if it exists
        if (isset($requestData)) {
            $request = $request->withParsedBody($requestData);
        }

        return $request;
    }

    /**
     * Process the application given a request method and URI
     *
     * @param Request $request
     * @return Response
     */
    public function runApp(Request $request)
    {
        // Set up a response object
        $response = new Response();

        // Use the application settings
        $settings = require __DIR__ . '/../src/settings.php';

        // Instantiate the application
        $app = new App($settings);

        // Set up dependencies
        require __DIR__ . '/../src/dependencies.php';

        // Register middleware
        if ($this->withMiddleware) {
            require __DIR__ . '/../src/middleware.php';
        }

        // Register routes
        require __DIR__ . '/../src/routes.php';

        /** @var Response $response Process the application */
        $response = $app->process($request, $response);

        // Return the response
        return $response;
    }

    /**
     * @param string $SQLString
     * @return string
     */
    public function inlineSQLString(string $SQLString): string
    {
        return trim(preg_replace(['/\s{1,}/', '/\s{1,};/'], [' ', ';'], $SQLString));
    }

    /**
     * creates a PDO instance to help model integration tests
     *
     * @return \PDO
     */
    public function getPDO()
    {
        $db = [
            'host'     => getenv('DB_TEST_HOST'),
            'name'     => getenv('DB_TEST_NAME'),
            'user'     => getenv('DB_TEST_USER'),
            'password' => getenv('DB_TEST_PASSWORD'),
        ];

        $dbh = new \PDO("pgsql:host={$db['host']};dbname={$db['name']}", $db['user'], $db['password']);

        $dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $dbh->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
        $dbh->setAttribute(\PDO::ATTR_STRINGIFY_FETCHES, false);
        $dbh->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);

        return $dbh;
    }
}
