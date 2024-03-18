<?php

namespace App\core;

class RouteDefinition
{
    public string $method;
    public string $path;
    public string $name;
    public mixed $callback;
}

class Route
{
    private static array $routes = [];
    private static RouteDefinition $routeDefinition;

    private static function routeRegister(string $method, string $path, callable | array $callback): void
    {
        self::$routeDefinition = new RouteDefinition();
        self::$routeDefinition->path = $path;
        self::$routeDefinition->method = $method;
        self::$routeDefinition->callback = $callback;
        
        self::$routes[] = self::$routeDefinition;
    }

    private static function routeParameter(string $routePath, string $requestUrl): array | bool
    {
        $routePathParts = explode('/', ltrim($routePath, '/'));
        $requestUrlParts = explode('/', trim($requestUrl, '/'));

        if (count($routePathParts) !== count($requestUrlParts)) {
            return false;
        }

        $params = [];

        foreach ($routePathParts as $key => $part) {
            if (strpos($part, '{') !== false) {
                $varname = trim($part, '{}');
                $params[$varname] = $requestUrlParts[$key];
            } elseif ($part !== $requestUrlParts[$key]) {
                return false;
            }
        }

        return $params;
    }

    public static function view(string $url, string $name, array $values = []): Route
    {
        self::routeRegister('get', $url, ['view' => $name, 'values' => $values]);
        return new static;
    }

    public static function get(string $url, callable | array $callback): Route
    {
        self::routeRegister('get', $url, $callback);
        return new static;
    }

    public static function post(string $url, callable | array $callback): void
    {
        self::routeRegister('post', $url, $callback);
    }

    public static function name(string $name): void
    {
        self::$routeDefinition->name = $name;
    }

    public static function fallback(string $filename): void
    {
        self::$routes[404] = $filename;
    }

    public function getfallback(int $number): string
    {
        return self::$routes[$number] ?? false;
    }

    public function getRouteName(string $name): string
    {
        foreach (self::$routes as $route) {
            if ($route->name ?? '' === $name) {
                return $_ENV['BASE_URL'] . $route->path;
            }
        }
    }

    public function getCallback(string $method, string $requestUrl): mixed
    {
        foreach (self::$routes as $route) {
            if ($route->path === $requestUrl && $route->method === $method) {
                return $route->callback;
            }

            if ($this->routeParameter($route->path, $requestUrl) && $route->method === $method) {
                return [$route->callback, $this->routeParameter($route->path, $requestUrl)];
            }
        }

        return false;
    }
}
