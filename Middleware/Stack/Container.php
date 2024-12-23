<?php
namespace Middleware\Stack;

use Exception;
use Pimple\Container as PimpleContainer;
use Psr\Container\ContainerInterface;

/**
 *
 */
class ContainerException extends Exception
{

}
class Container implements ContainerInterface
{
    private $pimple;

    public function __construct(PimpleContainer $pimple)
    {
        $this->pimple = $pimple;
    }

    public function get($id)
    {
        return $this->pimple[$id];
    }

    public function has($id)
    {
        return isset($this->pimple[$id]);
    }

    public function put($id, $value)
    {
        $this->pimple[$id] = $value;
    }

    public function singletone($id)
    {
        return $this->pimple->raw($id);
    }
}
