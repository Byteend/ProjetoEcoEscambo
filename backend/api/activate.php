<?php
require_once __DIR__ . '/../lib/db.php';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0){
    http_response_code(400);
    echo 'Link de ativação inválido.';
    exit;
}

$db = get_db();
$stmt = $db->prepare('SELECT id, activated, email FROM users WHERE id = :id');
$stmt->execute([':id' => $id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user){
    http_response_code(404);
    echo 'Utilizador não encontrado.';
    exit;
}
if ($user['activated']){
    echo 'Conta já ativada.';
    exit;
}

$stmt = $db->prepare('UPDATE users SET activated = 1 WHERE id = :id');
$stmt->execute([':id' => $id]);

echo 'A conta relativa ao e-mail foi desbloqueada. Pode agora autenticar-se.';
