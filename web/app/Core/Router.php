<?php

namespace App\Core;

/**
 * Router - Contrôleur frontal minimaliste (front controller).
 */
class Router
{
    private array $routes = [];

    public function get(string $path, string $controller, string $action): void
    {
        $this->addRoute('GET', $path, $controller, $action);
    }

    public function post(string $path, string $controller, string $action): void
    {
        $this->addRoute('POST', $path, $controller, $action);
    }

    private function addRoute(string $method, string $path, string $controller, string $action): void
    {
        $pattern = preg_replace('#\{([a-zA-Z_]+)\}#', '(?P<$1>[^/]+)', $path);
        $pattern = '#^' . $pattern . '$#';

        $this->routes[] = [
            'method'     => $method,
            'pattern'    => $pattern,
            'controller' => $controller,
            'action'     => $action,
        ];
    }

    public function dispatch(string $requestUri, string $requestMethod): void
    {
        $path = parse_url($requestUri, PHP_URL_PATH);
        $basePath = parse_url(APP_URL, PHP_URL_PATH) ?: '';
        if ($basePath !== '' && str_starts_with($path, $basePath)) {
            $path = substr($path, strlen($basePath));
        }
        $path = '/' . ltrim($path, '/');
        $path = rtrim($path, '/');
        if ($path === '') {
            $path = '/';
        }

        foreach ($this->routes as $route) {
            if ($route['method'] !== $requestMethod) {
                continue;
            }
            if (preg_match($route['pattern'], $path, $matches)) {
                // Les paramètres d'URL sont transmis de façon POSITIONNELLE.
                // En PHP 8, un tableau à clés textuelles passé à call_user_func_array
                // est interprété comme des arguments nommés : le nom du segment de
                // route ({id}) devrait alors correspondre exactement au nom du
                // paramètre de la méthode ($idDemande, $idSeance...), ce qui n'est
                // pas le cas ici. array_values() rétablit un passage positionnel.
                $named = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                $params = array_values($named);
if ($requestMethod === 'POST' && !Csrf::check()) {
    $this->abort(403, "Formulaire invalide ou session expirée. Rechargez la page et réessayez.");
}
                $controllerClass = 'App\\Controllers\\' . $route['controller'];
                if (!class_exists($controllerClass)) {
                    $this->abort(500, "Contrôleur introuvable : {$route['controller']}");
                }

                $controller = new $controllerClass();
                $action = $route['action'];

                if (!method_exists($controller, $action)) {
                    $this->abort(500, "Action introuvable : {$route['action']}");
                }

                call_user_func_array([$controller, $action], $params);
                return;
            }
        }

        $this->abort(404, "Page introuvable.");
    }

    private function abort(int $code, string $message): void
    {
        http_response_code($code);
        $title = $code === 404 ? '404 — Page introuvable' : 'Erreur';
        require __DIR__ . '/../../views/errors/generic.php';
        exit;
    }
}
