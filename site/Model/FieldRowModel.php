<?php

namespace site\Model;

use site\Helper\ValidatorModel;
use site\Helper\GeneratorModel;

class FieldRowModel {
    protected int $gameId;
    protected string $playerCode;
    protected array $field;
    protected FieldModel $fieldModel;

    public function __construct(int $gameId, string $playerCode, array $cells = []) {
        $this->gameId     = $gameId;
        $this->playerCode = $playerCode;
        $this->fieldModel = new FieldModel();

        $this->setField($cells);
    }

    protected function setField(array $cells): void {
        foreach ($cells as $item) {
            if ($item['IS_SHIP']) {
                // координаты инвертированы
                $this->field[$item['X_COORD']][$item['Y_COORD']] = [
                    'shipType' => $item['SHIP_TYPE'],
                    'isHit'    => $item['IS_HIT']
                ];
            } else {
                $this->field[$item['X_COORD']][$item['Y_COORD']] = [
                    'shipType' => 'empty',
                    'isHit'    => $item['IS_HIT']
                ];
            }
            $this->field[$item['X_COORD']][$item['Y_COORD']]['id'] = $item['ID'];
        }
    }

    public function getFieldForFront(bool $isEnemy = false): array {
        $field = [];

        if ($isEnemy) {

            for ($x = 0; $x < 10; $x++) {
                for ($y = 0; $y < 10; $y++) {

                    if ($this->field[$x][$y]['isHit']) {
                        $field[$x][$y] = [
                            $this->field[$x][$y]['shipType'],
                            (int)$this->field[$x][$y]['isHit']
                        ];
                    } else {
                        $field[$x][$y] = [
                            'hidden',
                            (int)$this->field[$x][$y]['isHit']
                        ];
                    }
                }
            }

            return $field;
        }

        for ($x = 0; $x < 10; $x++) {
            for ($y = 0; $y < 10; $y++) {
                $field[$x][$y] = [
                    $this->field[$x][$y]['shipType'],
                    (int)$this->field[$x][$y]['isHit']
                ];
            }
        }

        return $field;
    }

    public function getUsedPlaces(): array {
        $usedPlaces = [];

        for ($x = 0; $x < 10; $x++) {
            for ($y = 0; $y < 10; $y++) {
                if ($this->field[$x][$y]['shipType'] != 'empty' && !in_array($this->field[$x][$y]['shipType'], $usedPlaces)) {
                    $usedPlaces[] = $this->field[$x][$y]['shipType'];
                }
            }
        }

        return $usedPlaces;
    }

    protected function placeShip(array $shipInfo): bool {
        // формат описания корабля 4-1, 3-1, 3-2 (первая цифра размер, вторая идентификатор)
        $shipSize = explode('-', $shipInfo['ship'])[0];

        if ($shipInfo['orientation'] === 'vertical') {
            $iter       = $shipInfo['y'];
            $fix        = $shipInfo['x'];
            $offsetSize = (int)$shipInfo['y'] + (int)$shipSize;
        } else {
            $iter       = $shipInfo['x'];
            $fix        = $shipInfo['y'];
            $offsetSize = (int)$shipInfo['x'] + (int)$shipSize;
        }

        while ($iter < $offsetSize) {

            if ($shipInfo['orientation'] === 'vertical') {
                $success = $this->fieldModel->update($this->field[$fix][$iter]['id'], [
                    'IS_SHIP'   => true,
                    'SHIP_TYPE' => $shipInfo['ship']
                ]);
            } else {
                $success = $this->fieldModel->update($this->field[$iter][$fix]['id'], [
                    'IS_SHIP'   => true,
                    'SHIP_TYPE' => $shipInfo['ship']
                ]);
            }

            if (!$success) {
                return false;
            }

            $iter++;
        }

        return true;
    }

    public function deleteShip(array $shipInfo): array {
        for ($x = 0; $x < 10; $x++) {
            for ($y = 0; $y < 10; $y++) {

                if ($this->field[$x][$y]['shipType'] === $shipInfo['ship']) {

                    $success = $this->fieldModel->update($this->field[$x][$y]['id'], [
                        'IS_SHIP'   => false,
                        'SHIP_TYPE' => null
                    ]);

                    if (!$success) {
                        return GeneratorModel::generateError('Db-Error', 'Не удалось удалить корабль');
                    }
                }
            }
        }

        $info['success'] = true;

        return $info;
    }

    public function shipPlacement(array $shipInfo): array {
        if (!isset($shipInfo['ship'])) {
            return GeneratorModel::generateError('Param-Error', 'Не переданы необходимые параметры');
        }

        $validator = new Validator($shipInfo);

        if (!$validator->isOnField()) {
            return GeneratorModel::generateError('Placement-Error', 'Выход за границы поля');
        }

        if (!$validator->isCellNotShip($this->field)) {
            return GeneratorModel::generateError('Placement-Error', 'Корабль пересекается с другим кораблем');
        }

        $info = $this->deleteShip($shipInfo);

        if (!$info['success']) {
            return $info;
        }

        $success = $this->placeShip($shipInfo);

        if (!$success) {
            return GeneratorModel::generateError('Db-Error', 'Не удалось разместить корабль, попробуйте еще раз');
        }

        $info['success'] = true;

        return $info;
    }

    public function clearField(): array {
        $usedShip = $this->getUsedPlaces();

        foreach ($usedShip as $item) {
            $info = $this->deleteShip(['ship' => $item]);

            if (!$info['success']) {
                return $info;
            }
        }

        $info['success'] = true;

        return $info;
    }

    public function packagePlacement(array $shipInfo): array {
        foreach ($shipInfo as $item) {
            $info = $this->shipPlacement($item);

            if (!$info['success']) {
                return $info;
            }
        }

        $info['success'] = true;

        return $info;
    }

    protected function isLastSegment(array $shipInfo, array $coords): bool {
        // формат описания корабля 4-1, 3-1, 3-2 (первая цифра размер, вторая идентификатор)
        $shipSize = explode('-', $this->field[$shipInfo['x']][$shipInfo['y']]['shipType'])[0];

        if ((int)$shipSize === 1) {
            return true;
        }

        $counter = 0;

        foreach ($coords as $item) {

            if ($this->field[$item['x']][$item['y']]['isHit']) {
                $counter++;
            }
        }

        return $counter === (int)$shipSize;
    }

    protected function getShipCoords(array $shipInfo): array {
        $coords = [];

        for ($x = 0; $x < 10; $x++) {
            for ($y = 0; $y < 10; $y++) {

                if ($this->field[$x][$y]['shipType'] === $this->field[$shipInfo['x']][$shipInfo['y']]['shipType']) {
                    $coords[] = [
                        'x' => $x,
                        'y' => $y
                    ];
                }
            }
        }

        return $coords;
    }

    protected function markAround(array $coords): array {
        foreach ($coords as $item) {

            for ($x = $item['x'] - 1; $x < $item['x'] + 2; $x++) {
                for ($y = $item['y'] - 1; $y < $item['y'] + 2; $y++) {

                    if (array_key_exists($x, $this->field) && array_key_exists($y, $this->field[$x])) {

                        if ($this->field[$x][$y]['shipType'] === 'empty' && !$this->field[$x][$y]['isHit']) {
                            $info = $this->takeShot([
                                'x' => $x,
                                'y' => $y
                            ]);

                            if (!$info['success']) {
                                return $info;
                            }
                        }
                    }
                }
            }
        }

        $info['success'] = true;

        return $info;
    }

    public function takeShot(array $shipInfo): array {
        if ($this->field[$shipInfo['x']][$shipInfo['y']]['isHit']) {
            return GeneratorModel::generateError('Game-Error', 'Сюда уже стреляли');
        }

        $success = $this->fieldModel->update($this->field[$shipInfo['x']][$shipInfo['y']]['id'], ['IS_HIT' => true]);

        if (!$success) {
            return GeneratorModel::generateError('Db-Error', 'Не удалось произвести выстрел');
        }

        $this->field[$shipInfo['x']][$shipInfo['y']]['isHit'] = true;

        if ($this->field[$shipInfo['x']][$shipInfo['y']]['shipType'] === 'empty') {
            $info['success'] = true;
            $info['isShip']  = false;

            return $info;
        }

        $coords = $this->getShipCoords($shipInfo);

        if ($this->isLastSegment($shipInfo, $coords)) {
            $info = $this->markAround($coords);

            if (!$info['success']) {
                return $info;
            }
        }

        $info['success'] = true;
        $info['isShip']  = true;

        return $info;
    }

    public function isLastShip(): bool {
        for ($x = 0; $x < 10; $x++) {
            for ($y = 0; $y < 10; $y++) {

                if ($this->field[$x][$y]['shipType'] != 'empty' && !$this->field[$x][$y]['isHit']) {
                    return false;
                }
            }
        }

        return true;
    }
}
