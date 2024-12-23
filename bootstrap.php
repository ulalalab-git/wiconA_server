<?php
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/vendor/autoload.php';

if (function_exists('__') === false) {
    function __()
    {
        static $cache = null;
        if ($cache === null) { // cache load
            $cache = require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'language.php';
        }

        $lang = 'ko';
        if (empty($_SERVER['HTTP_ACCEPT_LANGUAGE']) === false) {
            $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        }
        if (empty($_COOKIE['language']) === false) {
            if (empty($_COOKIE['language']) === false) {
                $lang = $_COOKIE['language'];
            }
        } else {
            if (empty($_GET['lang']) === false) {
                $lang = $_GET['lang'];
            }
        }
        $_COOKIE['language'] = $lang;

        $args = func_get_args();
        if (count($args) === 0) {
            return '';
        }
        $key = array_shift($args);
        if (isset($cache[$key][$lang]) === false) {
            return '';
        }

        $string = $cache[$key][$lang];
        // sprintf
        if (count($args) > 0) {
            $replace = [];
            for ($i = 0; $i < count($args); ++$i) {
                $replace[] = '{' . $i . '}';
            }
            $string = str_replace($replace, $args, $string);
        }
        return str($string);
    }
}

if (function_exists('str') === false) {
    function str($string = null)
    {
        // return nl2br(htmlentities($string, ENT_QUOTES, 'UTF-8'));
        return nl2br($string);
    }
}

if (function_exists('ep') === false) {
    function ep($string = null)
    {
        p(__($string));
    }
}

if (function_exists('p') === false) {
    function p($string = null)
    {
        echo $string;
    }
}

if (function_exists('measure') === false) {
    function measure()
    {
        return [
            'time'    => round((microtime(true) - _BOOTSTRAP_) * 1000, 4),
            'memoery' => number_format(((memory_get_usage() / 1024) / 1024), 4, '.', ','),
            'cpu'     => current(sys_getloadavg()),
        ];
    }
}

if (file_exists($config = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'connect.ulalalab') === false) {
    exit('service off');
}

use App\Handler\Helper\File;
use Middleware\Error\ErrorHandler;
use Middleware\Psr\Factory\ResponseFactory;
use Middleware\Psr\Factory\StreamFactory;
use Middleware\Session\RedisSession;
use Middleware\Session\SessionHelper;
use Middleware\Stack\Container as PsrContainer;
use Middleware\Stack\Messages;
use Middleware\Stack\Resolver;
use Monolog\Handler\StreamHandler;
use Monolog\Logger as Logger;
use Pimple\Container;
use Predis\Client as RedisClient;
use SlimFacades\Facade;
use Slim\App as WimX;
use Slim\Http\Factory\DecoratedResponseFactory;
use Slim\Middleware\ContentLengthMiddleware;
use Slim\Middleware\ErrorMiddleware;
use Slim\Middleware\MethodOverrideMiddleware;
use Slim\Middleware\OutputBufferingMiddleware;
use Slim\Middleware\RoutingMiddleware;
use Slim\Views\PhpRenderer;

$streamFactory            = new StreamFactory();
$decoratedResponseFactory = new DecoratedResponseFactory(new ResponseFactory(), $streamFactory);

$container = new Container();
$app       = new WimX($decoratedResponseFactory, new PsrContainer($container));

// condition -- $config setting ?
$configLoad            = File::load($config);
$configLoad['mode']    = 'development';
$configLoad['service'] = 'altas';

$container['config'] = $configLoad;
$container['app']    = $container->factory(function ($c) use ($app) {
    return $app;
});
/*
$suffle = implode('', array_merge(range('A', 'Z'), range('a', 'z'), range(0, 9)));
$suffle = str_shuffle($suffle);
var_dump($suffle);exit;
 */
// todo. query procedure update
$container['maria'] = $container->factory(function ($c) {
    $conf = $c['config'];
    $pdo  = null;
    try {
        $uri = 'mysql:host=' . $conf['mariadb.host'] . ';port=' . $conf['mariadb.port'] . ';dbname=' . $conf['mariadb.dbname'] . ';charset=utf8';
        $pdo = new PDO($uri, $conf['mariadb.userid'], $conf['mariadb.passwd'], [
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET time_zone = '+09:00',NAMES utf8;",
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_PERSISTENT         => false,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            // PDO::ATTR_AUTOCOMMIT => false,
            // PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
        ]);
    } catch (PDOException $e) {
        var_dump($e);exit;
    }
    return $pdo;
});

$container['redis'] = $container->factory(function ($c) {
    $conf  = $c['config'];
    $redis = null;
    try {
        $uri = [
            'scheme' => 'tcp',
            'host'   => $conf['redis.host'],
            'port'   => $conf['redis.port'],
        ];
        if (empty($conf['redis.auth']) === false) {
            $uri['password'] = $conf['redis.auth'];
        }
        $redis = new RedisClient($uri);
        $redis->select($conf['redis.default']);
    } catch (Exception $e) {
        var_dump($e);exit;
    }
    return $redis;
});

$container['redis.session'] = $container->factory(function ($c) {
    $conf  = $c['config'];
    $redis = null;
    try {
        $uri = [
            'scheme' => 'tcp',
            'host'   => $conf['redis.host'],
            'port'   => $conf['redis.port'],
        ];
        if (empty($conf['redis.auth']) === false) {
            $uri['password'] = $conf['redis.auth'];
        }
        $redis = new RedisClient($uri);
        $redis->select($conf['redis.session']);
    } catch (Exception $e) {
        var_dump($e);exit;
    }
    return $redis;
});

$container['view'] = $container->factory(function ($c) {
    return new PhpRenderer($c['path']['public_html']);
});

$container['logger'] = $container->factory(function ($c) {
    $logger = new Logger('wim.x');
    $logger->pushHandler(new StreamHandler('/tmp/wimx.log', Logger::INFO));
    return $logger;
});

$container['flash'] = $container->factory(function ($c) {
    return new Messages();
});

$container['session'] = $container->factory(function ($c) {
    return new SessionHelper($c['redis.session']);
});

$container['path'] = [
    'service'     => dirname(__FILE__, 3),
    'application' => dirname(__FILE__),
    'public_html' => dirname(__FILE__) . DIRECTORY_SEPARATOR . 'public_html',
    'upload'      => dirname(__FILE__) . DIRECTORY_SEPARATOR . 'public_html' . DIRECTORY_SEPARATOR . 'upload',
];

$app->add(new RedisSession($container['redis.session'], $container['config']));
$app->add(new RoutingMiddleware($app->getRouteResolver()));
$app->add(new OutputBufferingMiddleware($streamFactory));
$app->add(new ContentLengthMiddleware());
$app->add(new MethodOverrideMiddleware());

$errorMiddleware = new ErrorMiddleware($app->getCallableResolver(), $decoratedResponseFactory, true, true, true);
$errorMiddleware->setDefaultErrorHandler(new ErrorHandler($decoratedResponseFactory));
$app->add($errorMiddleware);
$app->add(new Resolver($app));

Facade::setFacadeApplication($app);
