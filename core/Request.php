<?php

namespace core;

class Request {
    protected static ?Request $request = null;
    protected array $get = [];
    protected array $post = [];
    protected ?array $body = [];
    protected array $param = [];

    protected function __construct() {
    }

    protected function __clone() {
    }

    public static function getInstance(): static {
        if (is_null(static::$request)) {
            static::$request = new static();
        }

        return static::$request;
    }

    public function get(): array {
        return $this->get;
    }

    public function body(): array {
        return $this->body;
    }

    public function post(): array {
        return $this->post;
    }

    public function param(): array {
        return $this->param;
    }

    public function setGet(array $get): static {
        $this->get = $get;
        return $this;
    }

    public function setPost(array $post): static {
        $this->post = $post;
        return $this;
    }

    public function setBody(?array $body): static {
        $this->body = $body;
        return $this;
    }

    public function setParam(array $param): static {
        $this->param = $param;
        return $this;
    }

    public function getAll(): array {
        return array_merge(static::$request->get, static::$request->post, static::$request->body, static::$request->param);
    }
}
