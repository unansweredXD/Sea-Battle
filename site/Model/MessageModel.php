<?php

namespace site\Model;

use core\Db\QueryBuilder;
use site\Helper\GeneratorModel;

class MessageModel extends QueryBuilder {
    protected string $table = 'message';
    protected string $playerCode;
    protected int $gameId;
    protected string $text;
    protected int $time;

    public function __construct(int $gameId = 0, string $playerCode = '', string $text = '') {
        $this->gameId     = $gameId;
        $this->playerCode = $playerCode;
        $this->text       = $text;
        $this->time       = time();
    }

    public function getMessageInfo(string $playerCode, ?int $time = null): array {
        $this->select(['*'], true);

        if ($time) {
            $this->where([
                'GAME_ID' => $this->gameId,
                'TIME'    => [
                    'BETWEEN' => [
                        $time + 1,
                        $time + 5
                    ]
                ]
            ], true);
        } else {
            $this->where(['GAME_ID' => $this->gameId], true);
        }

        $dbData = $this->order(['ID' => 'ASC'], true)
            ->fetchAll();

        if (is_null($dbData)) {
            return [];
        }

        $messageList = [];

        foreach ($dbData as $item) {
            $isMy = $item['PLAYER_CODE'] === $playerCode;

            $messageList[] = [
                'my'      => $isMy,
                'time'    => $item['TIME'],
                'message' => $item['TEXT']
            ];
        }

        return $messageList;
    }

    public function send(): array {
        $success = $this->add([
            'PLAYER_CODE' => $this->playerCode,
            'GAME_ID'     => $this->gameId,
            'TEXT'        => $this->text,
            'TIME'        => $this->time
        ]);

        if (!$success) {
            return GeneratorModel::generateError('Db-Error', 'Не удалось отправить сообщение');
        }

        $info['success'] = true;

        return $info;
    }
}
