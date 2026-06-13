<?php
namespace App\Core;

class Router {
    private $routes = [];

    public function add($method, $path, $callback) {
        $path = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<\1>[a-zA-Z0-9_]+)', $path);
        $this->routes[] = [
            'method' => $method,
            'path' => '#^' . $path . '$#',
            'callback' => $callback
        ];
    }

    public function run() {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $scriptName = dirname($_SERVER['SCRIPT_NAME']);
        if ($scriptName !== '/' && $scriptName !== '\\') {
            $uri = str_replace($scriptName, '', $uri);
        }
        if ($uri === '') {
            $uri = '/';
        }

        $method = $_SERVER['REQUEST_METHOD'];

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && preg_match($route['path'], $uri, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                
                if (is_array($route['callback'])) {
                    $controllerName = "App\\Controllers\\" . $route['callback'][0];
                    $action = $route['callback'][1];
                    $controller = new $controllerName();
                    call_user_func_array([$controller, $action], $params);
                } else {
                    call_user_func_array($route['callback'], $params);
                }
                return;
            }
        }

        http_response_code(404);
        echo "404 - Página não encontrada";
    }
}
