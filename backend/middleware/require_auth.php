<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../helpers/response.php';

start_app_session();

if (empty($_SESSION['user_id'])) {
    error_response('Voce precisa estar autenticado para acessar este recurso.', 401);
}
