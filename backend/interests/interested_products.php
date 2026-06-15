<?php

declare(strict_types=1);

require_once __DIR__ . '/../middleware/require_auth.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../helpers/validation.php';

$ofertanteId = current_user_id();
$interesseId = input_int('interesse_id', 'get');

if ($interesseId <= 0) {
    error_response('Interesse invalido.', 422);
}

$pdo = db();

$interestStmt = $pdo->prepare(
    'SELECT interessado_id
     FROM interesses
     WHERE id = :interesse_id AND ofertante_id = :ofertante_id
     LIMIT 1'
);
$interestStmt->execute([
    'interesse_id' => $interesseId,
    'ofertante_id' => $ofertanteId,
]);
$interest = $interestStmt->fetch();

if (!$interest) {
    error_response('Interesse nao encontrado para este ofertante.', 404);
}

$stmt = $pdo->prepare(
    'SELECT id, nome, descricao, foto_url, estado, criado_em
     FROM produtos
     WHERE utilizador_id = :interessado_id AND estado = "Em aberto"
     ORDER BY criado_em DESC, id DESC'
);
$stmt->execute(['interessado_id' => (int) $interest['interessado_id']]);

success_response('Produtos do interessado encontrados.', [
    'items' => $stmt->fetchAll(),
]);
