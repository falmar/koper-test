<?php
declare(strict_types = 1);
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
    protected $executeThrowable = [];

    protected $fetchCallCount = 0;
    protected $fetchParams = [];
    protected $fetchReturn = [];
    protected $fetchThrowable = [];

    protected $bindColumnCallCount = 0;
    protected $bindColumnParams = [];
    protected $bindColumnReturn = [];
    protected $bindColumnReference = [];
    protected $bindColumnThrowable = [];

    protected $bindValueCallCount = 0;
    protected $bindValueParams = [];
    protected $bindValueReturn = [];
    protected $bindValueThrowable = [];

    protected $rowCountCallCount = 0;
    protected $rowCountParams = [];
    protected $rowCountReturn = [];
    protected $rowCountThrowable = [];

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

        if (
            count($this->executeThrowable) &&
            isset($this->executeThrowable[$this->executeCallCount - 1]) &&
            is_callable($this->executeThrowable[$this->executeCallCount - 1])
        ) {
            $this->executeThrowable[$this->executeCallCount - 1]($params);
        }

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

        if (
            count($this->fetchThrowable) &&
            isset($this->fetchThrowable[$this->fetchCallCount - 1]) &&
            is_callable($this->fetchThrowable[$this->fetchCallCount - 1])
        ) {
            $this->fetchThrowable[$this->fetchCallCount - 1]($fetchStyle, $orientation, $offset);
        }

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

        $this->bindColumnParams[] = [$column, $param, $type, $maxLength, $driverData];

        if (count($this->bindColumnReturn) && isset($this->bindColumnReturn[$this->bindColumnCallCount])) {
            $result = $this->bindColumnReturn[$this->bindColumnCallCount];
        }

        if (count($this->bindColumnReference) && isset($this->bindColumnReference[$this->bindColumnCallCount])) {
            list($param) = $this->bindColumnReference[$this->bindColumnCallCount];
        }

        $this->bindColumnCallCount++;

        if (
            count($this->bindColumnThrowable) &&
            isset($this->bindColumnThrowable[$this->bindColumnCallCount - 1]) &&
            is_callable($this->bindColumnThrowable[$this->bindColumnCallCount - 1])
        ) {
            $this->bindColumnThrowable[$this->bindColumnCallCount - 1]($column, $param, $type, $maxLength, $driverData);
        }

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

        if (
            count($this->bindValueThrowable) &&
            isset($this->bindValueThrowable[$this->bindValueCallCount - 1]) &&
            is_callable($this->bindValueThrowable[$this->bindValueCallCount - 1])
        ) {
            $this->bindValueThrowable[$this->bindValueCallCount - 1]($param, $value, $type);
        }

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

    // ------------------------- rowCount

    /**
     * briefly simulates rowCount method call
     * @return int
     */
    public function rowCount()
    {
        $result = false;

        $this->rowCountParams[] = [];

        if (count($this->rowCountReturn) && isset($this->rowCountReturn[$this->rowCountCallCount])) {
            $result = $this->rowCountReturn[$this->rowCountCallCount];
        }

        $this->rowCountCallCount++;

        if (
            count($this->rowCountThrowable) &&
            isset($this->rowCountThrowable[$this->rowCountCallCount - 1]) &&
            is_callable($this->rowCountThrowable[$this->rowCountCallCount - 1])
        ) {
            $this->rowCountThrowable[$this->rowCountCallCount - 1]();
        }

        return $result;
    }

    /**
     * Get parameters used for  the  specified method call
     * @param int $call
     * @return array
     * @throws \Exception if params not found for specified call
     */
    public function getRowCountParams($call)
    {
        if (!isset($this->rowCountParams[$call])) {
            return [];
        }

        return $this->rowCountParams[$call];
    }

    /**
     * Return all the params for the past method calls
     * @return array
     */
    public function getRowCountParamsAll()
    {
        return $this->rowCountParams;
    }

    /**
     * Set return values for the rowCount method calls
     * @param array $returns
     */
    public function setRowCountReturn(array $returns = [])
    {
        $this->rowCountReturn = $returns;
    }

    /**
     * Returns the amount of times the rowCount method was called
     * @return int
     */
    public function getRowCountCallCount()
    {
        return $this->rowCountCallCount;
    }

    /**
     * Reset abouts rowCount method
     */
    public function resetRowCount()
    {
        $this->rowCountCallCount = 0;
        $this->rowCountParams    = [];
        $this->rowCountReturn    = [];
    }
}
