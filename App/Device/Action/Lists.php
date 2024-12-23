<?php
namespace App\Device\Action;

use App\Device\Domain\Info as DeviceInfo;
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

        $deviceInfo = $this->load(DeviceInfo::class);

        $inform = $this->session->get('inform');
        $privieage = array();
        if($inform['userInfo']['user_level'] == 127){

        }else if($inform['userInfo']['is_agent'] ){
            $privieage = [
                'type'    => 'agent',
                'keyword' => $inform['userInfo']['is_agent'],
            ];
        }else if($inform['userInfo']['is_company']){
            $privieage = [
                'type'    => 'company',
                'keyword' => $inform['userInfo']['is_company'],
            ];
        }else{
            $privieage = [
                'type'    => 'user',
                'keyword' => $inform['userInfo']['user_idx'],
            ];
        }


        $handlerLayout = $this->load(HandlerLayout::class);
        $handlerLayout->appendJs('/js/dashboard_device_manager.js');
        return $handlerLayout->render('html/dashboard/device/lists.html', [
            'info'      => $inform,
            'search'    => $search,
            //'lists'     => $deviceInfo->lists($search, $page),
            'lists'     => $deviceInfo->lists_privileage($search,$privieage, $page),
            'paginator' => $this->helper('paginator')->generator([
                'page'  => $page,
                //'total' => $deviceInfo->count($search),
                'total' => $deviceInfo->count_privileage($search,$privieage),
            ]),
        ]);
    }
}
