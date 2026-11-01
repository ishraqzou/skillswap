<?php
/**
 * SkillSwap - Configuration principale
 * Tâche 18 - Application Web (PHP 8, MVC, OOP, PDO, Bootstrap 5)
 */
define('APP_ENV', 'development');
define('APP_NAME', 'SkillSwap');
 define('APP_URL', 'http://localhost/skillswapweb/public');

define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'skillswap');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

define('SESSION_LIFETIME', 7200);
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCKOUT_DURATION_MIN', 15);
define('PASSWORD_MIN_LENGTH', 8);

define('POINTS_PER_SESSION', 10);
define('EXCHANGE_REQUEST_EXPIRY_DAYS', 7);

if (APP_ENV === 'development') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

date_default_timezone_set('Africa/Tunis');
ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
session_set_cookie_params([
    'lifetime' => SESSION_LIFETIME,
    'path' => '/',
    'httponly' => true,
    'samesite' => 'Lax',
]);
