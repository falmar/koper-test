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

    protected $bindColumnCallCount = 0;
    protected $bindColumnParams = [];
    protected $bindColumnReturn = [];
    protected $bindColumnReference = [];

    protected $bindValueCallCount = 0;
    protected $bindValueParams = [];
    protected $bindValueReturn = [];

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
            return [];
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
            return [];
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

    // ------------------------- bindColumn

    /**
     * briefly simulates bindColumn method call
     * @param mixed $column
     * @param mixed $param
     * @param int $type
     * @param int $maxLength
     * @param mixed $driverData
     * @return bool
     */
    public function bindColumn($column, &$param, $type = null, $maxLength = null, $driverData = null)
    {
        $result = false;

        $this->bindColumnParams[] = [$column, $param, $type = null, $maxLenth = null, $driverData = null];

        if (count($this->bindColumnReturn) && isset($this->bindColumnReturn[$this->bindColumnCallCount])) {
            $result = $this->bindColumnReturn[$this->bindColumnCallCount];
        }

        if (count($this->bindColumnReference) && isset($this->bindColumnReference[$this->bindColumnCallCount])) {
            list($param) = $this->bindColumnReference[$this->bindColumnCallCount];
        }

        $this->bindColumnCallCount++;

        return $result;
    }

    /**
     * Get parameters used for  the  specified method call
     * @param int $call
     * @return array
     * @throws \Exception if params not found for specified call
     */
    public function getBindColumnParams($call)
    {
        if (!isset($this->bindColumnParams[$call])) {
            return [];
        }

        return $this->bindColumnParams[$call];
    }

    /**
     * Return all the params for the past method calls
     * @return array
     */
    public function getBindColumnParamsAll()
    {
        return $this->bindColumnParams;
    }

    /**
     * Set return values for the bindColumn method calls
     * @param array $returns
     */
    public function setBindColumnReturn(array $returns = [])
    {
        $this->bindColumnReturn = $returns;
    }

    /**
     * Returns the amount of times the bindColumn method was called
     * @return int
     */
    public function getBindColumnCallCount()
    {
        return $this->bindColumnCallCount;
    }

    /**
     * Reset abouts bindColumn method
     */
    public function resetBindColumn()
    {
        $this->bindColumnCallCount = 0;
        $this->bindColumnParams    = [];
        $this->bindColumnReturn    = [];
    }

    // ------------------------- bindValue

    /**
     * briefly simulates bindValue method call
     * @param mixed $param
     * @param mixed $value
     * @param int $type
     * @return bool
     */
    public function bindValue($param, $value, $type = \PDO::PARAM_STR)
    {
        $result = false;

        $this->bindValueParams[] = [$param, $value, $type];

        if (count($this->bindValueReturn) && isset($this->bindValueReturn[$this->bindValueCallCount])) {
            $result = $this->bindValueReturn[$this->bindValueCallCount];
        }

        $this->bindValueCallCount++;

        return $result;
    }

    /**
     * Get parameters used for  the  specified method call
     * @param int $call
     * @return array
     * @throws \Exception if params not found for specified call
     */
    public function getBindValueParams($call)
    {
        if (!isset($this->bindValueParams[$call])) {
            return [];
        }

        return $this->bindValueParams[$call];
    }

    /**
     * Return all the params for the past method calls
     * @return array
     */
    public function getBindValueParamsAll()
    {
        return $this->bindValueParams;
    }

    /**
     * Set return values for the bindValue method calls
     * @param array $returns
     */
    public function setBindValueReturn(array $returns = [])
    {
        $this->bindValueReturn = $returns;
    }

    /**
     * Returns the amount of times the bindValue method was called
     * @return int
     */
    public function getBindValueCallCount()
    {
        return $this->bindValueCallCount;
    }

    /**
     * Reset abouts bindValue method
     */
    public function resetBindValue()
    {
        $this->bindValueCallCount = 0;
        $this->bindValueParams    = [];
        $this->bindValueReturn    = [];
    }
}
