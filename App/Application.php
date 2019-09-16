<?php

namespace App;

class Application
{
    private $routes = [];

    public function __construct($routes = [])
    {
        $this->routes = $routes;
    }

    public function addRoute($method, $uri, $function)
    {
        $this->routes[] = [$method, $uri, $function];
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

        //var_dump($serverMethod, $serverUri);
        foreach ($this->routes as $route) {
            list($routeMethod, $routeUri, $function) =  $route;

            $routeUri = preg_quote($routeUri, '/');

            //var_dump($routeUri, $serverUri, preg_match('/^$routeUri$/i', $serverUri));

            if ($serverMethod == $routeMethod && preg_match("/^$routeUri$/i", $serverUri)) {

                $rawData =  file_get_contents('php://input');
                $function();
                return;
            }
        }
    }
}