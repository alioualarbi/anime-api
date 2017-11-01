<?php

use Phalcon\Config;
use Phalcon\Db\Adapter\Pdo\Mysql as PdoMysql;
use Phalcon\Cache\Backend\Apcu;
use Phalcon\Cache\Frontend\Data as FrontendData;
use Phalcon\Cache\Backend\Memcache as BackendMemcache;

//set config
$di->setShared(
    "config",
    function () {
        $configData = require "config/config.php";
        return new Config($configData);
    }
);

//set db
$di->setShared(
    "db",
    function () {
        return new PdoMysql(
            $this->get('config')->database->toArray()
        );
    }
);
// Set the models cache service
$di->setShared(
    "cache",
    function () {
        // Cache data for one day by default
        $frontCache = new FrontendData(
            [
                "lifetime" => 86400,
            ]
        );

        // Memcached connection settings
        $cache = new BackendMemcache(
            $frontCache,
            [
                "host" => "localhost",
                "port" => "11211",
            ]
        );

        return $cache;
    }
);


// Set the models cache service
$di->setShared(
    "modelsCache",
    function () {
        // Cache data for one day by default
        $frontCache = new FrontendData(
            [
                "lifetime" => 86400,
            ]
        );

        // Memcached connection settings
        $cache = new BackendMemcache(
            $frontCache,
            [
                "host" => "localhost",
                "port" => "11211",
            ]
        );

        return $cache;
    }
);

/*
$di->set(
    "cache",
    function () {
        // Cache data for one day by default
        $frontCache = new FrontendData(
            [
                "lifetime" => 86400,
            ]
        );
        $cache = new Apcu(
            $frontCache,
            [
                "prefix" => "app-data",
            ]
        );
        return $cache;
    }
);
*/