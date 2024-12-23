<?php
namespace App\Benefit\Action;

use App\Benefit\Domain\Mail as BenefitMail;
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
class Confirm extends HandlerKernel
{
    /**
     * 자주 사용하는 함수 모음
     *
     * @see \App\TComponent
     */
    use TComponent, TContainer;

    /**
     * 유저 가입 페이지
     *
     * @param  Request    $req  \Psr\Http\Message\ServerRequestInterface
     * @param  Response   $res  \Psr\Http\Message\ResponseInterface
     * @param  array|null $args URL 맵핑 정규식 데이터 - 참조(\Slim\DeferredCallable)
     * @return Response         \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(ServerRequestInterface $req, ResponseInterface $res, $args = null)
    {
        $params = $this->checked([
            'email',
            'passwd',
            'name',
            'name_last',
            'confirm',
        ]);

        if (filter_var($params['email'], FILTER_VALIDATE_EMAIL) === false) {
            $this->flash->addMessage('error', 'email_not_input');
            return $this->show(__('empty_system'), "window.location.href = '/register';");
        }

        if ($params['passwd'] !== $params['confirm'] || strlen($params['passwd']) < 6) {
            $this->flash->addMessage('error', 'password_confirm');
            return $this->show(__('empty_system'), "window.location.href = '/register';");
        }

        if (empty($params['name']) === true || empty($params['name_last']) === true) {
            $this->flash->addMessage('error', 'name_empty');
            return $this->show(__('empty_system'), "window.location.href = '/register';");
        }

        $info = $this->load(UserInfo::class)->find($params['email']);
        if (empty($info) === false) {
            $this->flash->addMessage('error', 'not_empty_email');
            return $this->show(__('empty_system'), "window.location.href = '/register';");
        }

        $stringify = $this->helper('stringify');
        $encrypt   = $this->helper('encrypt');
        $hash      = $encrypt->encodebase64($params['email']) . $stringify->shuffle(40);
        $passwd    = $stringify->password($params['passwd']);
        $name      = $params['name'] . ' ' . $params['name_last'] . ' (' . $params['email'] . ')';
        $this->load(UserInfo::class)->register([
            'email'     => $params['email'],
            'passwd'    => $passwd,
            'name'      => $params['name'],
            'name_last' => $params['name_last'],
            'tel'       => '',
            'level'     => 1,
            'access'    => 'N',
            'comment'   => $hash,
        ]);

        $domain  = $this->request->getUri()->getBaseUrl() . DIRECTORY_SEPARATOR . 'verification' . DIRECTORY_SEPARATOR . $hash;
        $message = $this->view->fetch('html/benefit/invite.html', [
            'name'   => $name,
            'domain' => $domain,
        ]);

        $subject = __('register_mail_title');
        $this->load(BenefitMail::class)->send([$params['email']], $subject, $message);

        return $this->layout('html/benefit/confirm.html', [
            'message' => str(__('auth_email_info', $name)),
        ]);
    }
}
