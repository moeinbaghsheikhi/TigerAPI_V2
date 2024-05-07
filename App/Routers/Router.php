<?php

namespace App\Routers;

use App\Traits\ResponseTrait;

class Router {
    use ResponseTrait;
    private $routes = [];

    public function get($version, $path, $controller, $method) {
        $path = '/' . $version . $path;
        $this->routes[$version]['GET'][$path] = ['controller' => $controller, 'method' => $method, 'request' => '', "requestMethod" => "get"];
    }

    public function post($version, $path, $controller, $method) {
        $path = '/' . $version . $path;
        $postData = getPostDataInput();
        $this->routes[$version]['POST'][$path] = ['controller' => $controller, 'method' => $method, 'request' => $postData, "requestMethod" => "post"];
    }

    public function put($version, $path, $controller, $method) {
        $path = '/' . $version . $path;
        $postData = getPostDataInput();
        $this->routes[$version]['PUT'][$path] = ['controller' => $controller, 'method' => $method, 'request' => $postData, "requestMethod" => "put"];
    }

    public function delete($version, $path, $controller, $method) {
        $path = '/' . $version . $path;
        $this->routes[$version]['DELETE'][$path] = ['controller' => $controller, 'method' => $method, 'request' => '', "requestMethod" => "delete"];
    }

    public function resolve($version, $requestMethod, $path) {
        $path = '/' . $version . '/' . $path;
        $matchedRoute = null;

        // Match routes with variable patterns
        foreach ($this->routes[$version][$requestMethod] as $routePath => $route) {
            if ($this->isVariablePattern($routePath)) {
                $pattern = $this->getPatternFromRoute($routePath);
                if (preg_match($pattern, $path, $matches)) {
                    $matchedRoute = $route;
                    break;
                }
            } elseif ($routePath === $path) {
                $matchedRoute = $route;
                break;
            }
        }

        if ($matchedRoute) {
            $controller = $matchedRoute['controller'];
            $method = $matchedRoute['method'];
            $requestMethod = $matchedRoute['requestMethod'];
            $request = $matchedRoute['request'];

            $controllerInstance = new $controller();
            if (isset($matches) && $requestMethod != "put") {
                $controllerInstance->$method($matches["id"]);
            } else {
                if($requestMethod == 'post') $controllerInstance->$method($request);
                else if($requestMethod == 'put' && isset($matches)) $controllerInstance->$method($matches["id"], $request);
                else $controllerInstance->$method();
            }
            exit();
        } else {
            return $this->sendResponse(null, "Not Found", true, HTTP_NotFOUND);
        }
    }

    private function isVariablePattern($path) {
        return strpos($path, '{') !== false && strpos($path, '}') !== false;
    }

    private function getPatternFromRoute($routePath) {
        $pattern = preg_replace('/\{([^\/]+)\}/', '(?<$1>[^\/]+)', $routePath);
        return '#^' . $pattern . '$#';
    }
}
