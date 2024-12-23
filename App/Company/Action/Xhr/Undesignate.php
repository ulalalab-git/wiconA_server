<?php
namespace App\Company\Action\Xhr;

use App\Company\Domain\User as CompanyUser;
use App\Company\Domain\Device as CompanyDevice;
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
class Undesignate extends HandlerKernel
{
    /**
     * 자주 사용하는 함수 모음
     *
     * @see \App\TComponent
     */
    use TComponent, TContainer;

    /**
     * 유저 회사 등록
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
                'type',
                'target',
            ]);

            // $params = [
            //     'company' => 1,
            //     'type'    => 'user',
            //     'target'  => [1, 2, 3, 4],
            // ];

            if ($params['type'] === 'user') {
                $this->load(CompanyUser::class)->undesignate($params['company'], $params['target']);
            } else {
                $this->load(CompanyDevice::class)->undesignate($params['company'], $params['target']);
            }

        } catch (Exception $e) {
            return $res->withJson(['error' => $e->getMessage()]);
        }

        return $res->withJson($response);
    }
}
