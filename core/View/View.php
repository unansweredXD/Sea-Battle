<?php

namespace core\View;

abstract class View {
    protected array $data = [];

    public function __construct(array $data = [], int $responseStatus = 200) {
        $this->data = $data;

        http_response_code($responseStatus);
    }

    abstract public function render(): bool;
}
