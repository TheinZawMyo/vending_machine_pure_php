<?php

namespace Theinzawmyo\VendingMachine;

/**
 * Simple router: matches URI and method, invokes controller action.
 * Supports attribute-style routes like /products/{id}/purchase.
 */
class Router
{
    /** @var array<int, array{pattern: string, method: string, handler: callable, middleware?: callable[]}> */
    private array $routes = [];

    public function get(string $pattern, callable $handler, array $middleware = []): self
    {
        $this->routes[] = ['pattern' => $pattern, 'method' => 'GET', 'handler' => $handler, 'middleware' => $middleware];
        return $this;
    }

    public function post(string $pattern, callable $handler, array $middleware = []): self
    {
        $this->routes[] = ['pattern' => $pattern, 'method' => 'POST', 'handler' => $handler, 'middleware' => $middleware];
        return $this;
    }

    public function put(string $pattern, callable $handler, array $middleware = []): self
    {
        $this->routes[] = ['pattern' => $pattern, 'method' => 'PUT', 'handler' => $handler, 'middleware' => $middleware];
        return $this;
    }

    public function delete(string $pattern, callable $handler, array $middleware = []): self
    {
        $this->routes[] = ['pattern' => $pattern, 'method' => 'DELETE', 'handler' => $handler, 'middleware' => $middleware];
        return $this;
    }

    /**
     * Match URI against pattern with placeholders {id}, etc.
     * @return array{0: callable, 1: array<string, string>}|null
     */
    public function match(string $method, string $uri): ?array
    {
        $uri = '/' . trim(parse_url($uri, PHP_URL_PATH) ?: '', '/');
        if ($uri !== '/') {
            $uri = rtrim($uri, '/') ?: '/';
        }

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }
            $pattern = $this->patternToRegex($route['pattern']);
            if (preg_match($pattern, $uri, $m)) {
                $params = [];
                foreach ($m as $k => $v) {
                    if (is_string($k)) {
                        $params[$k] = $v;
                    }
                }
                $handler = $route['handler'];
                $middleware = $route['middleware'] ?? [];
                foreach (array_reverse($middleware) as $mw) {
                    $handler = $mw($handler);
                }
                return [$handler, $params];
            }
        }
        return null;
    }

    private function patternToRegex(string $pattern): string
    {
        $pattern = '/' . trim($pattern, '/');
        $pattern = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $pattern);
        return '#^' . $pattern . '$#';
    }
}
