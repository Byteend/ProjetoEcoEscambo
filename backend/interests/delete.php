<?php

declare(strict_types=1);

require_once __DIR__ . '/../middleware/require_auth.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../helpers/validation.php';

require_method('POST');

$userId = current_user_id();
$interesseId = input_int('interesse_id');

if ($interesseId <= 0) {
    error_response('Interesse invalido.', 422);
}

$stmt = db()->prepare(
    'DELETE FROM interesses
     WHERE id = :interesse_id
       AND (interessado_id = :user_id OR ofertante_id = :user_id)'
);
$stmt->execute([
    'interesse_id' => $interesseId,
    'user_id' => $userId,
]);

if ($stmt->rowCount() === 0) {
    error_response('Interesse nao encontrado para este utilizador.', 404);
}

success_response('Interesse removido com sucesso.');
