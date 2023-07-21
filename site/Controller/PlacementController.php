<?php

namespace site\Controller;

use core\Controller\Controller;
use core\View\JsonView;
use core\View\View;
use site\Helper\GeneratorModel;

class PlacementController extends Controller {
    public function placeShipAction(): View {
        $post = $this->request->post();

        $info = $this->checkRequest($this->param['gameId'], $this->param['playerId']);

        if (!$info['success']) {
            return new JsonView($info);
        }

        if ($this->currentPlayer->isMeReady()) {
            return new JsonView(GeneratorModel::generateError('Game-error', 'Невозможно поставить корабль, так как вы нажали кнопку готовности'));
        }

        $currentField = $this->fieldModel->getFieldInfo($this->param['gameId'], $this->param['playerId']);

        if (!$currentField) {
            return new JsonView(GeneratorModel::generateError('Db-Error', 'Невозможно получить информацию о поле игрока!'));
        }

        if (isset($post['ships'])) {
            $info = $currentField->packagePlacement($post['ships']);

            return new JsonView($info);
        }

        if (!isset($post['orientation']) && isset($post['ship'])) {
            $info = $currentField->deleteShip($post);

            return new JsonView($info);
        }

        $info = $currentField->shipPlacement($post);

        return new JsonView($info);
    }

    public function clearFieldAction(): View {
        $info = $this->checkRequest($this->param['gameId'], $this->param['playerId']);

        if (!$info['success']) {
            return new JsonView($info);
        }

        if ($this->currentPlayer->isMeReady()) {
            return new JsonView(GeneratorModel::generateError('Game-error', 'Невозможно очистить поле, так как вы нажали кнопку готовности'));
        }

        $currentField = $this->fieldModel->getFieldInfo($this->param['gameId'], $this->param['playerId']);

        if (!$currentField) {
            return new JsonView(GeneratorModel::generateError('Db-Error', 'Невозможно получить информацию о поле игрока!'));
        }

        $info = $currentField->clearField();

        return new JsonView($info);
    }
}
