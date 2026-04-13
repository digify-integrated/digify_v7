<?php
namespace Core;

class Router {
    protected array $routes = [];
    protected string $groupPrefix = '';
    protected array $namedRoutes = [];

    /**
     * Group routes under a common prefix (e.g., /admin)
     */
    public function group(string $prefix, callable $callback): void {
        $previousGroupPrefix = $this->groupPrefix;
        $this->groupPrefix .= $prefix;
        $callback($this);
        $this->groupPrefix = $previousGroupPrefix;
    }

    /**
     * Registers a GET route. Now supports route naming.
     */
    public function get(string $path, array|callable $callback, array $middleware = [], string $name = null): void {
        $fullPath = $this->groupPrefix . $path;
        $this->routes['get'][$fullPath] = ['callback' => $callback, 'middleware' => $middleware];
        
        if ($name) {
            $this->namedRoutes[$name] = $fullPath;
        }
    }

    /**
     * Registers a POST route. Now supports route naming.
     */
    public function post(string $path, array|callable $callback, array $middleware = [], string $name = null): void {
        $fullPath = $this->groupPrefix . $path;
        $this->routes['post'][$fullPath] = ['callback' => $callback, 'middleware' => $middleware];
        
        if ($name) {
            $this->namedRoutes[$name] = $fullPath;
        }
    }

    /**
     * Helper to get a URL by its route name.
     */
    public function url(string $name): string {
        return $this->namedRoutes[$name] ?? '';
    }

    public function resolve(Request $request) {
        $path = $request->getUri();
        $method = $request->getMethod();

        foreach ($this->routes[$method] ?? [] as $route => $action) {
            // Convert {id} to named regex groups
            $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<\1>[a-zA-Z0-9_]+)', $route);
            $pattern = "#^" . $pattern . "$#";

            if (preg_match($pattern, $path, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                
                $callback = $action['callback'];
                $middlewares = $action['middleware'];

                // Recursive function to execute middleware chain
                $next = function($req) use (&$middlewares, &$next, $callback, $params) {
                    if (empty($middlewares)) {
                        if (is_array($callback)) {
                            $controller = new $callback[0]();
                            return call_user_func_array([$controller, $callback[1]], array_merge([$req], $params));
                        }
                        return call_user_func_array($callback, array_merge([$req], $params));
                    }

                    $middlewareClass = array_shift($middlewares);
                    $middlewareInstance = new $middlewareClass();
                    return $middlewareInstance->handle($req, $next);
                };

                return $next($request);
            }
        }

        throw new \Exception("Route not found", 404);
    }
}