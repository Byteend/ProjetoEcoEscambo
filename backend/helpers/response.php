<?php

declare(strict_types=1);

function json_response(array $payload, int $statusCode = 200): never
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function success_response(string $message, array $data = [], int $statusCode = 200): never
{
    json_response([
        'success' => true,
        'message' => $message,
        'data' => $data,
    ], $statusCode);
}

function error_response(string $message, int $statusCode = 400, array $errors = []): never
{
    json_response([
        'success' => false,
        'message' => $message,
        'errors' => $errors,
    ], $statusCode);
}

function require_method(string $method): void
{
    if ($_SERVER['REQUEST_METHOD'] !== strtoupper($method)) {
        error_response('Metodo HTTP nao permitido.', 405);
    }
}
