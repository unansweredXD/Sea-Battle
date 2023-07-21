<?php

namespace site\Controller;

use core\Controller\Controller;
use core\View\JsonView;
use core\View\View;
use site\Helper\GeneratorModel;
use site\Model\GameRowModel;
use site\Model\PlayerModel;

class ActionController extends Controller {
    public function readyAction(): View {
        $info = $this->checkRequest($this->param['gameId'], $this->param['playerId']);

        if (!$info['success']) {
            return new JsonView($info);
        }

        $info = $this->currentPlayer->playerReady();

        $currentEnemy = PlayerModel::getPlayer($this->currentGame, $this->currentPlayer->getEnemy());

        $info['enemyReady'] = $currentEnemy->isMeReady();

        if ($info['enemyReady']) {
            $info = array_merge($info, $this->currentGame->setGameStatus(GameRowModel::GAME_STATUS['COMBAT']));
        }

        return new JsonView($info);
    }

    public function shotAction(): View {
        $info = $this->checkRequest($this->param['gameId'], $this->param['playerId']);

        if (!$info['success']) {
            return new JsonView($info);
        }

        $info = $this->currentGame->canPlayerTakeShot($this->currentPlayer->getCode());

        if (!$info['success']) {
            return new JsonView($info);
        }

        $currentField = $this->fieldModel->getFieldInfo($this->param['gameId'], $this->currentPlayer->getEnemy());

        if (!$currentField) {
            return new JsonView(GeneratorModel::generateError('Db-Error', 'Невозможно получить информацию о поле игрока!'));
        }

        $info = $currentField->takeShot($this->request->post());

        if (!$info['success']) {
            return new JsonView($info);
        }

        if (!$info['isShip']) {
            $info = $this->currentGame->swapTurn();

            return new JsonView($info);
        }

        if ($currentField->isLastShip()) {
            $info = $this->currentGame->setGameStatus(GameRowModel::GAME_STATUS['END']);
        }

        return new JsonView($info);
    }
}
