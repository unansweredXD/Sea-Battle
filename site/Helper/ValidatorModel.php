<?php

namespace site\Helper;

use site\Model\GameRowModel;

class Validator {
    protected array $post;

    public function __construct(array $post = []) {
        $this->post = $post;
    }

    public function isOnField(): bool {
        // формат описания корабля 4-1, 3-1, 3-2 (первая цифра размер, вторая идентификатор)
        $shipSize = explode('-', $this->post['ship'])[0];

        if ($this->post['orientation'] === 'vertical') {
            $yPermission = (int)$shipSize + $this->post['y'] - 1;

            return $yPermission < 10;
        } else {
            $xPermission = (int)$shipSize + $this->post['x'] - 1;

            return $xPermission < 10;
        }
    }

    public function isCellNotShip(array $field): bool {
        // формат описания корабля 4-1, 3-1, 3-2 (первая цифра размер, вторая идентификатор)
        $shipSize = explode('-', $this->post['ship'])[0];

        if ($this->post['orientation'] === 'vertical') {
            $xSize = (int)$this->post['x'] + 2;
            $ySize = (int)$this->post['y'] + (int)$shipSize + 1;
        } else {
            $xSize = (int)$this->post['x'] + (int)$shipSize + 1;
            $ySize = (int)$this->post['y'] + 2;
        }

        for ($x = $this->post['x'] - 1; $x < $xSize; $x++) {
            for ($y = $this->post['y'] - 1; $y < $ySize; $y++) {

                if (array_key_exists($x, $field) && array_key_exists($y, $field[$x])) {

                    if ($field[$x][$y]['shipType'] === 'empty' || $field[$x][$y]['shipType'] === $this->post['ship']) {
                        continue;
                    } else {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    public function validateGame(?GameRowModel $game, string $playerCode): array {
        if (!$game) {
            return GeneratorModel::generateError('Db-Error', 'Невозможно получить информацию об игре!');
        }

        return $game->playerExist($playerCode);
    }
}
