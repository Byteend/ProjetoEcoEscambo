<?php

declare(strict_types=1);

require_once __DIR__ . '/../middleware/require_auth.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../helpers/validation.php';

require_method('POST');

$ofertanteId = current_user_id();
$interesseId = input_int('interesse_id');
$produtoPropostoId = input_int('produto_proposto_id');

if ($interesseId <= 0 || $produtoPropostoId <= 0) {
    error_response('Interesse ou produto proposto invalido.', 422);
}

$pdo = db();

$stmt = $pdo->prepare(
    'SELECT i.id, i.interessado_id, i.ofertante_id, p.estado AS produto_estado
     FROM interesses i
     INNER JOIN produtos p ON p.id = i.produto_id
     WHERE i.id = :interesse_id
     LIMIT 1'
);
$stmt->execute(['interesse_id' => $interesseId]);
$interest = $stmt->fetch();

if (!$interest || (int) $interest['ofertante_id'] !== $ofertanteId) {
    error_response('Interesse nao encontrado para este ofertante.', 404);
}

if ($interest['produto_estado'] !== 'Em aberto') {
    error_response('O produto original nao esta mais aberto para troca.', 409);
}

$productStmt = $pdo->prepare(
    'SELECT id, estado
     FROM produtos
     WHERE id = :produto_id AND utilizador_id = :interessado_id
     LIMIT 1'
);
$productStmt->execute([
    'produto_id' => $produtoPropostoId,
    'interessado_id' => (int) $interest['interessado_id'],
]);
$product = $productStmt->fetch();

if (!$product || $product['estado'] !== 'Em aberto') {
    error_response('Produto proposto nao encontrado ou indisponivel.', 404);
}

try {
    $insert = $pdo->prepare(
        'INSERT INTO propostas_troca (interesse_id, produto_proposto_id, estado)
         VALUES (:interesse_id, :produto_proposto_id, "Pendente")'
    );
    $insert->execute([
        'interesse_id' => $interesseId,
        'produto_proposto_id' => $produtoPropostoId,
    ]);

    success_response('Proposta registrada com sucesso.', [
        'proposta_id' => (int) $pdo->lastInsertId(),
    ], 201);
} catch (PDOException $exception) {
    if ($exception->getCode() === '23000') {
        error_response('Ja existe uma proposta para este produto.', 409);
    }

    error_response('Nao foi possivel registrar a proposta.', 500);
}
