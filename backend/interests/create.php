<?php

declare(strict_types=1);

require_once __DIR__ . '/../middleware/require_auth.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../helpers/validation.php';

require_method('POST');

$interessadoId = current_user_id();
$produtoId = input_int('produto_id');

if ($produtoId <= 0) {
    error_response('Produto invalido.', 422);
}

$pdo = db();

$stmt = $pdo->prepare(
    'SELECT id, utilizador_id, estado
     FROM produtos
     WHERE id = :produto_id
     LIMIT 1'
);
$stmt->execute(['produto_id' => $produtoId]);
$produto = $stmt->fetch();

if (!$produto || $produto['estado'] !== 'Em aberto') {
    error_response('Produto nao encontrado ou indisponivel para troca.', 404);
}

if ((int) $produto['utilizador_id'] === $interessadoId) {
    error_response('Voce nao pode manifestar interesse no seu proprio produto.', 422);
}

try {
    $stmt = $pdo->prepare(
        'INSERT INTO interesses (interessado_id, ofertante_id, produto_id)
         VALUES (:interessado_id, :ofertante_id, :produto_id)'
    );
    $stmt->execute([
        'interessado_id' => $interessadoId,
        'ofertante_id' => (int) $produto['utilizador_id'],
        'produto_id' => $produtoId,
    ]);

    success_response('Interesse registrado com sucesso.', [
        'interesse_id' => (int) $pdo->lastInsertId(),
    ], 201);
} catch (PDOException $exception) {
    if ($exception->getCode() === '23000') {
        error_response('Voce ja manifestou interesse neste produto.', 409);
    }

    error_response('Nao foi possivel registrar o interesse.', 500);
}
