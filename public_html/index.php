<?php

# service alias bootstrap
if (file_exists($bootstrap = dirname(__FILE__, 2) . DIRECTORY_SEPARATOR . 'bootstrap.php') === false) {
    exit('not boot file');
}

require_once $bootstrap;

use SlimFacades\App as Application;
use SlimFacades\Route;
use Psr\Http\Message\ServerRequestInterface;
use Middleware\Psr\Factory\ServerRequestFactory;
use Psr\Http\Server\RequestHandlerInterface;

# global variable $app;
$container->extend('view', function ($view, $c) {
    $view->setTemplatePath($c['path']['public_html']);
    return $view;
});

$isLogin = function (ServerRequestInterface $request, RequestHandlerInterface $handler) use ($container) {
    if (empty($container['session']->get('inform')) === true) {
        return $res->withRedirect($container['app']->getRouteCollector()->getNamedRoute('default')->getPattern());
    }
    return $handler->handle($request);
};

# Route::get('/test', \App\Company\Action\Xhr\Register::class);

Route::get('/', \App\Benefit\Action\Valid::class)->setName('default');
Route::post('/login', \App\Benefit\Action\Login::class);
Route::any('/logout', \App\Benefit\Action\Logout::class);

Route::get('/register', \App\Benefit\Action\Register::class)->setName('register');

Route::post('/confirm', \App\Benefit\Action\Confirm::class);
Route::get('/verification/{token}', \App\Benefit\Action\Verification::class);

Route::get('/password', \App\Benefit\Action\Password::class)->setName('password');
Route::post('/password', \App\Benefit\Action\Xhr\Password::class);

Route::get('/initialize/{token}', \App\Benefit\Action\Initialize::class)->setName('initialize');
Route::post('/initialize', \App\Benefit\Action\Xhr\Initialize::class);

Route::group('/dashboard', function ($app) {
    $app->get('', \App\Dashboard\Action\Main::class)->setName('dashboard');
    $app->post('/packet', \App\Dashboard\Action\Xhr\Packet::class);

    $app->group('/agent', function ($app) {
        $app->get('/lists', \App\Agent\Action\Lists::class); ####################
        $app->get('/detail/{agent}', \App\Agent\Action\Detail::class);

        $app->get('/register', \App\Agent\Action\Register::class); ######################
        $app->post('/register', \App\Agent\Action\Xhr\Register::class);  ###################

        $app->get('/modify/{agent}', \App\Agent\Action\Modify::class); ################
        $app->post('/modify', \App\Agent\Action\Xhr\Modify::class);####################

        $app->post('/access', \App\Agent\Action\Xhr\Access::class);
        $app->post('/deny', \App\Agent\Action\Xhr\Deny::class);

        $app->post('/remove', \App\Agent\Action\Xhr\Remove::class); ##################
        $app->post('/restore', \App\Agent\Action\Xhr\Restore::class);

        $app->post('/designate', \App\Agent\Action\Xhr\Designate::class);
        $app->post('/undesignate', \App\Agent\Action\Xhr\Undesignate::class);

        $app->group('/user', function ($app) {
            $app->post('/search', \App\User\Action\Xhr\Search::class);
            $app->post('/users', \App\Agent\Action\Xhr\Users::class);
        });

        $app->group('/company', function ($app) {
            $app->post('/search', \App\Company\Action\Xhr\Search::class);
            $app->post('/companies', \App\Agent\Action\Xhr\Companies::class);
        });

    });

    $app->group('/company', function ($app) {
        $app->get('/lists', \App\Company\Action\Lists::class);
        $app->get('/detail/{company}', \App\Company\Action\Detail::class);

        $app->get('/register', \App\Company\Action\Register::class);
        $app->post('/register', \App\Company\Action\Xhr\Register::class);

        $app->get('/modify/{company}', \App\Company\Action\Modify::class);
        $app->post('/modify', \App\Company\Action\Xhr\Modify::class);

        $app->post('/access', \App\Company\Action\Xhr\Access::class);
        $app->post('/deny', \App\Company\Action\Xhr\Deny::class);

        $app->post('/remove', \App\Company\Action\Xhr\Remove::class);
        $app->post('/restore', \App\Company\Action\Xhr\Restore::class);

        $app->post('/designate', \App\Company\Action\Xhr\Designate::class);
        $app->post('/undesignate', \App\Company\Action\Xhr\Undesignate::class);

        $app->group('/user', function ($app) {
            $app->post('/search', \App\User\Action\Xhr\Search::class);
            $app->post('/users', \App\Company\Action\Xhr\Users::class);
        });

        $app->group('/device', function ($app) {
            $app->post('/search', \App\Device\Action\Xhr\Search::class);
            $app->post('/devices', \App\Company\Action\Xhr\Devices::class);
        });
    });

    $app->group('/user', function ($app) {
        $app->get('/lists', \App\User\Action\Lists::class);

        $app->get('/register', \App\User\Action\Register::class);
        $app->post('/register', \App\User\Action\Xhr\Register::class);

        $app->get('/modify/{user}', \App\User\Action\Modify::class);
        $app->post('/modify', \App\User\Action\Xhr\Modify::class);

        $app->post('/access', \App\User\Action\Xhr\Access::class);
        $app->post('/deny', \App\User\Action\Xhr\Deny::class);

        $app->post('/remove', \App\User\Action\Xhr\Remove::class);
        $app->post('/restore', \App\User\Action\Xhr\Restore::class);
    });

    $app->group('/device', function ($app) {
        $app->get('/lists', \App\Device\Action\Lists::class);
        $app->post('/virtuallist', \App\Device\Action\Xhr\VirtualList::class);

        $app->get('/register', \App\Device\Action\Register::class);
        $app->post('/register', \App\Device\Action\Xhr\Register::class);

        $app->get('/modify/{device}', \App\Device\Action\Modify::class);
        $app->post('/modify', \App\Device\Action\Xhr\Modify::class);

        $app->post('/remove', \App\Device\Action\Xhr\Remove::class);
        $app->post('/restore', \App\Device\Action\Xhr\Restore::class);

        $app->get('/detail/{device}', \App\Device\Action\Detail::class);
        $app->group('/config', function ($app) {
            $app->post('/port', \App\Device\Action\Xhr\Port::class);
            $app->post('/state', \App\Device\Action\Xhr\State::class);
            $app->post('/user', \App\Device\Action\Xhr\User::class);
            $app->post('/remove', \App\Device\Action\Xhr\Remove::class);
        });
    });

    $app->group('/report', function ($app) {
        $app->map(['POST', 'GET'], '/analysis', \App\Report\Action\Analysis::class);
        $app->map(['POST', 'GET'], '/summary', \App\Report\Action\Summary::class);
    });
})->add($isLogin);

Application::run(ServerRequestFactory::createFromGlobals());
# error_log(json_encode(measure()));
# PHP_EOL;
