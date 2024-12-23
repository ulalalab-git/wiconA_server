<?php
namespace App\User\Action;

use App\User\Domain\Info as UserInfo;
use App\Handler\Kernel as HandlerKernel;
use App\Handler\Responder\Layout as HandlerLayout;
use App\TComponent;
use App\TContainer;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 *
 *
 */
class Modify extends HandlerKernel
{
    /**
     * 자주 사용하는 함수 모음
     *
     * @see \App\TComponent
     */
    use TComponent, TContainer;

    /**
     * 유저 수정 페이지
     *
     * @param  Request    $req  \Psr\Http\Message\ServerRequestInterface
     * @param  Response   $res  \Psr\Http\Message\ResponseInterface
     * @param  array|null $args URL 맵핑 정규식 데이터 - 참조(\Slim\DeferredCallable)
     * @return Response         \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(ServerRequestInterface $req, ResponseInterface $res, $args = null)
    {
        if (empty($args['user']) === true) {
            return $this->show(__('empty_system'), "window.location.href = '/';");
        }

        $user = $this->load(UserInfo::class)->get($args['user']);
        if (empty($user) === true) {
            return $this->show(__('empty_system'), "javascript:history.go(-1);");
        }


        $handlerLayout = $this->load(HandlerLayout::class);
        $handlerLayout->appendJs('/js/dashboard_user_manager.js');
        return $handlerLayout->render('html/dashboard/user/register.html', [
            'user' => $user,
        ]);
    }
}
