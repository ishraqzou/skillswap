<?php

namespace App\Core;

class Csrf
{
    public static function token(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function field(): string
    {
        return '<input type="hidden" name="_csrf" value="' . self::token() . '">';
    }

    public static function check(): bool
    {
        $envoye = $_POST['_csrf'] ?? '';
        return is_string($envoye) && $envoye !== '' && hash_equals(self::token(), $envoye);
    }
}