<?php

declare(strict_types=1);

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/helpers/response.php';

try {
    $stmt = db()->query('SELECT DATABASE() AS database_name, VERSION() AS mysql_version');
    $databaseInfo = $stmt->fetch();

    success_response('Backend conectado ao MySQL com sucesso.', [
        'database' => $databaseInfo['database_name'] ?? null,
        'mysql_version' => $databaseInfo['mysql_version'] ?? null,
    ]);
} catch (PDOException $exception) {
    error_response('Falha ao conectar no MySQL. Confira backend/config/app.php e se o banco foi criado.', 500);
}
