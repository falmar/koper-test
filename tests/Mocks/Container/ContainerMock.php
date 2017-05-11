<?php
/**
 * Created by PhpStorm.
 * User: falmar
 * Date: 5/10/17
 * Time: 8:35 PM
 */

namespace KoperTest\Mocks\Container;


use Psr\Container\ContainerInterface;

class ContainerMock implements ContainerInterface
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
}
