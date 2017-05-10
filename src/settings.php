<?php
$dotEnv = new Dotenv\Dotenv(__DIR__ . '/../');
$dotEnv->load();

return [
    'settings' => [
        // set to false in production
        'displayErrorDetails'    => getenv('SLIM_ENV') !== 'production',
        // Allow the web server to send the content-length header
        'addContentLengthHeader' => true,

        // database
        'db'                     => [
            'host'     => getenv('DB_HOST'),
            'name'     => getenv('DB_NAME'),
            'user'     => getenv('DB_USER'),
            'password' => getenv('DB_PASSWORD'),
        ],

        // Monolog settings
        'logger'                 => [
            'name'  => 'slim-app',
            'path'  => __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],
    ],
];
