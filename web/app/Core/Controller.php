<?php

namespace App\Core;

/**
 * Controller - Classe abstraite de base pour tous les contrôleurs.
 */
abstract class Controller
{
    protected function render(string $view, array $data = [], string $layout = 'public'): void
    {
        extract($data);

        $viewFile = __DIR__ . '/../../views/' . $view . '.php';
        if (!file_exists($viewFile)) {
            throw new \RuntimeException("Vue introuvable : {$view}");
        }

        ob_start();
        require $viewFile;
        $content = ob_get_clean();
        $content = preg_replace(
    '/(<form\b[^>]*\bmethod=["\']post["\'][^>]*>)/i',
    '${1}' . Csrf::field(),
    $content
);

        $layoutFile = __DIR__ . '/../../views/layouts/' . $layout . '.php';
        require $layoutFile;
    }

    protected function redirect(string $path): void
    {
        header('Location: ' . APP_URL . $path);
        exit;
    }

    protected function setFlash(string $type, string $message): void
    {
        $_SESSION['flash'][$type] = $message;
    }

    protected function getFlash(): array
    {
        $flash = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);
        return $flash;
    }

    protected function input(string $key, $default = null)
    {
        return isset($_POST[$key]) ? trim((string) $_POST[$key]) : $default;
    }

    protected function requirePost(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/');
        }
    }

    protected function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
