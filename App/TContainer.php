<?php
namespace App;

use App\Handler\Exception\ProcessError;
use App\Handler\Exception\ProcessPass;
use SlimFacades\App;

/**
 *
 */
trait TContainer
{
    protected $once = [];

    public function __get($name = null)
    {
        return App::getContainer()->get($name);
    }

    protected function error($message = null, $code = 500)
    {
        $mode    = $this->setting('mode', 'product');
        $service = $this->setting('service', 'local');

        if ($mode === 'development' || ($service === 'local' || $service === 'dev')) {
            $container = $this->app->getContainer();
            $request   = $container->get('request');
            $uri       = $request->getUri();
            $trace     = debug_backtrace();
            error_log('exception error : ' . $trace[0]['file'] . ' : ' . $trace[0]['line'] . $uri->getPath() . ' ' . $request->getMethod() . ' ' . json_encode($request->getParams()));
        }

        throw new ProcessError($message, $code);
    }

    protected function errorPass($message = null, $code = 500)
    {
        throw new ProcessPass($message, $code);
    }

    protected function get($key = null)
    {
        return $this->once[$key];
    }

    protected function helper($name = null)
    {
        return $this->load('App\\Handler\\Helper\\' . ucfirst(strtolower($name)));
    }

    protected function load($className = null, $args = [])
    {
        if (isset($this->once[$className]) === false) {
            $this->once[$className] = new $className($args);
        }

        return $this->once[$className];
    }

    protected function proc($className = null, $args = [])
    {
        // return call_user_func()
        return new $className($args);
    }

    protected function set($key = null, $value = null)
    {
        $this->once[$key] = $value;
    }

    protected function setting($key = null, $default = null)
    {
        $config = $this->app->getContainer()->get('config');
        return (empty($config[$key]) === false) ? $config[$key] : $default;
    }
}
