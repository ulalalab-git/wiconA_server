<?php
namespace App\Agent\Action;

use App\Agent\Domain\Info as AgentInfo;
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
class Lists extends HandlerKernel
{
    /**
     * 자주 사용하는 함수 모음
     *
     * @see \App\TComponent
     */
    use TComponent, TContainer;

    /**
     * 에이전트 리스트
     *
     * @param  Request    $req  \Psr\Http\Message\ServerRequestInterface
     * @param  Response   $res  \Psr\Http\Message\ResponseInterface
     * @param  array|null $args URL 맵핑 정규식 데이터 - 참조(\Slim\DeferredCallable)
     * @return Response         \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(ServerRequestInterface $req, ResponseInterface $res, $args = null)
    {
        $page   = intval($req->getParam('page', 1));
        $search = [
            'type'    => $req->getParam('type'),
            'keyword' => $req->getParam('keyword'),
        ];

        $agentInfo = $this->load(AgentInfo::class);

        $inform = $this->session->get('inform');
        $privieage = array();
        if($inform['userInfo']['user_level'] == 127){

        }else if($inform['userInfo']['is_agent']){
            $privieage = [
                'type'    => 'agent',
                'wu_idx' => $inform['userInfo']['user_idx'],
            ];
        }else if($inform['userInfo']['is_company']){
            $privieage = [
                'type'    => 'company',
                'wu_idx' => $inform['userInfo']['user_idx'],
            ];
        }else{
            $privieage = [
                'type'    => 'user',
                'wu_idx' => $inform['userInfo']['user_idx'],
            ];
        }


        $handlerLayout = $this->load(HandlerLayout::class);
        $handlerLayout->appendJs('/js/dashboard_agent_manager.js');
        return $handlerLayout->render('html/dashboard/agent/lists.html', [
            'info'      => $inform,
            'search'    => $search,
            //'lists'     => $agentInfo->lists($search, $page),
            'lists'     => $agentInfo->lists_privileage($search, $privieage, $page),
            'paginator' => $this->helper('paginator')->generator([
                'page'  => $page,
                //'total' => $agentInfo->count($search),
                'total' => $agentInfo->count_privileage($search,$privieage),
            ]),
        ]);
    }
}
#echo "<script>alert(\"이렇게 띄우면 되자나\");</script>";
