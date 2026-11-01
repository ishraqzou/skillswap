<?php

namespace App\Core;

use App\Models\Compte;

/**
 * Auth - Gestion de l'authentification et de la session.
 */
class Auth
{
    public static function login(array $compte): void
    {
        session_regenerate_id(true);
        $_SESSION['auth'] = [
            'idCompte'   => $compte['idCompte'],
            'email'      => $compte['email'],
            'typeCompte' => $compte['typeCompte'],
            'loginTime'  => time(),
        ];
    }

    public static function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path']);
        }
        session_destroy();
    }

    public static function check(): bool
    {
        return isset($_SESSION['auth']);
    }

    public static function id()
    {
        return $_SESSION['auth']['idCompte'] ?? null;
    }

    public static function type(): ?string
    {
        return $_SESSION['auth']['typeCompte'] ?? null;
    }

    public static function isAdmin(): bool
    {
        return self::check() && self::type() === 'ADMIN';
    }

    public static function isUser(): bool
    {
        return self::check() && self::type() === 'UTILISATEUR';
    }

    public static function user(): ?array
    {
        if (!self::check()) {
            return null;
        }
        static $cache = null;
        if ($cache !== null) {
            return $cache;
        }
        $compteModel = new Compte();
        $cache = $compteModel->findWithProfile(self::id());
        return $cache;
    }

    public static function requireLogin(): void
    {
        if (!self::check()) {
            header('Location: ' . APP_URL . '/connexion');
            exit;
        }
    }

    public static function requireAdmin(): void
    {
        self::requireLogin();
        if (!self::isAdmin()) {
            http_response_code(403);
            echo 'Accès réservé aux administrateurs.';
            exit;
        }
    }

    public static function checkTimeout(): void
    {
        if (self::check()) {
            $elapsed = time() - ($_SESSION['auth']['loginTime'] ?? 0);
            if ($elapsed > SESSION_LIFETIME) {
                self::logout();
            } else {
                $_SESSION['auth']['loginTime'] = time();
            }
        }
    }
}
