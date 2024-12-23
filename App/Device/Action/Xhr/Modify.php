<?php
namespace App\Device\Action\Xhr;

use App\Device\Domain\Info as DeviceInfo;
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
                'device',
                'name',
                'serial',
                'sw_version',
                'hw_version',
                'server',
            ]);

            // $params = [
            //     'device'     => 1,
            //     'name'       => 'name',
            //     'serial'     => 'ceo',
            //     'sw_version' => 'business',
            //     'hw_version' => 'a 123123',
            //     'server'     => 'dsafasd 1231a',
            // ];

            $deviceInfo = $this->load(DeviceInfo::class);
            if (empty($deviceInfo->get($params['device'])) === true) {
                $this->error('empty_system');
            }

            $deviceInfo->modify($params['device'], [
                'name'       => $params['name'],
                'serial'     => $params['serial'],
                'sw_version' => $params['sw_version'],
                'hw_version' => $params['hw_version'],
                'server'     => $params['server'],
            ]);

        } catch (Exception $e) {
            return $res->withJson(['error' => $e->getMessage()]);
        }

        return $res->withJson($response);
    }
}
