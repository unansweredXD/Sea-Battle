<?php

use core\Router\Router;

spl_autoload_register();

$router = new Router($_SERVER['REQUEST_URI']);

$currentRoute = $router->run();

$view = $currentRoute->runController();

$view->render();
