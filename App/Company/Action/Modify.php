<?php
namespace App\Company\Action;

use App\Company\Domain\Info as CompanyInfo;
use App\Handler\Kernel as HandlerKernel;
use App\Handler\Responder\Layout as HandlerLayout;
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
     * 회사 수정 페이지
     *
     * @param  Request    $req  \Psr\Http\Message\ServerRequestInterface
     * @param  Response   $res  \Psr\Http\Message\ResponseInterface
     * @param  array|null $args URL 맵핑 정규식 데이터 - 참조(\Slim\DeferredCallable)
     * @return Response         \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(ServerRequestInterface $req, ResponseInterface $res, $args = null)
    {
        if (empty($args['company']) === true) {
            return $this->show(__('empty_system'), "window.location.href = '/';");
        }

        $company = $this->load(CompanyInfo::class)->get($args['company']);
        if (empty($company) === true) {
            return $this->show(__('empty_system'), "javascript:history.go(-1);");
        }

        $handlerLayout = $this->load(HandlerLayout::class);
        $handlerLayout->appendJs('/js/dashboard_company_manager.js');
        return $handlerLayout->render('html/dashboard/company/register.html', [
            'company' => $company,
        ]);
    }
}
