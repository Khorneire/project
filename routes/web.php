<?php

/** @var Router $router */
use Illuminate\Routing\Router;

$router->get('{path}', fn () => view('welcome'))->where('path', '^(?!api).*$');
