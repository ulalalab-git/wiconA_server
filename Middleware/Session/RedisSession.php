<?php
namespace Middleware\Session;

use Middleware\Session\RedisSessionHandler;
use Predis\Client as RedisClient;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Session 을 Redis 에 연동하여 사용할 수 있도록 제공합니다.
 * 같이 제공하는 Helper 를 이용하여 사용할 수 있습니다.
 * 기존과 동일하게 $_SESSION 도 사용 할 수 있습니다.
 *
 *
 * usage
 * 1. set 하는 방법
 *  tobe. $_SESSION['set'] = 'temp';
 *  asis. $this->session->set('set', 'temp');
 *
 * 2. get 하는 방법
 *  tobe. $_SESSION['set'];
 *  asis. $this->session->get('set');
 *
 */
class RedisSession implements MiddlewareInterface
{
    /**
     * [__construct description]
     * @param [type] $uri      [description]
     * @param array  $settings [description]
     */
    public function __construct(RedisClient $redis, $settings = [])
    {
        $settings = array_merge([
            'session.name'    => 'atlas_session',
            'session.id'      => '',
            // 'session.expires' => ini_get('session.gc_maxlifetime'),
            'session.expires' => 86400,
            'cookie.lifetime' => 43200,
            'cookie.path'     => '/',
            'cookie.domain'   => '',
            'cookie.secure'   => false,
            'cookie.httponly' => true,
            'autorefresh'     => false,
        ], $settings);

        if (is_string($settings['session.expires'])) {
            $settings['session.expires'] = intval($settings['session.expires']);
        }

        ini_set('session.use_strict_mode', 1);
        ini_set('session.use_cookies', 1);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.use_trans_sid', 0);
        ini_set('session.serialize_handler', 'php_serialize');

        ini_set('session.gc_probability', 1);
        ini_set('session.gc_divisor', 1000);
        ini_set('session.gc_maxlifetime', $settings['session.expires']);

        // cookies blah!
        session_set_cookie_params(
            $settings['cookie.lifetime'],
            $settings['cookie.path'],
            $settings['cookie.domain'],
            $settings['cookie.secure'],
            $settings['cookie.httponly']
        );

        if (session_status() !== PHP_SESSION_NONE) {
            return true;
        }

        // ini_set('session.save_handler', 'redis');
        // ini_set('session.save_path', $uri);

        // 통합 로그인 섹션 관리 부분
        session_name($settings['session.name']);
        ini_set('session.name', $settings['session.name']);
        // session_id('tests');
        session_set_save_handler(new RedisSessionHandler($redis, $settings['session.expires'], $settings['session.name']));
        @session_start();
    }

    /**
     * [__destruct description]
     */
    public function __destruct()
    {
        @session_write_close();
    }

    /**
     * [process description]
     * @param  ServerRequestInterface $request  [description]
     * @param  ResponseInterface      $response [description]
     * @param  callable               $next     [description]
     * @return [type]                           [description]
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $handler->handle($request);
    }
}
