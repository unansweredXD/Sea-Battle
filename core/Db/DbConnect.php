<?php

namespace core\Db;

use PDO;

class DbConnect {
    protected static ?PDO $pdo = null;

    private function __construct() {
    }

    private function __clone() {
    }

    public static function getPdo(): PDO {
        if (is_null(self::$pdo)) {
            $dbConfig = parse_ini_file('db-config.ini');

            $dsn       = sprintf("mysql:host=%s;dbname=%s;charset=%s;", $dbConfig['host'], $dbConfig['db'], $dbConfig['charset']);
            self::$pdo = new PDO($dsn, $dbConfig['user'], $dbConfig['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);
        }

        return self::$pdo;
    }
}
