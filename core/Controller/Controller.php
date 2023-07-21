<?php

namespace core\Controller;

use core\Request;
use site\Helper\ValidatorModel;
use site\Model\FieldModel;
use site\Model\GameModel;
use site\Model\GameRowModel;
use site\Model\PlayerModel;

abstract class Controller {
    protected GameModel $gameModel;
    protected FieldModel $fieldModel;
    protected Request $request;
    protected ?GameRowModel $currentGame;
    protected PlayerModel $currentPlayer;
    protected array $param;

    public function __construct() {
        $this->gameModel  = new GameModel();
        $this->fieldModel = new FieldModel();
        $this->request    = Request::getInstance();
        $this->param      = $this->request->param();
    }

    protected function checkRequest(int $gameId, string $playerCode): array {
        $this->currentGame = $this->gameModel->getGameInfo($gameId);

        $validator = new Validator();

        $info = $validator->validateGame($this->currentGame, $playerCode);

        if (!$info['success']) {
            return $info;
        }

        $this->currentPlayer = PlayerModel::getPlayer($this->currentGame, $this->param['playerId']);

        return $info;
    }
}
