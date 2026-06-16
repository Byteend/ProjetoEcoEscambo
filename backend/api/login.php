<?php
require_once __DIR__ . '/../lib/db.php';
require_post();

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
    http_response_code(422);
    json_response(['error' => 'Email inválido']);
}

$db = get_db();
$stmt = $db->prepare('SELECT id, password, activated, name FROM users WHERE email = :email');
$stmt->execute([':email' => $email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user || !password_verify($password, $user['password'])){
    http_response_code(401);
    json_response(['error' => 'Credenciais inválidas']);
}
if (!$user['activated']){
    http_response_code(403);
    json_response(['error' => 'Conta desativada. Ative via link enviado por e-mail.']);
}

$_SESSION['user_id'] = $user['id'];
$_SESSION['user_name'] = $user['name'];
json_response(['message' => 'Autenticado com sucesso']);
