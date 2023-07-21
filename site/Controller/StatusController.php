<?php

namespace site\Controller;

use core\Controller\Controller;
use core\View\JsonView;
use core\View\View;

class StatusController extends Controller {
    public function getStatusAction(): View {
        $info = $this->checkRequest($this->param['gameId'], $this->param['playerId']);

        if (!$info['success']) {
            return new JsonView($info);
        }

        $info                   = $this->currentPlayer->getPlayerInfo();
        $info['game']['id']     = $this->currentGame->getId();
        $info['game']['status'] = $this->currentGame->getStatus();

        return new JsonView($info);
    }
}
