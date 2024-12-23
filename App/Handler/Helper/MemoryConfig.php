<?php
namespace App\Handler\Helper;

/**
 *
 */
class MemoryConfig
{
    /**
     * [$confArray description]
     * @var array
     */
    static $confArray = [];

    /**
     * [read description]
     * @param  [type] $name [description]
     * @return [type]       [description]
     */
    public static function read(string $name = null):  ? object
    {
        return self::$confArray[$name];
    }

    /**
     * [write description]
     * @param  string|null $name  [description]
     * @param  object|null $value [description]
     * @return [type]             [description]
     */
    public static function write(string $name = null,  ? object $value = null) : void
    {
        self::$confArray[$name] = $value;
    }
}
