<?php

return [
    "database" => [
        "adapter"  => "Mysql",
        'host'     => 'localhost',
        'username' => 'root',
        'password' => 'p1234x',
        'dbname'   => 'animedb',
        'charset'  => 'utf8',
        'options' => array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
        ),
    ],
    'application' => [
        'modelsDir'      => BASE_PATH.'/models/',
        'controllersDir' => BASE_PATH.'/controllers/',
        'validatorsDir'  => BASE_PATH.'/validators/',
        'libraryDir'      => BASE_PATH.'/library/',
        'baseUri'        => '/api/',
    ],

    'jwtAuth' => [
        'secretKey' => 'A POWER LEVEL OVER 9000',
        'payload' => [
            'iss' => 'api.dev',
            'exp' => 60*24*8
        ],
        'ignoreUri' => [
            '/',
            '/auth/token',
            '/auth/secure',
            //'regex:/anime:GET',
            //'regex:/episode:GET',
            //'regex:/genre:GET'
        ]
    ]
];