<?php

namespace site\Helper;

class GeneratorModel {
    protected static string $chars = 'qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM1234567890';

    public static function generatePlayerCode(int $length = 10): string {
        $amountChar = strlen(static::$chars);
        $string     = '';

        for ($i = 0; $i < $length; $i++) {
            $string .= substr(static::$chars, mt_rand(1, $amountChar) - 1, 1);
        }

        return $string;
    }

    public static function generatePlayerTurn(string $firstPlayer, string $secondPlayer): string {
        if (mt_rand(0, 1)) {
            return $secondPlayer;
        }

        return $firstPlayer;
    }

    public static function generateError(string $error, string $message): array {
        $info['success'] = false;
        $info['error']   = $error;
        $info['message'] = $message;

        return $info;
    }
}
