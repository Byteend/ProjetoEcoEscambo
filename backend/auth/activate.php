<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../helpers/validation.php';

$userId = input_int('id', 'get');

if ($userId <= 0) {
    error_response('Link de ativacao invalido.', 422);
}

$stmt = db()->prepare('UPDATE utilizadores SET ativo = 1 WHERE id = :id');
$stmt->execute(['id' => $userId]);

if ($stmt->rowCount() === 0) {
    error_response('Utilizador nao encontrado ou ja ativado.', 404);
}

success_response('Conta ativada com sucesso. Voce ja pode fazer login.');
