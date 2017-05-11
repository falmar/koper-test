<?php
/**
 * Created by PhpStorm.
 * User: falmar
 * Date: 5/10/17
 * Time: 8:35 PM
 */

namespace KoperTest\Mocks\Container;


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

        $callable = is_callable($this->data[$id]);

        if ($callable && !isset($this->called[$id])) {
            $this->data[$id]   = $this->data[$id]();
            $this->called[$id] = true;
        }

        return $this->data[$id];
    }

    public function has($id)
    {

    }
}
