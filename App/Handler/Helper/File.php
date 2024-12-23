<?php
namespace App\Handler\Helper;

use InvalidArgumentException;
use RuntimeException;

/**
 * File
 */
class File
{
    protected static $instance = null;

    public static function getInstance($name = null)
    {
        if (isset(static::$instance[$name]) === false) {
            static::$instance[$name] = new static();
        }
        return static::$instance[$name];
    }

    public static function load($filename = null)
    {
        $txt  = preg_split('/\n|\r\n|\r/', self::read($filename), -1, PREG_SPLIT_NO_EMPTY);
        $conf = [];
        foreach ($txt as $tx) {
            if (strpos($tx, '#') === 0) {
                continue;
            }
            $tx                = preg_replace('/\#.+/', '', $tx);
            list($key, $value) = array_map('trim', explode('=', $tx));
            $conf[$key]        = $value;
        }

        return $conf;
    }

    public static function read($filename = null)
    {
        if (file_exists($filename) === false) {
            throw new RuntimeException($filename . ' not exist');
        }
        if (is_readable($filename) === false) {
            throw new RuntimeException($filename . ' not readable');
        }

        $fh   = fopen($filename, "r");
        $data = fread($fh, filesize($filename));
        fclose($fh);

        return $data;
    }

    public static function write($filename = null, $data = '', $mode = 'w')
    {
        if ($mode !== 'w' && $mode !== 'w+') {
            throw new InvalidArgumentException('mode "' . $mode . '" is not valid');
        }

        if (file_exists($filename) === false) {
            if (is_writable(dirname($filename)) === false) {
                throw new RuntimeException($filename . ' not writable');
            }
        }

        if (is_writable($filename) === false) {
            throw new RuntimeException($filename . ' not writable');
        }

        $handler = fopen($filename, $mode);
        fwrite($handler, (string) $data);
        fclose($handler);

        return true;
    }
}
