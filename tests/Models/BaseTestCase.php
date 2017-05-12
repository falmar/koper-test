<?php
/**
 * Created by PhpStorm.
 * User: falmar
 * Date: 5/12/17
 * Time: 4:28 PM
 */

namespace Tests\Models;


class BaseTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $SQLString
     * @return string
     */
    public function inlineSQLString(string $SQLString): string
    {
        return trim(preg_replace(['/\s{1,}/', '/\s{1,};/'], [' ', ';'], $SQLString));
    }
}
