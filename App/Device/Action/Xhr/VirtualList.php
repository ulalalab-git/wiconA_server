<?php
namespace App\Device\Action\Xhr;


use App\Device\Domain\Info as DeviceInfo;
use App\Device\Domain\Virtual as DeviceVirtual;
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
class VirtualList extends HandlerKernel
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

            $search = [
                'type'    => trim($req->getParam('type')),
                'keyword' => trim($req->getParam('keyword')),
            ];

            $deviceInfo = $this->load(DeviceVirtual::class);


            $response = [
                'search'    => $search,
                'lists'     => $deviceInfo->lists($search)
            ];

        } catch (Exception $e) {
            return $res->withJson(['error' => $e->getMessage()]);
        }

        return $res->withJson($response);
    }
}
