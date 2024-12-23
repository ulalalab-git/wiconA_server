<?php
namespace Middleware\Error;

use SlimFacades\App;
use Slim\Error\AbstractErrorRenderer;
use Slim\Error\Renderers\HtmlErrorRenderer;
use Slim\Http\Body;
use Throwable;

/**
 *
 */
class ErrorRenderer extends AbstractErrorRenderer
{
    /**
     * [render description]
     * @param  Throwable $exception           [description]
     * @param  bool      $displayErrorDetails [description]
     * @return [type]                         [description]
     */
    public function render(Throwable $exception, bool $displayErrorDetails): string
    {
        return App::getContainer()->get('view')->fetch('html/error.html');
    }

    /**
     * @param Throwable $exception
     * @param bool $displayErrorDetails
     * @return Body
     */
    public function renderWithBody(Throwable $exception, bool $displayErrorDetails): Body
    {
        $output = $this->render($exception, $displayErrorDetails);
        $body   = new Body(fopen('php://temp', 'r+'));
        $body->write($output);
        return $body;
    }
}
