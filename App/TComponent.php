<?php
namespace App;

/**
 *
 */
trait TComponent
{
    // const REDIS_TTL = 3600;
    protected $redisTTL = 3600;

    /**
     * [cacheRead description]
     * @param  string      $key   [description]
     * @param  string|null $token [description]
     * @return [type]             [description]
     */
    protected function cacheRead(string $key = 'token', string $token = null)
    {
        $response = [];
        if (empty($cache = $this->redis->get($key)) === false) {
            $response = json_decode($cache, true);
        }

        if (empty($token) === false) {
            return (empty($response[$token]) === false) ? $response[$token] : false;
        }

        return $response;
    }

    /**
     * [cacheRemove description]
     * @param  string      $key   [description]
     * @param  string|null $token [description]
     * @return [type]             [description]
     */
    protected function cacheRemove(string $key = 'token', string $token = null, int $ttl = 0)
    {
        if ($ttl === 0) {
            $ttl = $this->redisTTL;
        }

        $response = [];
        if (empty($cache = $this->redis->get($key)) === false) {
            $response = json_decode($cache, true);
        }

        if (empty($response[$token]) === true) {
            return;
        }
        unset($response[$token]);
        $this->redis->setEx($key, $ttl, $response);
    }

    /**
     * [informWrite description]
     * @param  string      $key   [description]
     * @param  string|null $token [description]
     * @param  string|null $info  [description]
     * @param  int|integer $ttl   [description]
     * @return [type]             [description]
     */
    protected function cacheWrite(string $key = 'token', string $token = null, string $info = null, int $ttl = 0)
    {
        if ($ttl === 0) {
            $ttl = $this->redisTTL;
        }

        $cache         = $this->cacheRead($key);
        $cache[$token] = $info;
        $cache         = json_encode($cache);

        // $this->redis->set($key, $cache);
        $this->redis->setEx($key, $ttl, $cache);
        return $cache;
    }

    /**
     * [checked description]
     * @param  array  $validation [description]
     * @return [type]             [description]
     */
    protected function checked(array $validation = [])
    {
        $params = $this->request->getParams();
        $params = $this->valid($params);
        if (empty($params) === true) {
            $this->error('not_network');
        }

        if (empty($validation) === true) {
            return $params;
        }

        // todo. require
        foreach ($validation as $valid) {
            if (isset($params[$valid]) === false) {
                error_log(json_encode([$validation, $params, $valid]));
                $this->error('not_data');
            }
        }

        return array_intersect_key($params, array_flip($validation));
    }

    /**
     * [cut description]
     * @param  string|null $string [description]
     * @param  int|integer $limit  [description]
     * @return [type]              [description]
     */
    protected function cut(string $string = null, int $limit = 45)
    {
        if (mb_strlen($string, 'UTF-8') <= $limit) {
            return $string;
        }

        return mb_substr($string, 0, $limit, 'UTF-8');
    }

    /**
     * [getUserInfo description]
     * @return [type] [description]
     */
    protected function getUserInfo()
    {
        return $this->session->get('inform');
    }

    /**
     * [isLogin description]
     * @return boolean [description]
     */
    protected function isLogin()
    {
        return (empty($this->getUserInfo()) === true) ? false : true;
    }

    /**
     * [layout description]
     * @param  string|null $template [description]
     * @param  array       $data     [description]
     * @param  string      $type     [description]
     * @return [type]                [description]
     */
    protected function layout(string $template = null, $data = [], string $type = 'default')
    {
        $header = $footer = null;
        switch ($type) {
            case 'default':
                $header = 'html/layout/default_header.html';
                $footer = 'html/layout/default_footer.html';
                break;

            default:
                break;
        }

        $view = $this->view;
        return $view->render($this->response, 'html/layout/default.html', [
            'header'   => $view->fetch($header),
            'contents' => $view->fetch($template, $data),
            'footer'   => $view->fetch($footer),
        ]);
    }

    /**
     * [render description]
     * @param  string|null $template [description]
     * @param  array       $data     [description]
     * @return [type]                [description]
     */
    protected function render(string $template = null, array $data = [])
    {
        return $this->view->render($this->response, $template, $data);
    }

    /**
     * [setUserInfo description]
     * @param array $inform [description]
     */
    protected function setUserInfo(array $inform = [])
    {
        return $this->session->set('inform', $inform);
    }

    /**
     * [show description]
     * @param  string|null $message  [description]
     * @param  string|null $redirect [description]
     * @return [type]                [description]
     */
    protected function show(string $message = null, string $redirect = null)
    {
        return $this->render('html/show.html', [
            'message'  => $message,
            'redirect' => $redirect,
        ]);
    }

    /**
     * [valid description]
     * @param  array  $args [description]
     * @return [type]       [description]
     */
    protected function valid(array $args = [])
    {
        if (empty($args) === true) {
            return $args;
        }

        return filter_var_array($args, array_fill_keys(array_keys($args), [
            'filter'  => FILTER_CALLBACK,
            'options' => function (string $value = null) {
                $value = trim($value);
                # $value = stripslashes($value);
                $value = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $value);
                # $value = preg_replace('/[\r\n\s\t\'\;\"\=\-\-\#\/*]+/', '', $value);
                return htmlspecialchars($value, ENT_QUOTES, 'UTF-8', true);
            },
        ]));
    }
}
