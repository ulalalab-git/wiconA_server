<?php
namespace App\Agent\Action;

use App\Agent\Domain\Company as AgentCompany;
use App\Agent\Domain\Info as AgentInfo;
use App\Agent\Domain\User as AgentUser;
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
class Detail extends HandlerKernel
{
    /**
     * 자주 사용하는 함수 모음
     *
     * @see \App\TComponent
     */
    use TComponent, TContainer;

    /**
     * 회사 수정 페이지
     *
     * @param  Request    $req  \Psr\Http\Message\ServerRequestInterface
     * @param  Response   $res  \Psr\Http\Message\ResponseInterface
     * @param  array|null $args URL 맵핑 정규식 데이터 - 참조(\Slim\DeferredCallable)
     * @return Response         \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(ServerRequestInterface $req, ResponseInterface $res, $args = null)
    {
        if (empty($args['agent']) === true) {
            return $this->show(__('empty_system'), "window.location.href = '/';");
        }

        $agent = $this->load(AgentInfo::class)->get($args['agent']);
        if (empty($agent) === true) {
            return $this->show(__('empty_system'), "javascript:history.go(-1);");
        }

        $userPage   = intval($req->getParam('user_page', 1));
        $userSearch = [
            'type'    => trim($req->getParam('user_type')),
            'keyword' => trim($req->getParam('user_keyword')),
        ];

        $companyPage   = intval($req->getParam('company_page', 1));
        $companySearch = [
            'type'    => trim($req->getParam('company_type')),
            'keyword' => trim($req->getParam('company_keyword')),
        ];



        $agentUser   = $this->load(AgentUser::class);
        $agentCompany = $this->load(AgentCompany::class);

        $handlerLayout = $this->load(HandlerLayout::class);
        $handlerLayout->appendJs('/js/dashboard_agent_manager.js');
        return $handlerLayout->render('html/dashboard/agent/detail.html', [
            'agent' => $agent,
            'user'    => [
                'search'    => $userSearch,
                'lists'     => $agentUser->lists($agent['agent_idx'], $userSearch, $userPage),
                'paginator' => $this->helper('paginator')->generator([
                    'page'  => $userPage,
                    'total' => $agentUser->count($agent['agent_idx'], $userSearch),
                ]),
            ],
            'company'  => [
                'search'    => $companySearch,
                'lists'     => $agentCompany->lists($agent['agent_idx'], $companySearch, $companyPage),
                'paginator' => $this->helper('paginator')->generator([
                    'page'  => $companyPage,
                    'total' => $agentCompany->count($agent['agent_idx'], $companySearch),
                ]),
            ],
        ]);
    }
}
