<?php

namespace core\View;

use Exception;

class JsonView extends View {
    public function __construct(array $data = [], int $responseStatus = 200) {
        parent::__construct($data, $responseStatus);
        header('Content-type: application/json');
    }

    public function render(): bool {
        try {
            echo json_encode($this->data);

            return true;
        } catch (Exception $e) {
            echo $e;

            return false;
        }
    }
}
