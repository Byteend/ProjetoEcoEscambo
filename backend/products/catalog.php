<?php

declare(strict_types=1);

require_once __DIR__ . '/../middleware/require_auth.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../helpers/validation.php';

$userId = current_user_id();
$page = max(1, input_int('page', 'get', 1));
$perPage = clamp_per_page(input_int('per_page', 'get', 10));
$offset = ($page - 1) * $perPage;

$pdo = db();

$countStmt = $pdo->prepare(
    'SELECT COUNT(*)
     FROM produtos
     WHERE estado = "Em aberto" AND utilizador_id <> :user_id'
);
$countStmt->execute(['user_id' => $userId]);
$total = (int) $countStmt->fetchColumn();

$stmt = $pdo->prepare(
    'SELECT p.id, p.nome, p.descricao, p.foto_url, p.estado, p.criado_em,
            u.id AS ofertante_id, u.nome AS ofertante_nome
     FROM produtos p
     INNER JOIN utilizadores u ON u.id = p.utilizador_id
     WHERE p.estado = "Em aberto" AND p.utilizador_id <> :user_id
     ORDER BY p.criado_em DESC, p.id DESC
     LIMIT :limit OFFSET :offset'
);
$stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

success_response('Produtos encontrados.', [
    'items' => $stmt->fetchAll(),
    'pagination' => [
        'page' => $page,
        'per_page' => $perPage,
        'total' => $total,
        'total_pages' => (int) ceil($total / $perPage),
    ],
]);
