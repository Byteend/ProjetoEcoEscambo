<?php
require_once __DIR__ . '/lib/db.php';
$sql = file_get_contents(__DIR__ . '/migrations/migrations.sql');
$db = get_db();
$db->exec($sql);
echo "Migrações aplicadas com sucesso.\n";
