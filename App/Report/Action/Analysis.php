<?php
namespace App\Report\Action;

use App\Company\Domain\Device as CompanyDevice;
use App\Device\Domain\Data as DeviceData;
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
class Analysis extends HandlerKernel
{
    /**
     * 자주 사용하는 함수 모음
     *
     * @see \App\TComponent
     */
    use TComponent, TContainer;

    /**
     * 분석리포트 페이지
     *
     * @param  Request    $req  \Psr\Http\Message\ServerRequestInterface
     * @param  Response   $res  \Psr\Http\Message\ResponseInterface
     * @param  array|null $args URL 맵핑 정규식 데이터 - 참조(\Slim\DeferredCallable)
     * @return Response         \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(ServerRequestInterface $req, ResponseInterface $res, $args = null)
    {
        $loginUserInfo = $this->getUserInfo();
        $company       = -1;
        if ($this->masterLevel <= $loginUserInfo['user']['user_level']) {
            $company = 0;
        } else {
            if (empty($loginUserInfo['company']) === false) {
                $company = $loginUserInfo['company']['company_idx'];
            }
        }

        $virtual = 0;
        $params   = $req->getParams();
        $analysis = [];
        if ($req->getMethod() === 'POST') {
            $start    = $params['date'] . ' ' . str_pad($params['start'], 5, '0', STR_PAD_LEFT) . ':00';
            $end      = $params['date'] . ' ' . str_pad($params['end'], 5, '0', STR_PAD_LEFT) . ':00';
            $analysis = $this->load(DeviceData::class)->analysis($params['virtual'], $start, $end);
            $virtual = $params['virtual'];
        }
        $device = $this->load(CompanyDevice::class)->company($company);



        $handlerLayout = $this->load(HandlerLayout::class);
        $handlerLayout->appendJs('/js/dashboard_report_manager.js');
        return $handlerLayout->render('html/dashboard/report/analysis.html', [
            'device'   => $device,
            'virtual'  => $virtual,
            'analysis' => $analysis,
            'search'   => $params,
        ]);
    }
}
