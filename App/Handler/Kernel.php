<?php
namespace App\Handler;

use App\TContainer;

/**
 *
 */
class Kernel
{
    /**
     * 자주 사용하는 함수 모음
     *
     * @see \App\TComponent
     */
    use TContainer;

    protected $masterLevel = 127;

    /**
     * [redirect description]
     * @param  [type] $movePage [description]
     * @return [type]           [description]
     */
    protected function redirect($movePage = null)
    {
        return $this->response->withRedirect($this->app->getRouteCollector()->getNamedRoute($movePage)->getPattern());
    }
}
