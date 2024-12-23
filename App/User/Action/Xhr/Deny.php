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
class Deny extends HandlerKernel
{
    /**
     * 자주 사용하는 함수 모음
     *
     * @see \App\TComponent
     */
    use TComponent, TContainer;

    /**
     * 유저 거부
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
                'deny',
            ]);

            // $params = [
            //     'deny' => [
            //         1,
            //         2,
            //         3,
            //     ],
            // ];

            $userInfo = $this->load(UserInfo::class);
            $manager  = $userInfo->find('mail@ulalalab.com');
            $deny     = array_flip($params['deny']);
            if (isset($deny[$manager['user_idx']]) === true) {
                $this->error('empty_system');
            }

            $userInfo->confirm($params['deny'], 'N');

        } catch (Exception $e) {
            return $res->withJson(['error' => $e->getMessage()]);
        }

        return $res->withJson($response);
    }
}
