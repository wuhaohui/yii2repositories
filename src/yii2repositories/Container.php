<?php

namespace whh\yii2repositories;
use yii\base\InvalidArgumentException;

/**
 * 容器
 * Class Container
 * @package common
 */
class Container
{
    private static $services = array();
    /**
     * @param $class
     * @return mixed
     */
    public static function creation($class)
    {
        $className = $class;
        if (!class_exists($className)) {
            throw new InvalidArgumentException('Missing format class.');
        }

        if (key_exists($className, self::$services)) {
            return self::$services[$className];
        }

        $class = new $className();
        self::$services[$className] = $class;
        return $class;
    }

    public static function make($className)
    {
        if (!class_exists($className)) {
            throw new InvalidArgumentException('Missing format class.');
        }

        if (key_exists($className, self::$services)) {
            return self::$services[$className];
        }

        $class = new $className();
        self::$services[$className] = $class;
        return $class;
    }


    public static  function __callStatic($func,$arguments)
    {
        return self::creation($func);
    }
}