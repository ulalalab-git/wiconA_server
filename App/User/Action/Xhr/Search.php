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
class Search extends HandlerKernel
{
    /**
     * 자주 사용하는 함수 모음
     *
     * @see \App\TComponent
     */
    use TComponent, TContainer;

    /**
     * 유저 검색
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

            $page   = intval($req->getParam('page', 1));
            $search = [
                'type'    => trim($req->getParam('type')),
                'keyword' => trim($req->getParam('keyword')),
            ];

            $userInfo = $this->load(UserInfo::class);

            $inform = $this->session->get('inform');
            $privieage = array();
            if($inform['userInfo']['user_level'] == 127){

            }else if($inform['userInfo']['is_agent']){
                $privieage = [
                    'type'    => 'agent',
                    'search' => $inform['userInfo']['user_idx'],
                ];
            }else if($inform['userInfo']['is_company']){
                $privieage = [
                    'type'    => 'company',
                    'search' => $inform['userInfo']['is_company'],
                ];
            }else{
                $privieage = [
                    'type'    => 'user',
                    'search' => $inform['userInfo']['user_idx'],
                ];
            }


            $response = [
                'search'    => $search,
                'lists'     => $userInfo->lists_privileage($search,$privieage, $page),
                'paginator' => $this->helper('paginator')->generator([
                    'page'  => $page,
                    'total' => $userInfo->count_privileage($search,$privieage),
                ]),
            ];

        } catch (Exception $e) {
            return $res->withJson(['error' => $e->getMessage()]);
        }

        return $res->withJson($response);
    }
}
