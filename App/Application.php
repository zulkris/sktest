<?php

namespace App;

class Application
{
    private $routes = [];

    public function __construct($routes = [])
    {
        foreach ($routes as $route) {
            list($method, $uri, $function) = $route;
            $this->addRoute($method, $uri, $function);
        }
    }

    public function addRoute($method, $uri, $function)
    {
        $this->routes[] = [
            $method,
            preg_replace('|{(.*?)}|i', '(?P<$1>.+)', $uri),
            $function
        ];
    }

    public function get($uri, $function)
    {
        $this->addRoute('GET', $uri, $function);
    }

    public function put($uri, $function)
    {
        $this->addRoute('PUT', $uri, $function);
    }

    public function post($uri, $function)
    {
        $this->addRoute('POST', $uri, $function);
    }

    public function run()
    {
        $serverMethod = $_SERVER['REQUEST_METHOD'];
        $serverUri = $_SERVER['REQUEST_URI'];

        foreach ($this->routes as $route) {
            list($routeMethod, $routeUri, $function) = $route;

            $routeUri = str_replace('/', '\/', $routeUri);

            $matches = [];
            if ($serverMethod == $routeMethod && preg_match("/^$routeUri$/i", $serverUri, $matches)) {
                $stringMathches = array_filter($matches, function ($key) {
                    return !is_numeric($key);
                }, ARRAY_FILTER_USE_KEY);
                $rawData = file_get_contents('php://input');
                $function($rawData, $stringMathches);
                exit();
            }
        }

        header('HTTP/1.0 404 Not Found');
        return;
    }
}