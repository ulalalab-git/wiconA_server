<?php
namespace App\User\Action\Xhr;

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
class Register extends HandlerKernel
{
    /**
     * 자주 사용하는 함수 모음
     *
     * @see \App\TComponent
     */
    use TComponent, TContainer;

    /**
     * 유저 추가
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
            $params = $this->checked([
                'email',
                'name',
                'name_last',
                'passwd',
                'tel',
                'access',
            ]);

            // $params = [
            //     'email'     => 'sdfasdf@ulalalab.com',
            //     'name'      => 'asdfe',
            //     'name_last' => 'sfasef',
            //     'passwd'    => 'passwd',
            //     'tel'       => '010-2193-1231',
            //     'access'    => 'N',
            // ];

            $userInfo = $this->load(UserInfo::class);
            if (empty($userInfo->exist($params['email'])) === false) {
                $this->error('empty_system');
            }

            $userInfo->register([
                'email'     => $params['email'],
                'passwd'    => $this->helper('stringify')->password($params['passwd']),
                'name'      => $params['name'],
                'name_last' => $params['name_last'],
                'tel'       => $params['tel'],
                'level'     => 1,
                'access'    => $params['access'],
                'comment'   => '',
            ]);

        } catch (Exception $e) {
            return $res->withJson(['error' => $e->getMessage()]);
        }

        return $res->withJson($response);
    }
}
