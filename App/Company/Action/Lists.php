<?php
namespace App\Company\Action;

use App\Company\Domain\Info as CompanyInfo;
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
     * 회사 리스트
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

        $companyInfo = $this->load(CompanyInfo::class);


        $inform = $this->session->get('inform');
        $privieage = array();
        if($inform['userInfo']['user_level'] == 127){

        }else if($inform['userInfo']['is_agent']){
            $privieage = [
                'type'    => 'agent',
                'param' => $inform['userInfo']['is_agent'],
            ];
        }else if($inform['userInfo']['is_company']){
            $privieage = [
                'type'    => 'company',
                'param' => $inform['userInfo']['is_company'],
            ];
        }else{
            $privieage = [
                'type'    => 'user',
                'param' => $inform['userInfo']['user_idx'],
            ];
        }


        $handlerLayout = $this->load(HandlerLayout::class);
        $handlerLayout->appendJs('/js/dashboard_company_manager.js');
        return $handlerLayout->render('html/dashboard/company/lists.html', [
            'info'      => $inform,
            'search'    => $search,
            'lists'     => $companyInfo->lists($search,$privieage, $page),
            'paginator' => $this->helper('paginator')->generator([
                'page'  => $page,
                'total' => $companyInfo->count($search,$privieage),
            ]),
        ]);
    }
}
