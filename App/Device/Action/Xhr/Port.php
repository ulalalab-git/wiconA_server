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
class Port extends HandlerKernel
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
                'user',
                'port',
            ]);

            // $params = [
            //     'device' => 488529,
            //     'user'   => 234,
            //     'port'   => 23423,
            // ];

            if (empty($this->load(DeviceInfo::class)->get($params['device'])) === true) {
                $this->error('empty_system');
            }

            $this->load(DeviceVirtual::class)->register([
                'device' => $params['device'],
                'user'   => $params['user'],
                'port'   => $params['port'],
                'state'  => 'Y',
            ]);

        } catch (Exception $e) {
            return $res->withJson(['error' => $e->getMessage()]);
        }

        return $res->withJson($response);
    }
}
