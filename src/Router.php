<?php

namespace UWebPro\WordPress\Rest;

use UWebPro\Str\Substring;
use UWebPro\WordPress\Rest\Structure\Routing;

class Router implements Routing
{
    use Substring;

    private static $instance;

    private $actions;


    private $namespace;

    public function __construct(string $namespace)
    {
        $this->namespace = $namespace;
        self::$instance = $this;
    }

    public function request(string $method, string $route, \Closure $callback = null): ?RouteAction
    {
        if ($callback) {
            $action = new RouteAction($method, $route);
            $action->uses($callback);
            $this->actions[] = $action;
            return null;
        }
        $action = new RouteAction($method, $route);
        $this->actions[] = $action;
        return $action;
    }


    public function get(string $route, \Closure $callback = null): RouteAction
    {
        return $this->request('GET', $route, $callback);
    }

    public function post(string $route, \Closure $callback = null): RouteAction
    {
        return $this->request('POST', $route, $callback);
    }

    public function register(): void
    {
        add_action('rest_api_init', function () {
            foreach ($this->actions as $action) {
                if (!$action instanceof RouteAction) {
                    throw new \BadMethodCallException('Actions may only be registered by ' . RouteAction::class);
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
        while (mb_stripos($action->route, '{') !== false && mb_stripos($action->route, '}') !== false) {
            $variable = $this->substring($action->route, '{', '}');
            $find = '{' . $variable . '}';
            $replace = '(?P<' . $variable . '>[^/]+)';
            $action->route = str_replace($find, $replace, $action->route);
        }
        return $action;
    }


}