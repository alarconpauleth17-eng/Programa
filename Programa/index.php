<?php

require __DIR__ . '/app/bootstrap.php';

use App\Router;

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$router = new Router();
$router->dispatch($uri, $_SERVER['REQUEST_METHOD']);
