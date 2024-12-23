<?php
namespace App\Benefit\Action;

use App\Handler\Kernel as HandlerKernel;
use App\TComponent;
use App\TContainer;
use App\User\Domain\Info as UserInfo;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 *
 *
 */
class Initialize extends HandlerKernel
{
    /**
     * 자주 사용하는 함수 모음
     *
     * @see \App\TComponent
     */
    use TComponent, TContainer;

    /**
     * 비밀번호 초기화
     *
     * @param  Request    $req  \Psr\Http\Message\ServerRequestInterface
     * @param  Response   $res  \Psr\Http\Message\ResponseInterface
     * @param  array|null $args URL 맵핑 정규식 데이터 - 참조(\Slim\DeferredCallable)
     * @return Response         \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(ServerRequestInterface $req, ResponseInterface $res, $args = null)
    {
        if (empty($args['token']) === true) {
            return $this->show(__('empty_system'), "window.location.href = '/';");
        }

        $cache = $this->cacheRead('find', $args['token']);
        if (empty($cache) === true) {
            return $this->show(__('empty_system'), "window.location.href = '/';");
        }
        $this->cacheWrite('find', $args['token'], $cache, 86400);

        return $this->layout('html/benefit/initialize.html', [
            'token' => $args['token'],
            'cache' => $cache,
        ]);
    }
}
