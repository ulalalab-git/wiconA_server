<?php
namespace App\Agent\Action\Xhr;

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
class Companies extends HandlerKernel
{
    /**
     * 자주 사용하는 함수 모음
     *
     * @see \App\TComponent
     */
    use TComponent, TContainer;

    /**
     * 장비 검색
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
            $agent = $req->getParam('agent');
            if (empty($agent) === true) {
                $this->error('empty_system');
            }

            $page   = intval($req->getParam('page', 1));
            $search = [
                'type'    => trim($req->getParam('type')),
                'keyword' => trim($req->getParam('keyword')),
            ];

            $agentCompany = $this->load(AgentCompany::class);

            $response = [
                'search'    => $search,
                'lists'     => $agentCompany->lists($agent, $search, $page),
                'paginator' => $this->helper('paginator')->generator([
                    'page'  => $page,
                    'total' => $agentCompany->count($agent, $search),
                ]),
            ];

        } catch (Exception $e) {
            return $res->withJson(['error' => $e->getMessage()]);
        }

        return $res->withJson($response);
    }
}
