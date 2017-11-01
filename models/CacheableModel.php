<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017-08-01
 * Time: 1:12 AM
 */


class CacheableModel extends \Phalcon\Mvc\Model
{
    /**
     * Implement a method that returns a string key based
     * on the query parameters
     */
    public static function _createKey($parameters)
    {
        $uniqueKey = [];

        foreach ($parameters as $key => $value) {
            if (is_scalar($value)) {
                $uniqueKey[] = $key . ":" . $value;
            } elseif (is_array($value)) {
                $uniqueKey[] = $key . ":[" . self::_createKey($value) . "]";
            }
        }

        return join(",", $uniqueKey);
    }

    public static function find($parameters = null)
    {
        // Convert the parameters to an array
        if (!is_array($parameters)) {
            $parameters = [$parameters];
        }

        // Check if a cache key wasn't passed
        // and create the cache parameters
        if (!isset($parameters["cache"])) {
            $parameters["cache"] = [
                "key"      => self::_createKey($parameters),
                "lifetime" => 3600,
            ];
        }

        return parent::find($parameters);
    }

    public static function findFirst($parameters = null)
    {
        // Convert the parameters to an array
        if (!is_array($parameters)) {
            $parameters = [$parameters];
        }

        // Check if a cache key wasn't passed
        // and create the cache parameters
        if (!isset($parameters["cache"])) {
            $parameters["cache"] = [
                "key"      => self::_createKey($parameters),
                "lifetime" => 300,
            ];
        }

        return parent::findFirst($parameters);
    }

}