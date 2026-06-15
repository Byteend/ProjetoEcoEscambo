<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/app.php';

start_app_session();

if (empty($_SESSION['user_id'])) {
    header('Location: ' . APP_BASE_PATH . '/paginas/login/login.html');
    exit;
}
