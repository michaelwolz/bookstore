<?php

namespace maw\core;

use maw\controller\Controller,
    maw\controller\Error_Controller;

class Router
{
    private $url = [];

    public function add($url, $method = 'default', $id)
    {
        $this->url[] = [
            'url' => '/' . trim($url, '/'),
            'controller' => $method,
            'id' => $id
        ];
    }

    public function route()
    {
        $params = self::getURLParams();
        $found = false;
        foreach ($this->url as $item) {
            if (preg_match("#^" . $item['url'] . "$#", $params['controller'])) {
                $found = true;
                if (class_exists('\maw\controller\\' . $item['controller'] . '_Controller')) {
                    $class = '\maw\controller\\' . $item['controller'] . '_Controller';
                    $controller = new $class($item['id']);
                } else
                    $controller = new Controller($item['id']);

                if (isset($params['action']) && method_exists($controller, $params['action']))
                    $controller->{$params['action']}($params['id']);
                else
                    $controller->render();
            }
        }

        if (!$found) {
            $controller = new Error_Controller(404);
            $controller->render();
        }
    }

    public static function getURLParams()
    {
        $params = [];
        $url = isset($_GET['url']) ? $_GET['url'] : '/';
        $urlArray = explode('/', $url);
        $params['controller'] = '/' . $urlArray[0];
        if (isset($urlArray[1])) $params['action'] = $urlArray[1];
        $params['id'] = isset($urlArray[2]) ? $urlArray[2] : 0;
        return $params;
    }
}