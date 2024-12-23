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
class Modify extends HandlerKernel
{
    /**
     * 자주 사용하는 함수 모음
     *
     * @see \App\TComponent
     */
    use TComponent, TContainer;

    /**
     * 에이전트 수정 페이지
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

        $handlerLayout = $this->load(HandlerLayout::class);
        $handlerLayout->appendJs('/js/dashboard_agent_manager.js');
        return $handlerLayout->render('html/dashboard/agent/register.html', [
            'agent' => $agent,
        ]);
    }
}
