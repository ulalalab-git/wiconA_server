<?php
namespace App\Benefit\Action;

use App\Handler\Kernel as HandlerKernel;
use App\TComponent;
use App\TContainer;
use App\User\Domain\Info as UserInfo;
use App\Company\Domain\User as CompanyUser;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 *
 *
 */
class Login extends HandlerKernel
{
    /**
     * 자주 사용하는 함수 모음
     *
     * @see \App\TComponent
     */
    use TComponent, TContainer;

    /**
     * 유저 로그인
     *
     * @param  Request    $req  \Psr\Http\Message\ServerRequestInterface
     * @param  Response   $res  \Psr\Http\Message\ResponseInterface
     * @param  array|null $args URL 맵핑 정규식 데이터 - 참조(\Slim\DeferredCallable)
     * @return Response         \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(ServerRequestInterface $req, ResponseInterface $res, $args = null)
    {
        if ($this->isLogin() === true) {
            return $this->redirect('dashboard');
        }

        $params = $this->checked([
            'email',
            'passwd',
        ]);

        if (filter_var($params['email'], FILTER_VALIDATE_EMAIL) === false) {
            $this->flash->addMessage('error', 'email_not_input');
            return $this->show(__('empty_system'), "window.location.href = '/';");
        }

        $detail = $this->load(UserInfo::class)->find($params['email']);
        if (empty($detail) === true) {
            $this->flash->addMessage('error', 'empty_member');
            return $this->show(__('empty_system'), "window.location.href = '/';");
        }

        if ($detail['user_access'] !== 'Y') {
            $this->flash->addMessage('error', 'empty_member');
            return $this->show(__('empty_system'), "window.location.href = '/';");
        }

        if ($detail['user_delete_date'] !== '0000-00-00 00:00:00') {
            $this->flash->addMessage('error', 'empty_member');
            return $this->show(__('empty_system'), "window.location.href = '/';");
        }

        if (password_verify($params['passwd'], $detail['user_passwd']) === false) {
            $this->flash->addMessage('error', 'fail_password');
            return $this->show(__('empty_system'), "window.location.href = '/';");
        }
        $userInfo = $this->load(UserInfo::class)->get($detail['user_idx']);

        // todo. company, agent info
        $this->setUserInfo([
            'company' => $this->load(CompanyUser::class)->get($detail['user_idx']),
            'user'    => $detail,
            'userInfo' => $userInfo
        ]);
        return $this->redirect('dashboard');
    }
}
