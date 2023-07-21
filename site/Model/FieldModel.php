<?php

namespace site\Model;

use core\Db\QueryBuilder;

class FieldModel extends QueryBuilder {
    protected string $table = 'cell';

    public function generateFieldForPlayer(int $gameId, string $playerCode): ?FieldRowModel {
        $fieldInfo = [
            'GAME_ID' => $gameId,
            'IS_SHIP' => false,
            'IS_HIT'  => false,
        ];

        for ($y = 0; $y < 10; $y++) {
            for ($x = 0; $x < 10; $x++) {
                $fieldInfo['Y_COORD'] = $y;
                $fieldInfo['X_COORD'] = $x;

                $fieldInfo['PLAYER_CODE'] = $playerCode;

                $success = $this->add($fieldInfo);

                if (!$success) {
                    return null;
                }
            }
        }

        return new FieldRowModel($gameId, $playerCode);
    }

    public function getFieldInfo(int $gameId, string $playerCode): ?FieldRowModel {
        $dbData = $this->select([
            'ID',
            'IS_SHIP',
            'SHIP_TYPE',
            'IS_HIT',
            'X_COORD',
            'Y_COORD'
        ])
            ->where([
                'GAME_ID'     => $gameId,
                'PLAYER_CODE' => $playerCode
            ])
            ->fetchAll();

        if (!$dbData) {
            return null;
        }

        return new FieldRowModel($gameId, $playerCode, $dbData);
    }
}
