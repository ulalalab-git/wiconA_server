<?php
namespace App\Handler\Responder;

use App\TComponent;
use App\TContainer;
use Exception;

/**
 *
 */
class Layout
{
    /**
     * 자주 사용하는 함수 모음
     *
     * @see \App\TComponent
     */
    use TComponent, TContainer;

    private $css = [];

    private $js = [];

    public function appendCss(string $css = null)
    {
        $this->css[] = $css;
    }

    public function appendJs(string $js = null)
    {
        $this->js[] = $js;
    }

    public function removeCss(string $css = null)
    {
        $tmp = $this->css;
        $key = array_keys($tmp, $css);
        if (empty($key) === false) {
            $tmp = array_slice($tmp, $key[0], 1);
        }
        $this->css = $tmp;
    }

    public function removeJs(string $js = null)
    {
        $tmp = $this->js;
        $key = array_keys($tmp, $js);
        if (empty($key) === false) {
            $tmp = array_slice($tmp, $key[0], 1);
        }
        $this->js = $tmp;
    }

    public function render(string $template = null, $data = [])
    {
        $path = $this->request->getUri()->getPath();
        $path = str_replace('/dashboard', '', $path);

        $map = 'dashboard';
        switch (true) {
            case (strpos($path, '/report') !== false):
                $map = 'report';
                break;

            case (strpos($path, '/device') !== false):
                $map = 'device';
                break;

            case (strpos($path, '/agent') !== false):
                $map = 'agent';
                break;

            case (strpos($path, '/company') !== false):
                $map = 'company';
                break;

            case (strpos($path, '/user') !== false):
                $map = 'user';
                break;

            default:
                break;
        }

        $header = [
            'title' => __('menu_dashboard'),
            'menu'  => [
                'dashboard' => [
                    'name'   => __('menu_dashboard'),
                    'active' => false,
                    'link'   => '/dashboard',
                    'icon'   => 'ti-dashboard',
                ],
                'report'    => [
                    'name'   => __('menu_report'),
                    'active' => false,
                    'link'   => '/dashboard/report/analysis',
                    'icon'   => 'zmdi zmdi-collection-text',
                ],
                'device'    => [
                    'name'   => __('menu_device'),
                    'active' => false,
                    'link'   => '/dashboard/device/lists',
                    'icon'   => 'zmdi zmdi-collection-item',
                ],
                'company'   => [
                    'name'   => __('menu_company'),
                    'active' => false,
                    'link'   => '/dashboard/company/lists',
                    'icon'   => 'zmdi zmdi-codepen',
                ],
                'user'      => [
                    'name'   => __('menu_user'),
                    'active' => false,
                    'link'   => '/dashboard/user/lists',
                    'icon'   => 'zmdi zmdi-view-list',
                ],
                'agent'     => [
                    'name'   => __('menu_agent'),
                    'active' => false,
                    'link'   => '/dashboard/agent/lists',
                    'icon'   => 'zmdi zmdi-code',
                ],
            ],
        ];

        $header['menu'][$map]['active'] = true;

        $header['title'] = $header['menu'][$map]['name'];
        $header['info']  = $this->getUserInfo();

        // $this->{$key};
        if (empty($this->css) === false) {
            $path = $this->path['public_html']; // DIRECTORY_SEPARATOR
            foreach ($this->css as $css) {
                if (file_exists($path . $css) === false) {
                    continue;
                }
                $header['css'][] = $css;
            }
        }

        $footer = [];
        if (empty($this->js) === false) {
            $path = $this->path['public_html']; // DIRECTORY_SEPARATOR
            foreach ($this->js as $js) {
                if (file_exists($path . $js) === false) {
                    continue;
                }
                $footer['js'][] = $js;
            }
        }

        $view = $this->view;
        return $view->render($this->response, 'html/layout/default.html', [
            'header'   => $view->fetch('html/layout/dash_header.html', $header),
            'contents' => $view->fetch($template, $data),
            'footer'   => $view->fetch('html/layout/dash_footer.html', $footer),
        ]);
    }

    public function setCss(array $css = [])
    {
        $this->css = $css;
    }

    public function setJs(array $js = [])
    {
        $this->js = $js;
    }
}
