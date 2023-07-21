<?php

use site\Controller\ActionController;
use site\Controller\IndexController;
use site\Controller\MessageController;
use site\Controller\PlacementController;
use site\Controller\StatusController;

return [
    [
        'route'      => '^\/api\/start\/?$',
        'controller' => IndexController::class,
        'action'     => 'startAction',
        'method'     => 'POST'
    ],
    [
        'route'      => '^\/api\/status\/(?<gameId>\d*)\/(?<playerId>\w{10})\/?$',
        'controller' => StatusController::class,
        'action'     => 'getStatusAction',
        'method'     => 'GET'
    ],
    [
        'route'      => '^\/api\/status\/(?<gameId>\d*)\/(?<playerId>\w{10})\/?$',
        'controller' => StatusController::class,
        'action'     => 'getStatusAction',
        'method'     => 'POST'
    ],
    [
        'route'      => '^\/api\/place-ship\/(?<gameId>\d*)\/(?<playerId>\w{10})\/?$',
        'controller' => PlacementController::class,
        'action'     => 'placeShipAction',
        'method'     => 'POST'
    ],
    [
        'route'      => '^\/api\/clear-field\/(?<gameId>\d*)\/(?<playerId>\w{10})\/?$',
        'controller' => PlacementController::class,
        'action'     => 'clearFieldAction',
        'method'     => 'POST'
    ],
    [
        'route'      => '^\/api\/ready\/(?<gameId>\d*)\/(?<playerId>\w{10})\/?$',
        'controller' => ActionController::class,
        'action'     => 'readyAction',
        'method'     => 'POST'
    ],
    [
        'route'      => '^\/api\/shot\/(?<gameId>\d*)\/(?<playerId>\w{10})\/?$',
        'controller' => ActionController::class,
        'action'     => 'shotAction',
        'method'     => 'POST'
    ],
    [
        'route'      => '^\/api\/chat-load\/(?<gameId>\d*)\/(?<playerId>\w{10})\/?$',
        'controller' => MessageController::class,
        'action'     => 'loadAction',
        'method'     => 'GET'
    ],
    [
        'route'      => '^\/api\/chat-send\/(?<gameId>\d*)\/(?<playerId>\w{10})\/?$',
        'controller' => MessageController::class,
        'action'     => 'sendAction',
        'method'     => 'POST'
    ],
];
