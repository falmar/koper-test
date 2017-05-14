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

    public function getPDO()
    {
        $db = [
            'host'     => getenv('DB_TEST_HOST'),
            'name'     => getenv('DB_TEST_NAME'),
            'user'     => getenv('DB_TEST_USER'),
            'password' => getenv('DB_TEST_PASSWORD'),
        ];

        $dbh = new \PDO("pgsql:host={$db['host']};dbname={$db['name']}", $db['user'], $db['password']);

        $dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $dbh->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
        $dbh->setAttribute(\PDO::ATTR_STRINGIFY_FETCHES, false);
        $dbh->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);

        return $dbh;
    }
}
