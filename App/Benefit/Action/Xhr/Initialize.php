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
class Initialize extends HandlerKernel
{
    /**
     * 자주 사용하는 함수 모음
     *
     * @see \App\TComponent
     */
    use TComponent, TContainer;

    /**
     * 유저 비밀번호 바꾸기
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
                'token',
                'passwd',
            ]);

            // $params = [
            //     'token'  => 'WYFPLBQc7H38rxao4EhC',
            //     'passwd' => 'aaaaaa',
            // ];

            $cache = $this->cacheRead('find', $params['token']);
            if (empty($cache) === true) {
                $this->error('empty_system');
            }
            $cache = json_decode($cache, true);
            if (filter_var($cache['user_email'], FILTER_VALIDATE_EMAIL) === false) {
                $this->error('empty_system');
            }
            $passwd = $params['passwd'];
            if (strlen($passwd) < 6) {
                $this->error('empty_system');
            }

            $passwd = $this->helper('stringify')->password($passwd);
            $this->load(UserInfo::class)->passwd($cache['user_email'], $passwd);
            $this->cacheRemove('find', $params['token']);

        } catch (Exception $e) {
            return $res->withJson(['error' => $e->getMessage()]);
        }

        return $res->withJson($response);
    }
}
