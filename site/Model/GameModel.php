<?php

namespace site\Model;

use core\Db\QueryBuilder;
use site\Helper\GeneratorModel;

class GameModel extends QueryBuilder {
    protected string $table = 'game';

    public function generateGame(): array {
        $currentGame = new GameRowModel();

        $currentGame->setFirstPlayerCode(GeneratorModel::generatePlayerCode());
        $currentGame->setSecondPlayerCode(GeneratorModel::generatePlayerCode());

        $playerTurn = GeneratorModel::generatePlayerTurn($currentGame->getFirstPlayerCode(), $currentGame->getSecondPlayerCode());
        $currentGame->setPlayerTurn($playerTurn);

        $currentData = [
            'STATUS'         => GameRowModel::GAME_STATUS['START'],
            'PLAYER_CODE_1'  => $currentGame->getFirstPlayerCode(),
            'PLAYER_CODE_2'  => $currentGame->getSecondPlayerCode(),
            'PLAYER_TURN'    => $currentGame->getPlayerTurn(),
            'PLAYER_1_READY' => false,
            'PLAYER_2_READY' => false
        ];

        $gameId = $this->add($currentData);

        if (!$gameId) {
            return GeneratorModel::generateError('Db-Error', 'Невозможно создать игру, попробуйте еще раз!');
        }

        $currentGame->setId($gameId);

        $fieldModel = new FieldModel();

        $fieldFirstPlayer  = $fieldModel->generateFieldForPlayer($currentGame->getId(), $currentGame->getFirstPlayerCode());
        $fieldSecondPlayer = $fieldModel->generateFieldForPlayer($currentGame->getId(), $currentGame->getSecondPlayerCode());

        if (!$fieldFirstPlayer || !$fieldSecondPlayer) {
            return GeneratorModel::generateError('Db-Error', 'Невозможно создать поля для игроков, попробуйте еще раз!');
        }

        $info['id']      = $currentGame->getId();
        $info['code']    = $currentGame->getFirstPlayerCode();
        $info['invite']  = $currentGame->getSecondPlayerCode();
        $info['success'] = true;

        return $info;
    }

    public function getGameInfo(int $id): ?GameRowModel {
        $dbData = $this->select(['*'])
            ->where(['ID' => $id])
            ->fetch();

        if (!$dbData) {
            return null;
        }

        return new GameRowModel($dbData);
    }
}
