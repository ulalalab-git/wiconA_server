<?php
namespace App\Company\Action\Xhr;

use App\Company\Domain\Info as CompanyInfo;
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
class Search extends HandlerKernel
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

            $page   = intval($req->getParam('page', 1));
            $search = [
                'type'    => trim($req->getParam('type')),
                'keyword' => trim($req->getParam('keyword')),
            ];

            $companyInfo = $this->load(CompanyInfo::class);
            $inform = $this->session->get('inform');
            $privieage = array();


            $response = [
                'search'    => $search,
                'lists'     => $companyInfo->lists($search,$privieage, $page),
                'paginator' => $this->helper('paginator')->generator([
                    'page'  => $page,
                    'total' => $companyInfo->count($search),
                ]),
            ];

        } catch (Exception $e) {
            return $res->withJson(['error' => $e->getMessage()]);
        }

        return $res->withJson($response);
    }
}
