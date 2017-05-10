<?php
// DIC configuration

$container = $app->getContainer();

// database
/**
 * @var \Slim\Container $c
 * @return PDO
 */
$container['dbh'] = function ($c) {
    $db  = $c->get('settings')['db'];
    $dbh = new PDO("pgsql:host={$db['host']};dbname={$db['name']}", $db['user'], $db['password']);

    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $dbh->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);
    $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

    return $dbh;
};

// monolog
/**
 * @var \Slim\Container $c
 * @return Monolog\Logger
 */
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger   = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));

    return $logger;
};
