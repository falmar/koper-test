<?php
/**
 * Created by PhpStorm.
 * User: falmar
 * Date: 5/10/17
 * Time: 5:36 PM
 */

namespace tests\Functional\Products;


use KoperTest\Controllers\Products\ProductList;
use Psr\Container\ContainerInterface;


class ProductListTest extends \PHPUnit_Framework_TestCase
{
    public function testGetDataWithEmptyParams()
    {
        // PDO Expectations
        $dbh = $this->getPDOMock([
            // prepare calls and arguments
            'prepare' => [
                ['SELECT id, name FROM products;', null]
            ]
        ]);
        // PDOStatement Expectations
        $dbh->setStatement($this->getPDOStatementMock([]));
        $container   = $this->getContainerMock(['dbh' => $dbh]);
        $productList = new ProductList($container);
        $params      = [
            'limit'     => 0,
            'offset'    => 0,
            'sortField' => '',
            'sortOrder' => ''
        ];

        // test data
        $productList->getData($params);

        $this->equalTo($dbh->getPrepareCallCount(), 1);
    }

    // helper
    /**
     * @param array $expectations
     * @return \PDO
     */
    protected function getPDOMock($expectations)
    {
        $dbh = new class ($expectations) extends \PDO
        {
            // amount
            protected $prepareCalls = 0;
            // prepare argument calls
            protected $prepareExpectations = [];
            protected $stmt = null;

            /**
             *  constructor.
             * @param array $expectations
             */
            public function __construct(array $expectations)
            {
                // set properties
                foreach ($expectations as $k => $prop) {
                    $propKey = $k . 'Expectations';

                    if (property_exists(__CLASS__, $propKey)) {
                        $this->$propKey = $prop;
                    }
                }
            }

            public function prepare($string, $options = null)
            {
                if (!$this->stmt instanceof \PDOStatement) {
                    throw new \Exception('No PDOStatementMock has been set');
                }

                if (!count($this->prepareExpectations)) {
                    throw new \Exception('No prepareExpectations has been set');
                }

                if (!isset($this->prepareExpectations[$this->prepareCalls])) {
                    throw new \Exception("No expectation available for the current prepare call: {$this->prepareCalls}");
                }

                $expectedParams = $this->prepareExpectations[$this->prepareCalls];

                if (count($expectedParams) !== 2) {
                    throw new \Exception("Invalid expectation arguments amount for prepare call");
                }

                if ($expectedParams[0] !== $string) {
                    $expected = var_export($expectedParams[0], true);
                    $received = var_export($string, true);

                    throw new \Exception("
                        \n Invalid 'string' argument for prepare call: \n Expected: {$expected} \n Received: {$received}"
                    );
                }

                if ($expectedParams[1] !== $options) {
                    $expected = var_export($expectedParams[1], true);
                    $received = var_export($options, true);

                    throw new \Exception("
                        \n Invalid 'options' argument for prepare call: \n Expected: {$expected} \n Received: {$received}"
                    );
                }

                $this->prepareCalls++;

                return $this->stmt;
            }

            public function getPrepareCallCount()
            {
                return $this->prepareCalls;
            }

            public function setStatement(\PDOStatement $stmt)
            {
                $this->stmt = $stmt;
            }
        };

        return $dbh;
    }

    protected function getPDOStatementMock($expectations)
    {
        $stmt = new class ($expectations) extends \PDOStatement
        {
            /**
             *  constructor.
             * @param array $expectations
             */
            public function __construct(array $expectations)
            {
                // set properties
                foreach ($expectations as $k => $prop) {
                    $propKey = $k . 'Expectations';

                    if (property_exists(__CLASS__, $propKey)) {
                        $this->$propKey = $prop;
                    }
                }
            }
        };

        return $stmt;
    }

    /**
     * @param array $data
     * @return ContainerInterface
     */
    protected function getContainerMock($data)
    {
        $container = new class ($data) implements ContainerInterface
        {
            protected $data = [];

            public function __construct($data)
            {
                $this->data = $data;
            }

            public function get($id)
            {
                if (!isset($this->data[$id])) {
                    throw new \Exception("Container id '{$id}' does not exist");
                }

                return $this->data[$id];
            }

            public function has($id)
            {

            }
        };

        return $container;
    }
}
