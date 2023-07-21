<?php

namespace core\Db;

use Exception;

abstract class QueryBuilder {
    protected string $table = '';
    protected array $where = [];
    protected array $select = [];
    protected array $order = [];
    protected ?int $limit = null;
    protected ?int $offset = null;

    public function limit(int $limit): static {
        $this->limit = $limit;

        return $this;
    }

    public function offset(int $offset): static {
        $this->offset = $offset;

        return $this;
    }

    public function where(array $where, bool $clear = false): static {
        if ($clear) {
            $this->where = [];
        }

        $this->where = array_merge($this->where, $where);

        return $this;
    }

    public function select(array $select, bool $clear = false): static {
        if ($clear) {
            $this->select = [];
        }

        $this->select = array_merge($this->select, $select);

        return $this;
    }

    public function order(array $order, bool $clear = false): static {
        if ($clear) {
            $this->order = [];
        }

        $this->order = array_merge($this->order, $order);

        return $this;
    }

    public function update(int $id, array $data): bool {
        $update = [];

        foreach ($data as $key => $value) {
            $update[] = $key . ' = \'' . $value . '\'';
        }

        $sql   = [];
        $sql[] = 'UPDATE';
        $sql[] = $this->table;
        $sql[] = 'SET';
        $sql[] = implode(', ', $update);
        $sql[] = 'WHERE ID=\'' . $id . '\'';

        try {
            DbConnect::getPdo()
                ->exec(implode(' ', $sql));
            return true;
        } catch (Exception) {
            return false;
        }
    }

    public function delete(int $id): bool {
        $sql   = [];
        $sql[] = 'DELETE FROM';
        $sql[] = $this->table;
        $sql[] = 'WHERE ID=\'' . $id . '\'';

        try {
            DbConnect::getPdo()
                ->exec(implode(' ', $sql));
            return true;
        } catch (Exception) {
            return false;
        }
    }

    public function add(array $data): ?int {
        $sql   = [];
        $sql[] = 'INSERT INTO';
        $sql[] = $this->table;
        $sql[] = '(' . implode(', ', array_keys($data)) . ')';
        $sql[] = 'VALUES';
        $sql[] = '(\'' . implode('\', \'', $data) . '\')';

        try {
            DbConnect::getPdo()
                ->exec(implode(' ', $sql));
            return DbConnect::getPdo()
                ->lastInsertId();
        } catch (Exception) {
            return null;
        }
    }

    protected function buildSelect(): string {
        $sql   = [];
        $sql[] = 'SELECT';
        $sql[] = implode(', ', $this->select);
        $sql[] = 'FROM ' . $this->table;

        return implode(' ', $sql);
    }

    protected function buildWhere(): string {
        if (empty($this->where)) {
            return '';
        }

        $filter = [];

        foreach ($this->where as $key => $value) {
            if (is_array($value)) {
                $filterOr = [];

                if (isset($value['BETWEEN'])) {
                    $filter[] = $key . ' BETWEEN ' . implode(' AND ', $value['BETWEEN']);
                } else {
                    foreach ($value as $itemValue) {
                        $filterOr[] = $key . ' = \'' . $itemValue . '\'';
                    }

                    $filter[] = '(' . implode(' OR ', $filterOr) . ') ';
                }
            } else {
                $filter[] = $key . ' = \'' . $value . '\'';
            }
        }

        $sql   = [];
        $sql[] = 'WHERE';
        $sql[] = implode(' AND ', $filter);

        return implode(' ', $sql);
    }

    protected function buildOrder(): string {
        if (empty($this->order)) {
            return '';
        }

        $order = [];

        foreach ($this->order as $key => $value) {
            $order [] = $key . ' ' . $value;
        }

        $sql   = [];
        $sql[] = 'ORDER BY';
        $sql[] = implode(', ', $order);

        return implode(' ', $sql);
    }

    protected function buildLimitOffset(): string {
        if ($this->limit === null) {
            return '';
        }

        $sql   = [];
        $sql[] = 'LIMIT';
        $sql[] = $this->limit;
        $sql[] = 'OFFSET';
        $sql[] = $this->offset;

        return implode(' ', $sql);
    }

    public function fetchAll(): ?array {
        $query = DbConnect::getPdo()
            ->prepare(implode(' ', [
                $this->buildSelect(),
                $this->buildWhere(),
                $this->buildOrder(),
                $this->buildLimitOffset()
            ]));

        $query->execute();

        $data = $query->fetchAll();

        if (!$data) {
            return null;
        }

        return $data;
    }

    public function fetch(): ?array {
        $query = DbConnect::getPdo()
            ->prepare(implode(' ', [
                $this->buildSelect(),
                $this->buildWhere(),
                $this->buildOrder(),
                $this->buildLimitOffset()
            ]));

        $query->execute();

        $data = $query->fetch();

        if (!$data) {
            return null;
        }

        return $data;
    }

    public function getAmountRecord(): int {
        $dbData = (new static())->select(['COUNT(*)'])
            ->fetch();

        return $dbData[0];
    }
}
