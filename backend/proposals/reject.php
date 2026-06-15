<?php

declare(strict_types=1);

require_once __DIR__ . '/../middleware/require_auth.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../helpers/validation.php';

require_method('POST');

$interessadoId = current_user_id();
$propostaId = input_int('proposta_id');

if ($propostaId <= 0) {
    error_response('Proposta invalida.', 422);
}

$stmt = db()->prepare(
    'DELETE pt
     FROM propostas_troca pt
     INNER JOIN interesses i ON i.id = pt.interesse_id
     WHERE pt.id = :proposta_id AND i.interessado_id = :interessado_id'
);
$stmt->execute([
    'proposta_id' => $propostaId,
    'interessado_id' => $interessadoId,
]);

if ($stmt->rowCount() === 0) {
    error_response('Proposta nao encontrada para este utilizador.', 404);
}

success_response('Proposta recusada e removida com sucesso.');
