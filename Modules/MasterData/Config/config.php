<?php

return [
    'name' => 'MasterData',

    'dbClient' => [
        'driver' => 'pgsql',
        'url' => env('DATABASE_URL'),
        'host' => env('DB_HOST_CLIENT', 'localhost'),
        'port' => env('DB_PORT_CLIENT', '5433'),
        'database' => env('DB_DATABASE_CLIENT', 'forge'),
        'username' => env('DB_USERNAME_CLIENT', 'forge'),
        'password' => env('DB_PASSWORD_CLIENT', 'forge'),
        'charset' => 'utf8',
        'prefix' => '',
        'prefix_indexes' => true,
        'search_path' => 'public',
        'sslmode' => 'prefer',
    ],
];
