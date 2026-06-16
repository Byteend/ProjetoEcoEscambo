<?php
require_once __DIR__ . '/../lib/db.php';

require_post();

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$password2 = $_POST['password2'] ?? '';

$errors = [];
if ($name === '') $errors['name'] = 'Nome é obrigatório';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Email inválido';
if ($password !== $password2) $errors['password2'] = 'As senhas não coincidem';
if (strlen($password) < 6) $errors['password'] = 'Senha deve ter ao menos 6 caracteres';
if (!preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) $errors['password'] = 'Senha deve combinar letras e números';
if (!preg_match('/[a-z]/', $password) || !preg_match('/[A-Z]/', $password)) $errors['password'] = 'Senha deve conter letras maiúsculas e minúsculas';

if (!empty($errors)){
    http_response_code(422);
    json_response(['errors' => $errors]);
}

$db = get_db();
$stmt = $db->prepare('SELECT id FROM users WHERE email = :email');
$stmt->execute([':email' => $email]);
$exists = $stmt->fetchColumn();
if ($exists){
    http_response_code(409);
    json_response(['error' => 'Email já cadastrado']);
}

$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $db->prepare('INSERT INTO users (name, email, password, activated) VALUES (:name, :email, :password, 0)');
$stmt->execute([':name'=>$name, ':email'=>$email, ':password'=>$hash]);
$userId = $db->lastInsertId();

$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
$activationLink = sprintf('%s://%s%s/backend/api/activate.php?id=%s', $scheme, $host, $base === '/' ? '' : $base, $userId);

json_response([
    'message' => 'Utilizador registrado com sucesso. Ative sua conta com o link enviado para seu e-mail.',
    'activation_link' => $activationLink
]);
