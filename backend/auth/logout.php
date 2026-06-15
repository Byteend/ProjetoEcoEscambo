<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../helpers/response.php';

logout_user();

success_response('Logout realizado com sucesso.');
