<?php

declare(strict_types=1);

require_once __DIR__ . '/../middleware/require_auth.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../helpers/validation.php';

$userId = current_user_id();
$onlyWithInterests = input_int('com_interessados', 'get') === 1;

$sql = 'SELECT p.id, p.nome, p.descricao, p.foto_url, p.estado, p.criado_em,
               COUNT(i.id) AS total_interessados
        FROM produtos p
        LEFT JOIN interesses i ON i.produto_id = p.id
        WHERE p.utilizador_id = :user_id';

if ($onlyWithInterests) {
    $sql .= ' AND i.id IS NOT NULL';
}

$sql .= ' GROUP BY p.id
          ORDER BY p.criado_em DESC, p.id DESC';

$stmt = db()->prepare($sql);
$stmt->execute(['user_id' => $userId]);

success_response('Meus produtos encontrados.', [
    'items' => $stmt->fetchAll(),
]);
