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
class State extends HandlerKernel
{
    /**
     * 자주 사용하는 함수 모음
     *
     * @see \App\TComponent
     */
    use TComponent, TContainer;

    /**
     * 포트 설정
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
                'device',
                'port',
                'state',
            ]);

            // $params = [
            //     'device' => 488529,
            //     'port'   => 23423,
            //     'state'  => 'Y',
            // ];

            if (empty($this->load(DeviceInfo::class)->get($params['device'])) === true) {
                $this->error('empty_system');
            }

            $this->load(DeviceVirtual::class)->state($params['device'], $params['port'], $params['state']);

        } catch (Exception $e) {
            return $res->withJson(['error' => $e->getMessage()]);
        }

        return $res->withJson($response);
    }
}
