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
class Modify extends HandlerKernel
{
    /**
     * 자주 사용하는 함수 모음
     *
     * @see \App\TComponent
     */
    use TComponent, TContainer;

    /**
     * 회사 수정
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
            $params = $this->checked([
                'company',
                'name',
                'ceo',
                'business',
                'tel',
                'zip',
                'address',
                'address_detail',
            ]);

            // $params = [
            //     'company'        => 1,
            //     'name'           => 'name',
            //     'ceo'            => 'ceo',
            //     'business'       => 'business',
            //     'tel'            => 'a 123123',
            //     'zip'            => 'dsafasd 1231a',
            //     'address'        => 'address',
            //     'address_detail' => 'address_detail',
            // ];

            $companyInfo = $this->load(CompanyInfo::class);
            if (empty($companyInfo->get($params['company'])) === true) {
                $this->error('empty_system');
            }

            $companyInfo->modify($params['company'], [
                'name'           => $params['name'],
                'ceo'            => $params['ceo'],
                'business'       => $params['business'],
                'tel'            => $params['tel'],
                'zip'            => $params['zip'],
                'address'        => $params['address'],
                'address_detail' => $params['address_detail'],
            ]);

        } catch (Exception $e) {
            return $res->withJson(['error' => $e->getMessage()]);
        }

        return $res->withJson($response);
    }
}
