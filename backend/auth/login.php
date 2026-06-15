<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../helpers/validation.php';

require_method('POST');

$email = strtolower(input_string('email') ?: input_string('txt_email'));
$senha = (string) ($_POST['senha'] ?? $_POST['txt_senha'] ?? '');

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $senha === '') {
    error_response('Informe e-mail e senha validos.', 422);
}

$stmt = db()->prepare('SELECT id, nome, email, senha_hash, ativo FROM utilizadores WHERE email = :email LIMIT 1');
$stmt->execute(['email' => $email]);
$user = $stmt->fetch();

if (!$user || !password_verify($senha, $user['senha_hash'])) {
    error_response('E-mail ou senha incorretos.', 401);
}

if ((int) $user['ativo'] !== 1) {
    error_response('Sua conta ainda esta desativada. Acesse o link de ativacao enviado no cadastro.', 403);
}

login_user($user);

success_response('Login realizado com sucesso.', [
    'user' => [
        'id' => (int) $user['id'],
        'nome' => $user['nome'],
        'email' => $user['email'],
    ],
]);
