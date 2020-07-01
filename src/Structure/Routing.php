<?php

namespace UWebPro\WordPress\Rest\Structure;

interface Routing
{
    public function __construct(string $namespace);

    public function get(string $route);

    public function post(string $route);
}