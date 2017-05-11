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
    protected $prepareParams = [];
    protected $prepareReturn = [];

    /**
     *  constructor.
     * @param array $expectations
     */
    public function __construct(array $expectations = [])
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
        $result = new PDOStatement([]);

        $this->prepareParams[] = [$string, $options];

        if (count($this->prepareReturn) && isset($this->prepareReturn[$this->prepareCallCount])) {
            $result = $this->prepareReturn[$this->prepareCallCount];
        }

        $this->prepareCallCount++;

        return $result;
    }

    /**
     * Get parameters used for  the  specified method call
     * @param int $call
     * @return array
     * @throws \Exception if params not found for specified call
     */
    public function getPrepareParams($call)
    {
        if (!isset($this->prepareParams[$call])) {
            throw new \Exception('No params found for the current call: ' . $call);
        }

        return $this->prepareParams[$call];
    }

    /**
     * Return all the params for the past method calls
     * @return array
     */
    public function getPrepareParamsAll()
    {
        return $this->prepareParams;
    }

    /**
     * Set return values for the prepare method calls
     * @param array $returns
     */
    public function setPrepareReturn(array $returns = [])
    {
        $this->prepareReturn = $returns;
    }

    /**
     * Returns the amount of times the prepare method was called
     * @return int
     */
    public function getPrepareCallCount()
    {
        return $this->prepareCallCount;
    }

    /**
     * Reset abouts prepare method
     */
    public function resetPrepare()
    {
        $this->prepareCallCount = 0;
        $this->prepareParams    = [];
        $this->prepareReturn    = [];
    }
}
