<?php
namespace Middleware\Stack;

use Middleware\Psr\Factory\ResponseFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\App;

/**
 *
 */
class Resolver implements MiddlewareInterface
{
    private $app = null;

    /**
     * [__construct description]
     * @param App $app [description]
     */
    public function __construct(App $app)
    {
        $this->app = $app;
    }

    /**
     * [process description]
     * @param  ServerRequestInterface $request  [description]
     * @param  ResponseInterface      $response [description]
     * @param  callable               $next     [description]
     * @return [type]                           [description]
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response  = (new ResponseFactory())->createResponse();
        $container = $this->app->getContainer();
        $container->put('request', $request);
        $container->put('response', $response);

        $response = $handler->handle($request);
        if ($response->isRedirect() === true || $response->isRedirection() === true) {
            $container->get('flash')->messenger();
        }

        return $response;
    }
}
