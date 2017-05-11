<?php
/**
 * Created by PhpStorm.
 * User: falmar
 * Date: 5/10/17
 * Time: 8:27 PM
 */

namespace KoperTest\Mocks\PDO;


class PDO extends \PDO
{
    // prepare
    protected $prepareCallCount = 0;
    protected $prepareCalls = [];
    protected $prepareReturn = [];

    /**
     *  constructor.
     * @param array $expectations
     */
    public function __construct(array $expectations)
    {
        // set properties
        foreach ($expectations as $k => $prop) {
            if (property_exists(__CLASS__, $k)) {
                $this->$k = $prop;
            }
        }
    }

    public function prepare($string, $options = null)
    {
        if (count($this->prepareCalls) && isset($this->prepareCalls[$this->prepareCallCount])) {
            $expectedParams = $this->prepareCalls[$this->prepareCallCount];

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
        }


        if (count($this->prepareReturn) && isset($this->prepareReturn[$this->prepareCallCount])) {
            $this->prepareCallCount++;

            return $this->prepareReturn[$this->prepareCallCount];
        }

        $this->prepareCallCount++;

        return new PDOStatement([]);
    }

    public function getPrepareCallCount()
    {
        return $this->prepareCallCount;
    }
}
