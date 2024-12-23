<?php
namespace App\Dashboard\Action\Xhr;

use App\Dashboard\Domain\Packet as DashboardPacket;
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
class Packet extends HandlerKernel
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
            $device = $req->getParam('device', 0);
            $last   = $req->getParam('last', 0);
            $virtual   = $req->getParam('virtual', 0);


            if($virtual){
                $response = $this->load(DashboardPacket::class)->statisticsport($device, $last, date('Y-m-d'),$virtual);
            }else{
                $response = $this->load(DashboardPacket::class)->statistics($device, $last, date('Y-m-d'));
            }


        } catch (Exception $e) {
            return $res->withJson(['error' => $e->getMessage()]);
        }

        return $res->withJson($response);
    }
}
