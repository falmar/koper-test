<?php
/**
 * Created by PhpStorm.
 * User: falmar
 * Date: 5/10/17
 * Time: 8:34 PM
 */

namespace KoperTest\Mocks\PDO;


class PDOStatementMock extends \PDOStatement
{
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
}
