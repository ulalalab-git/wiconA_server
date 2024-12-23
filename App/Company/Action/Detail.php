<?php
namespace App\Company\Action;

use App\Company\Domain\Device as CompanyDevice;
use App\Company\Domain\Info as CompanyInfo;
use App\Company\Domain\User as CompanyUser;
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
        if (empty($args['company']) === true) {
            return $this->show(__('empty_system'), "window.location.href = '/';");
        }

        $company = $this->load(CompanyInfo::class)->get($args['company']);
        if (empty($company) === true) {
            return $this->show(__('empty_system'), "javascript:history.go(-1);");
        }

        $userPage   = intval($req->getParam('user_page', 1));
        $userSearch = [
            'type'    => trim($req->getParam('user_type')),
            'keyword' => trim($req->getParam('user_keyword')),
        ];

        $devicePage   = intval($req->getParam('device_page', 1));
        $deviceSearch = [
            'type'    => trim($req->getParam('device_type')),
            'keyword' => trim($req->getParam('device_keyword')),
        ];

        $inform = $this->session->get('inform');

        $companyUser   = $this->load(CompanyUser::class);
        $companyDevice = $this->load(CompanyDevice::class);

        $handlerLayout = $this->load(HandlerLayout::class);
        $handlerLayout->appendJs('/js/dashboard_company_manager.js');
        return $handlerLayout->render('html/dashboard/company/detail.html', [
            'info'          => $inform,
            'company' => $company,
            'user'    => [
                'search'    => $userSearch,
                'lists'     => $companyUser->lists($company['company_idx'], $userSearch, $userPage),
                'paginator' => $this->helper('paginator')->generator([
                    'page'  => $userPage,
                    'total' => $companyUser->count($company['company_idx'], $userSearch),
                ]),
            ],
            'device'  => [
                'search'    => $deviceSearch,
                'lists'     => $companyDevice->lists($company['company_idx'], $deviceSearch, $devicePage),
                'paginator' => $this->helper('paginator')->generator([
                    'page'  => $devicePage,
                    'total' => $companyDevice->count($company['company_idx'], $deviceSearch),
                ]),
            ],
        ]);
    }
}
