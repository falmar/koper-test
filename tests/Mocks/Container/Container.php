<?php
/**
 * Created by PhpStorm.
 * User: falmar
 * Date: 5/10/17
 * Time: 8:35 PM
 */

namespace Tests\Mocks\Container;


use Psr\Container\ContainerInterface;


class Container implements ContainerInterface
{
    protected $data = [];
    protected $called = [];

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function get($id)
    {
        if (!isset($this->data[$id])) {
            throw new \Exception("Container id '{$id}' does not exist");
        }

        if (is_callable($this->data[$id]) && !isset($this->called[$id])) {
            $this->data[$id]   = $this->data[$id]($this);
            $this->called[$id] = true;
        }

        return $this->data[$id];
    }

    /**
     * @param string $id
     * @return bool
     */
    public function has($id)
    {
        return !isset($this->data[$id]);
    }
}
