<?php

declare(strict_types=1);

function input_string(string $key, string $source = 'post'): string
{
    $data = $source === 'get' ? $_GET : $_POST;
    return trim((string) ($data[$key] ?? ''));
}

function input_int(string $key, string $source = 'post', int $default = 0): int
{
    $data = $source === 'get' ? $_GET : $_POST;
    return filter_var($data[$key] ?? $default, FILTER_VALIDATE_INT) ?: $default;
}

function is_strong_password(string $password): bool
{
    return (bool) preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{6,}$/', $password);
}

function validate_registration(string $nome, string $email, string $senha, string $confirmacaoSenha): array
{
    $errors = [];

    if ($nome === '') {
        $errors['nome'] = 'Informe o nome.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Informe um e-mail valido.';
    }

    if (!is_strong_password($senha)) {
        $errors['senha'] = 'A senha deve ter no minimo 6 caracteres, com letra maiuscula, minuscula e numero.';
    }

    if ($senha !== $confirmacaoSenha) {
        $errors['confirmacao_senha'] = 'A confirmacao de senha deve ser identica.';
    }

    return $errors;
}

function clamp_per_page(int $perPage): int
{
    if ($perPage < 1) {
        return 10;
    }

    return min($perPage, 50);
}
