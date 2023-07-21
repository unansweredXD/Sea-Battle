<?php

namespace core\Router;

use core\Request;
use site\Controller\ErrorController;

class Router {
    protected string $uri = '';

    public function __construct(string $uri) {
        $this->uri = $uri;
    }

    protected function getRoute(string $path): ResultRoute {
        $routeMap = require 'route-map.php';

        foreach ($routeMap as $serverRoute) {
            $match = preg_match('/' . $serverRoute['route'] . '/', $path, $param);
            if ($match && $_SERVER['REQUEST_METHOD'] === $serverRoute['method']) {

                $body = file_get_contents('php://input');
                $json = json_decode($body ?: '', true);

                Request::getInstance()->setParam($param)->setBody($json)->setPost($_POST)->setGet($_GET);

                return new ResultRoute($serverRoute['controller'], $serverRoute['action']);
            }
        }

        return new ResultRoute(ErrorController::class, 'notFoundAction');
    }

    public function run(): ResultRoute {
        $path = parse_url($this->uri)['path'];

        if (!$path) {
            return new ResultRoute(ErrorController::class, 'badRequestAction');
        }

        return $this->getRoute($path);
    }
}
