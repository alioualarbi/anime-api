<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017-08-21
 * Time: 12:16 AM
 */
class Helper
{

    public $cache;
    public $mm;
    public $di;
    public $qs;
    public $db;

    /**
     * Helper constructor.
     * Helps access DI in static methods.
     */
    public function __construct()
    {
        $this->di = \Phalcon\DI::getDefault();
        $this->db = $this->di['db'];
        $this->cache = $this->di['cache'];
        $this->mm = $this->di['modelsManager'];
        $this->qs = $this->di['request']->getQuery();
        unset($this->qs['_url']);
        unset($this->qs['token']);
    }

    public static function parseID($remove,$string){
        $string = explode('/',str_replace($remove,'',$string));
        return $string[0];
    }

    public static function createKey($parameters)
    {
        $uniqueKey = [];

        foreach ($parameters as $key => $value) {
            if (is_scalar($value)) {
                $uniqueKey[] = $key . ":" . $value;
            } elseif (is_array($value)) {
                $uniqueKey[] = $key . ":[" . self::createKey($value) . "]";
            }
        }

        return join(",", $uniqueKey);
    }
}