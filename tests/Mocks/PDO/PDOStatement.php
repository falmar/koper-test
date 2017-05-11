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
    protected $executeParams = [];
    protected $executeReturn = [];

    /**
     *  constructor.
     * @param array $expectations
     */
    public function __construct(array $expectations = [])
    {
        // set properties
        foreach ($expectations as $k => $prop) {
            $propKey = $k . 'Expectations';

            if (property_exists(__CLASS__, $propKey)) {
                $this->$propKey = $prop;
            }
        }
    }

    /**
     * briefly simulates execute method call
     * @param null $params
     * @return bool
     */
    public function execute($params = null)
    {
        $result = true;

        $this->executeParams[] = [$params];

        if (count($this->executeReturn) && isset($this->executeReturn[$this->executeCallCount])) {
            $result = $this->executeReturn[$this->executeCallCount];
        }

        $this->executeCallCount++;

        return $result;
    }

    /**
     * Get parameters used for  the  specified method call
     * @param int $call
     * @return array
     * @throws \Exception if params not found for specified call
     */
    public function getExecuteParams($call)
    {
        if (!isset($this->executeParams[$call])) {
            throw new \Exception('No params found for the current call: ' . $call);
        }

        return $this->executeParams[$call];
    }

    /**
     * Return all the params for the past method calls
     * @return array
     */
    public function getExecuteParamsAll()
    {
        return $this->executeParams;
    }

    /**
     * Set return values for the execute method calls
     * @param array $returns
     */
    public function setExecuteReturn(array $returns = [])
    {
        $this->executeReturn = $returns;
    }

    /**
     * Returns the amount of times the execute method was called
     * @return int
     */
    public function getExecuteCallCount()
    {
        return $this->executeCallCount;
    }

    /**
     * Reset abouts execute method
     */
    public function resetExecute()
    {
        $this->executeCallCount = 0;
        $this->executeParams    = [];
        $this->executeReturn    = [];
    }
}
