<?php

namespace site\Model;

use site\Helper\GeneratorModel;

class GameRowModel {
    const GAME_STATUS = [
        'START'  => 1,
        'COMBAT' => 2,
        'END'    => 3
    ];
    protected int $id;
    protected int $status;
    protected string $firstPlayerCode;
    protected string $secondPlayerCode;
    protected string $playerTurn;
    protected bool $firstPlayerReady;
    protected bool $secondPlayerReady;
    protected GameModel $gameModel;

    public function __construct(?array $data = null) {
        $this->gameModel = new GameModel();

        if ($data) {
            $this->id                = $data['ID'];
            $this->status            = $data['STATUS'];
            $this->firstPlayerCode   = $data['PLAYER_CODE_1'];
            $this->secondPlayerCode  = $data['PLAYER_CODE_2'];
            $this->playerTurn        = $data['PLAYER_TURN'];
            $this->firstPlayerReady  = $data['PLAYER_1_READY'];
            $this->secondPlayerReady = $data['PLAYER_2_READY'];
        }
    }

    public function setId(int $id): void {
        $this->id = $id;
    }

    public function setFirstPlayerCode(string $firstPlayerCode): void {
        $this->firstPlayerCode = $firstPlayerCode;
    }

    public function setSecondPlayerCode(string $secondPlayerCode): void {
        $this->secondPlayerCode = $secondPlayerCode;
    }

    public function setPlayerTurn(string $playerTurn): void {
        $this->playerTurn = $playerTurn;
    }

    public function getId(): int {
        return $this->id;
    }

    public function getStatus(): int {
        return $this->status;
    }

    public function getFirstPlayerCode(): string {
        return $this->firstPlayerCode;
    }

    public function getSecondPlayerCode(): string {
        return $this->secondPlayerCode;
    }

    public function getPlayerTurn(): string {
        return $this->playerTurn;
    }

    public function isFirstPlayerReady(): bool {
        return $this->firstPlayerReady;
    }

    public function isSecondPlayerReady(): bool {
        return $this->secondPlayerReady;
    }

    public function setGameStatus(int $gameStatus): array {
        $success = $this->gameModel->update($this->id, ['STATUS' => $gameStatus]);

        if (!$success) {
            return GeneratorModel::generateError('Db-Error', 'Невозможно изменить статус игры');
        }

        $info['success'] = true;

        return $info;
    }

    public function swapTurn(): array {
        $player = PlayerModel::getPlayer($this, $this->playerTurn);

        $success = $this->gameModel->update($this->id, ['PLAYER_TURN' => $player->getEnemy()]);

        if (!$success) {
            return GeneratorModel::generateError('Db-Error', 'Невозможно сменить ход игрока');
        }

        $info['success'] = true;

        return $info;
    }

    public function playerExist(string $playerCode): array {
        if ($playerCode != $this->firstPlayerCode && $playerCode != $this->secondPlayerCode) {
            return GeneratorModel::generateError('Db-Error', 'Невозможно получить информацию об игроке!');
        }

        $info['success'] = true;

        return $info;
    }

    public function canPlayerTakeShot(string $playerCode): array {
        if ($this->status === GameRowModel::GAME_STATUS['END']) {
            return GeneratorModel::generateError('Game-Error', 'Игра окончена!');
        }

        if ($this->playerTurn != $playerCode) {
            return GeneratorModel::generateError('Game-Error', 'Сейчас ходит ваш оппонент!');
        }

        $info['success'] = true;

        return $info;
    }
}
