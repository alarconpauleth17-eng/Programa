<?php

namespace App;

abstract class Controller
{
    protected function view(string $template, array $data = []): void
    {
        extract($data);
        $layout = __DIR__ . '/Views/' . $template . '.php';
        if (!file_exists($layout)) {
            throw new \RuntimeException('Vista no encontrada: ' . $template);
        }

        include $layout;
    }

    protected function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }

    protected function json(array $payload, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($payload);
        exit;
    }
}
