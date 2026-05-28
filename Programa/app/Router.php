<?php

namespace App;

use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\HomeController;
use App\Controllers\ProductController;

class Router
{
    public function dispatch(string $uri, string $method): void
    {
        $uri = trim($uri, '/');
        $segments = $uri === '' ? [] : array_filter(explode('/', $uri));

        if (str_starts_with($uri, 'assets/')) {
            return;
        }

        $controller = HomeController::class;
        $action = 'index';

        if ($segments === []) {
            $controller = HomeController::class;
            $action = 'index';
        } elseif ($segments[0] === 'login') {
            $controller = AuthController::class;
            $action = $method === 'POST' ? 'login' : 'showLogin';
        } elseif ($segments[0] === 'register') {
            $controller = AuthController::class;
            $action = $method === 'POST' ? 'register' : 'showRegister';
        } elseif ($segments[0] === 'logout') {
            $controller = AuthController::class;
            $action = 'logout';
        } elseif (isset($segments[0], $segments[1]) && $segments[0] === 'admin' && $segments[1] === 'products') {
            $controller = ProductController::class;
            $action = 'manage';
        } elseif (isset($segments[0], $segments[1]) && $segments[0] === 'admin' && $segments[1] === 'import') {
            $controller = ProductController::class;
            $action = 'import';
        } elseif (isset($segments[0], $segments[1]) && $segments[0] === 'admin' && $segments[1] === 'orders') {
            $controller = \App\Controllers\OrderController::class;
            $action = $method === 'POST' ? 'updateStatus' : 'index';
        } elseif ($segments[0] === 'admin') {
            $controller = DashboardController::class;
            $action = 'index';
        } else {
            $controller = HomeController::class;
            $action = 'index';
        }

        $controllerInstance = new $controller();
        $controllerInstance->$action();
    }
}
