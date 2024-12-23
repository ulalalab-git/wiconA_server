<?php
namespace App\Company\Action\Xhr;

use App\Company\Domain\User as CompanyUser;
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
class Users extends HandlerKernel
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
            $company = $req->getParam('company');
            if (empty($company) === true) {
                $this->error('empty_system');
            }

            $page   = intval($req->getParam('page', 1));
            $search = [
                'type'    => trim($req->getParam('type')),
                'keyword' => trim($req->getParam('keyword')),
            ];

            $companyUser = $this->load(CompanyUser::class);

            $response = [
                'search'    => $search,
                'lists'     => $companyUser->lists($company, $search, $page),
                'paginator' => $this->helper('paginator')->generator([
                    'page'  => $page,
                    'total' => $companyUser->count($company, $search),
                ]),
            ];

        } catch (Exception $e) {
            return $res->withJson(['error' => $e->getMessage()]);
        }

        return $res->withJson($response);
    }
}
