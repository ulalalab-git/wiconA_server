<?php
namespace App\Agent\Action\Xhr;

use App\Agent\Domain\User as AgentUser;
use App\Agent\Domain\Company as AgentCompany;
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
class Undesignate extends HandlerKernel
{
    /**
     * 자주 사용하는 함수 모음
     *
     * @see \App\TComponent
     */
    use TComponent, TContainer;

    /**
     * 유저 회사 등록
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
                'agent',
                'type',
                'target',
            ]);

            // $params = [
            //     'agent' => 1,
            //     'users'   => [1, 2, 3, 4],
            // ];

            if ($params['type'] === 'user') {
                $this->load(AgentUser::class)->undesignate($params['agent'], $params['target']);
            } else {
                $this->load(AgentCompany::class)->undesignate($params['agent'], $params['target']);
            }

        } catch (Exception $e) {
            return $res->withJson(['error' => $e->getMessage()]);
        }

        return $res->withJson($response);
    }
}
