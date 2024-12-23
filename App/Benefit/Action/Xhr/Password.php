<?php
namespace App\Benefit\Action\Xhr;

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
class Password extends HandlerKernel
{
    /**
     * 자주 사용하는 함수 모음
     *
     * @see \App\TComponent
     */
    use TComponent, TContainer;

    /**
     * 유저 비밀번호 찾기
     *
     * @param  Request    $req  \Psr\Http\Message\ServerRequestInterface
     * @param  Response   $res  \Psr\Http\Message\ResponseInterface
     * @param  array|null $args URL 맵핑 정규식 데이터 - 참조(\Slim\DeferredCallable)
     * @return Response         \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(ServerRequestInterface $req, ResponseInterface $res, $args = null)
    {
        $response = [];
        try {
            if ($req->isXhr() === false) {
                $this->error('empty_system');
            }

            $params = $this->checked([
                'email',
            ]);

            $info = $this->load(UserInfo::class)->find($params['email']);
            if (empty($info) === true) {
                $this->error('empty_user_info');
            }

            $hash    = $this->helper('stringify')->shuffle(20);
            $domain  = $this->request->getUri()->getBaseUrl() . DIRECTORY_SEPARATOR . 'initialize' . DIRECTORY_SEPARATOR . $hash;
            $message = $this->view->fetch('html/benefit/find.html', [
                'domain' => $domain,
            ]);

            $subject = __('find_password_mail_title');
            $this->load(BenefitMail::class)->send([$params['email']], $subject, $message);

        } catch (Exception $e) {
            return $res->withJson(['error' => $e->getMessage()]);
        }

        return $res->withJson($response);
    }
}
