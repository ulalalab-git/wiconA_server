<?php
/**
 * Slim Framework (https://slimframework.com)
 *
 * @license https://github.com/slimphp/Slim/blob/4.x/LICENSE.md (MIT License)
 */

declare(strict_types=1);

namespace Slim\Tests\Middleware;

use Psr\Http\Server\RequestHandlerInterface;
use Slim\Middleware\MethodOverrideMiddleware;
use Slim\MiddlewareDispatcher;
use Slim\Tests\TestCase;

class MethodOverrideMiddlewareTest extends TestCase
{
    public function testHeader()
    {
        $responseFactory = $this->getResponseFactory();
        $mw = (function ($request, $handler) use ($responseFactory) {
            $this->assertEquals('PUT', $request->getMethod());
            return $responseFactory->createResponse();
        })->bindTo($this);
        $mw2 = new MethodOverrideMiddleware();

        $request = $this
            ->createServerRequest('/', 'POST')
            ->withHeader('X-Http-Method-Override', 'PUT');

        $middlewareDispatcher = new MiddlewareDispatcher($this->createMock(RequestHandlerInterface::class));
        $middlewareDispatcher->addCallable($mw);
        $middlewareDispatcher->addMiddleware($mw2);
        $middlewareDispatcher->handle($request);
    }

    public function testBodyParam()
    {
        $responseFactory = $this->getResponseFactory();
        $mw = (function ($request, $handler) use ($responseFactory) {
            $this->assertEquals('PUT', $request->getMethod());
            return $responseFactory->createResponse();
        })->bindTo($this);

        $mw2 = new MethodOverrideMiddleware();

        $request = $this
            ->createServerRequest('/', 'POST')
            ->withParsedBody(['_METHOD' => 'PUT']);

        $middlewareDispatcher = new MiddlewareDispatcher($this->createMock(RequestHandlerInterface::class));
        $middlewareDispatcher->addCallable($mw);
        $middlewareDispatcher->addMiddleware($mw2);
        $middlewareDispatcher->handle($request);
    }

    public function testHeaderPreferred()
    {
        $responseFactory = $this->getResponseFactory();
        $mw = (function ($request, $handler) use ($responseFactory) {
            $this->assertEquals('DELETE', $request->getMethod());
            return $responseFactory->createResponse();
        })->bindTo($this);

        $mw2 = new MethodOverrideMiddleware();

        $request = $this
            ->createServerRequest('/', 'POST')
            ->withHeader('X-Http-Method-Override', 'DELETE')
            ->withParsedBody((object) ['_METHOD' => 'PUT']);

        $middlewareDispatcher = new MiddlewareDispatcher($this->createMock(RequestHandlerInterface::class));
        $middlewareDispatcher->addCallable($mw);
        $middlewareDispatcher->addMiddleware($mw2);
        $middlewareDispatcher->handle($request);
    }

    public function testNoOverride()
    {
        $responseFactory = $this->getResponseFactory();
        $mw = (function ($request, $handler) use ($responseFactory) {
            $this->assertEquals('POST', $request->getMethod());
            return $responseFactory->createResponse();
        })->bindTo($this);

        $mw2 = new MethodOverrideMiddleware();

        $request = $this->createServerRequest('/', 'POST');

        $middlewareDispatcher = new MiddlewareDispatcher($this->createMock(RequestHandlerInterface::class));
        $middlewareDispatcher->addCallable($mw);
        $middlewareDispatcher->addMiddleware($mw2);
        $middlewareDispatcher->handle($request);
    }
}
