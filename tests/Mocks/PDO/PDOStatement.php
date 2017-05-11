<?php
/**
 * Created by PhpStorm.
 * User: falmar
 * Date: 5/10/17
 * Time: 8:34 PM
 */

namespace KoperTest\Mocks\PDO;


class PDOStatement extends \PDOStatement
{
    protected $executeCallCount = 0;
    protected $executeCalls = [];
    protected $executeReturn = [];


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

    public function execute($params = null)
    {
        $result = true;

        $this->executeCallCount++;

        return $result;
    }

    public function getExecuteCallCount()
    {
        return $this->executeCallCount;
    }
}
