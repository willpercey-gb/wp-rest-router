<?php

namespace UWebPro\WordPress\Rest;

use http\Exception\BadMethodCallException;
use UWebPro\Str\SubstringTrait;
use UWebPro\WordPress\Rest\Structure\Routing;

class Router implements Routing
{
    use SubstringTrait;

    private static $instance;

    private $actions;


    private $namespace;

    public function __construct(string $namespace)
    {
        $this->namespace = $namespace;
        self::$instance = $this;
    }

    public function request(string $method, string $route): RouteAction
    {
        $this->actions[] = new RouteAction($method, $route);
        return end($this->actions);
    }


    public function get(string $route): RouteAction
    {
        return $this->request('GET', $route);
    }

    public function post(string $route): RouteAction
    {
        return $this->request('POST', $route);
    }

    public function register(): bool
    {
        add_action('rest_api_init', function () {
            foreach ($this->actions as $action) {
                if (!$action instanceof RouteAction) {
                    throw new BadMethodCallException('Actions may only be registered by ' . RouteAction::class);
                }

                $action = $this->convertVariables($action);

                register_rest_route($this->namespace, $action->route, [
                    'methods' => [$action->method],
                    'callback' => $action->callback,
                ]);
            }
        });
    }

    private function convertVariables($action): RouteAction
    {
        if (mb_stripos($action->route, '{') !== false && mb_stripos($action->route, '}') !== false) {
            $variable = $this->substring($action->route, '{', '}');
            $find = '{' . $variable . '}';
            $replace = '(?P<' . $variable . '>\d+)';
            $action->route = str_replace($find, $replace, $action->route);
        }
        return $action;
    }


}