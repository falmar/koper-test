<?php
/**
 * Created by PhpStorm.
 * User: falmar
 * Date: 5/10/17
 * Time: 8:34 PM
 */

namespace Tests\Mocks\PDO;


class PDOStatement extends \PDOStatement
{
    protected $executeCallCount = 0;
    protected $executeParams = [];
    protected $executeReturn = [];

    protected $fetchCallCount = 0;
    protected $fetchParams = [];
    protected $fetchReturn = [];

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

    // ------------------------- execute

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

    // ------------------------- fetch

    /**
     * briefly simulates fetch method call
     * @param null $fetchStyle
     * @param int $orientation
     * @param int $offset
     * @return mixed
     */
    public function fetch($fetchStyle = null, $orientation = \PDO::FETCH_ORI_NEXT, $offset = 0)
    {
        $result = false;

        $this->fetchParams[] = [$fetchStyle, $orientation, $offset];

        if (count($this->fetchReturn) && isset($this->fetchReturn[$this->fetchCallCount])) {
            $result = $this->fetchReturn[$this->fetchCallCount];
        }

        $this->fetchCallCount++;

        return $result;
    }

    /**
     * Get parameters used for  the  specified method call
     * @param int $call
     * @return array
     * @throws \Exception if params not found for specified call
     */
    public function getFetchParams($call)
    {
        if (!isset($this->fetchParams[$call])) {
            throw new \Exception('No params found for the current call: ' . $call);
        }

        return $this->fetchParams[$call];
    }

    /**
     * Return all the params for the past method calls
     * @return array
     */
    public function getFetchParamsAll()
    {
        return $this->fetchParams;
    }

    /**
     * Set return values for the fetch method calls
     * @param array $returns
     */
    public function setFetchReturn(array $returns = [])
    {
        $this->fetchReturn = $returns;
    }

    /**
     * Returns the amount of times the fetch method was called
     * @return int
     */
    public function getFetchCallCount()
    {
        return $this->fetchCallCount;
    }

    /**
     * Reset abouts fetch method
     */
    public function resetFetch()
    {
        $this->fetchCallCount = 0;
        $this->fetchParams    = [];
        $this->fetchReturn    = [];
    }
}
