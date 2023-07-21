<?php

namespace core\Router;

use core\View\View;

class ResultRoute {
    protected string $controller = '';
    protected string $action = '';

    public function __construct(string $controller, string $action) {
        $this->controller = $controller;
        $this->action     = $action;
    }

    public function runController(): View {
        $controller = new $this->controller();

        return $controller->{$this->action}();
    }
}
