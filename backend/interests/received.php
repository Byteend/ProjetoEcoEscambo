<?php

declare(strict_types=1);

require_once __DIR__ . '/../middleware/require_auth.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../helpers/validation.php';

$ofertanteId = current_user_id();
$produtoId = input_int('produto_id', 'get');

if ($produtoId <= 0) {
    error_response('Produto invalido.', 422);
}

$pdo = db();

$ownerStmt = $pdo->prepare('SELECT id FROM produtos WHERE id = :produto_id AND utilizador_id = :ofertante_id LIMIT 1');
$ownerStmt->execute([
    'produto_id' => $produtoId,
    'ofertante_id' => $ofertanteId,
]);

if (!$ownerStmt->fetch()) {
    error_response('Produto nao encontrado para este ofertante.', 404);
}

$stmt = $pdo->prepare(
    'SELECT i.id AS interesse_id, i.criado_em,
            u.id AS interessado_id, u.nome AS interessado_nome, u.email AS interessado_email
     FROM interesses i
     INNER JOIN utilizadores u ON u.id = i.interessado_id
     WHERE i.ofertante_id = :ofertante_id AND i.produto_id = :produto_id
     ORDER BY i.criado_em DESC, i.id DESC'
);
$stmt->execute([
    'ofertante_id' => $ofertanteId,
    'produto_id' => $produtoId,
]);

success_response('Interesses recebidos encontrados.', [
    'items' => $stmt->fetchAll(),
]);
