<?php
namespace App\User\Action\Xhr;

use App\User\Domain\Info as UserInfo;
use App\Handler\Kernel as HandlerKernel;
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
     * 유저 수정
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
                'user',
                'name',
                'name_last',
                'tel',
                'access',
                'email'
            ]);

            // $params = [
            //     'user' => 2,
            //     'name' => 'aa',
            //     'name_last' => 'bb',
            //     'tel' => '123123',
            //     'access' => 'R',
            // ];

            //error_log("here:".print_r($req->getParam('profile'),1)."::", 0);


            $userInfo = $this->load(UserInfo::class);
            if (empty($userInfo->get($params['user'])) === true) {
                $this->error('empty_system');
            }
            $passwd = $req->getParam('passwd');
            if (empty($passwd) === false) {
                $passwd = $this->helper('stringify')->password($passwd);
                $userInfo->modify($params['user'],
                    [
                        'passwd'    => $passwd,
                        'name'      => $params['name'],
                        'name_last' => $params['name_last'],
                        'tel'       => $params['tel'],
                        'access'    => $params['access'],
                    ]);
            }else{
                $userInfo->modify($params['user'],
                    [
                        'name'      => $params['name'],
                        'name_last' => $params['name_last'],
                        'tel'       => $params['tel'],
                        'access'    => $params['access'],
                    ]);
                if($req->getParam('profile')){
                    $detail = $this->load(UserInfo::class)->find($params['email']);
                    $this->setUserInfo([
                        'user'    => $detail,
                    ]);
                }

            }


        } catch (Exception $e) {
            return $res->withJson(['error' => $e->getMessage()]);
        }

        return $res->withJson($response);
    }
}
