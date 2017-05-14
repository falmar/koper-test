<?php
/**
 * Created by PhpStorm.
 * User: falmar
 * Date: 5/14/17
 * Time: 2:24 PM
 */

require(__DIR__ . '/../vendor/autoload.php');

$dotEnv = new \Dotenv\Dotenv(__DIR__ . '/../');
$dotEnv->load();
