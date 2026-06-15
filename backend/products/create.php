<?php

declare(strict_types=1);

require_once __DIR__ . '/../middleware/require_auth.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../helpers/validation.php';

require_method('POST');

$userId = current_user_id();
$nome = input_string('nome') ?: input_string('titulo');
$descricao = input_string('descricao') ?: input_string('descricaoP');
$fotoUrl = input_string('foto_url');

if ($nome === '' || $descricao === '') {
    error_response('Informe nome e descricao do produto.', 422);
}

$stmt = db()->prepare(
    'INSERT INTO produtos (utilizador_id, nome, descricao, foto_url, estado)
     VALUES (:utilizador_id, :nome, :descricao, :foto_url, "Em aberto")'
);
$stmt->execute([
    'utilizador_id' => $userId,
    'nome' => $nome,
    'descricao' => $descricao,
    'foto_url' => $fotoUrl !== '' ? $fotoUrl : null,
]);

success_response('Produto cadastrado com sucesso.', [
    'produto_id' => (int) db()->lastInsertId(),
], 201);
