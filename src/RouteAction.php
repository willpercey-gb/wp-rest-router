<?php


namespace UWebPro\WordPress\Rest;


class RouteAction
{
    public $method;

    public $route;

    public $callback;

    public function __construct(string $method, string $route)
    {
        $this->method = $method;
        $this->route = $route;
    }

    public function uses($callback)
    {
        $this->callback = $callback;
    }
}