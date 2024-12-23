<?php

if (defined('_TIMEZONE_') === false) {
    define('_TIMEZONE_', 'Asia/Seoul');
}

if (defined('_CHARSET_') === false) {
    define('_CHARSET_', 'utf-8');
}

if (defined('_BOOTSTRAP_') === false) {
    define('_BOOTSTRAP_', microtime(true));
}

if (defined('DS') === false) {
    define('DS', DIRECTORY_SEPARATOR);
}

# debug set
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

# init encoding
mb_internal_encoding(_CHARSET_);
mb_regex_encoding(_CHARSET_);
mb_http_output(_CHARSET_);
set_time_limit(0);
date_default_timezone_set(_TIMEZONE_);
ini_set('default_charset', _CHARSET_);
// ini_set('xdebug.max_nesting_level', 100000);
// ini_set('memory_limit', '-1');

header('P3P: CP="ALL CURa ADMa DEVa TAIa OUR BUS IND PHY ONL UNI PUR FIN COM NAV INT DEM CNT STA POL HEA PRE LOC OTC"');
header('Content-Type: text/html; charset=' . _CHARSET_);
header('Expires: 0'); // rfc2616 - Section 14.21
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');  // HTTP/1.1
header('Cache-Control: pre-check=0, post-check=0, max-age=0'); // HTTP/1.1
header('Pragma: no-cache');                                    // HTTP/1.0

// psr-4 autoload style
spl_autoload_register(function ($className = null) {
    if (empty($className) === true) {
        return false;
    }

    $thisClass = str_replace(__NAMESPACE__ . '\\', '', __CLASS__);
    $baseDir   = __DIR__ . DIRECTORY_SEPARATOR;
    if (substr($baseDir, -strlen($thisClass)) === $thisClass) {
        $baseDir = substr($baseDir, 0, -strlen($thisClass));
    }

    $className = ltrim($className, '\\');

    $namespace = '';
    if ($lastNsPos = strripos($className, '\\')) {
        $namespace = substr($className, 0, $lastNsPos);
        $baseDir .= str_replace('\\', DIRECTORY_SEPARATOR, $namespace);
    }

    # psr-4 style
    $len = strlen($namespace);
    if (strncmp($namespace, $className, $len) !== 0) {
        return false;
    }

    if (file_exists($fileName = $baseDir . str_replace('\\', DIRECTORY_SEPARATOR, substr($className, $len)) . '.php') === true) {
        require_once $fileName;
    }
});
