<?php
namespace Middleware\Error;

use Middleware\Error\ErrorRenderer;
use Slim\Handlers\ErrorHandler as ExtendErrorHandler;

/**
 *
 */
class ErrorHandler extends ExtendErrorHandler
{
    protected $renderer = ErrorRenderer::class;
}
