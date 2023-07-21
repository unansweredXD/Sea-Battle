<?php

namespace site\Model;

use site\Helper\GeneratorModel;

class PlayerModel {
    const PLAYER_NUMBER = [
        'first'  => 1,
        'second' => 2
    ];
    protected string $code;
    protected int $number;
    protected string $enemy;
    protected bool $isMyTurn;
    protected bool $meReady;
    protected GameRowModel $game;

    public function getCode(): string {
        return $this->code;
    }

    public function getEnemy(): string {
        return $this->enemy;
    }

    public function isMeReady(): bool {
        return $this->meReady;
    }

    protected function setPlayerNumber(): void {
        if ($this->game->getFirstPlayerCode() === $this->code) {
            $this->number = PlayerModel::PLAYER_NUMBER['first'];
        } else {
            $this->number = PlayerModel::PLAYER_NUMBER['second'];
        }
    }

    protected function setEnemy(): void {
        if ($this->game->getFirstPlayerCode() === $this->code) {
            $this->enemy = $this->game->getSecondPlayerCode();
        } else {
            $this->enemy = $this->game->getFirstPlayerCode();
        }
    }

    protected function setIsMyTurn(): void {
        $this->isMyTurn = $this->game->getPlayerTurn() === $this->code;
    }

    protected function setMeReady(): void {
        if ($this->number === PlayerModel::PLAYER_NUMBER['first']) {
            $this->meReady = $this->game->isFirstPlayerReady();
        } else {
            $this->meReady = $this->game->isSecondPlayerReady();
        }
    }

    public static function getPlayer(GameRowModel $game, string $code): static {
        $player = new static();

        $player->game = $game;
        $player->code = $code;

        $player->setPlayerNumber();

        $player->setEnemy();

        $player->setIsMyTurn();

        $player->setMeReady();

        return $player;
    }

    public function getPlayerInfo(): array {
        $playerInfo['game']['myTurn'] = $this->isMyTurn;

        $playerInfo['game']['invite']  = $this->enemy;
        $playerInfo['game']['meReady'] = $this->meReady;

        $fieldModel = new FieldModel();

        $playerField = $fieldModel->getFieldInfo($this->game->getId(), $this->code);
        $enemyField  = $fieldModel->getFieldInfo($this->game->getId(), $this->enemy);

        if (!$playerField || !$enemyField) {
            return GeneratorModel::generateError('Db-Error', 'Невозможно получить информацию о поле игрока!');
        }

        $playerInfo['fieldMy']    = $playerField->getFieldForFront();
        $playerInfo['fieldEnemy'] = $enemyField->getFieldForFront(true);

        $playerInfo['usedPlaces'] = $playerField->getUsedPlaces();

        $playerInfo['success'] = true;

        return $playerInfo;
    }

    protected function getPlayerForDb(): string {
        return 'PLAYER_' . $this->number . '_READY';
    }

    public function playerReady(): array {
        $gameDb = new GameModel();

        $success = $gameDb->update($this->game->getId(), [$this->getPlayerForDb() => true]);

        if (!$success) {
            return GeneratorModel::generateError('Db-Error', 'Невозможно подтвердить готовность');
        }

        $info['success'] = true;

        return $info;
    }
}
