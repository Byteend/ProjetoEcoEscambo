<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../helpers/validation.php';

require_method('POST');

$nome = input_string('nome');
$email = strtolower(input_string('email'));
$senha = (string) ($_POST['senha'] ?? '');
$confirmacaoSenha = (string) ($_POST['confirmacao_senha'] ?? $_POST['repitaSenha'] ?? '');

$errors = validate_registration($nome, $email, $senha, $confirmacaoSenha);

if ($errors !== []) {
    error_response('Corrija os dados informados.', 422, $errors);
}

try {
    $pdo = db();

    $stmt = $pdo->prepare('SELECT id FROM utilizadores WHERE email = :email LIMIT 1');
    $stmt->execute(['email' => $email]);

    if ($stmt->fetch()) {
        error_response('Este e-mail ja esta cadastrado.', 409);
    }

    $stmt = $pdo->prepare(
        'INSERT INTO utilizadores (nome, email, senha_hash, ativo)
         VALUES (:nome, :email, :senha_hash, 0)'
    );
    $stmt->execute([
        'nome' => $nome,
        'email' => $email,
        'senha_hash' => password_hash($senha, PASSWORD_DEFAULT),
    ]);

    $userId = (int) $pdo->lastInsertId();
    $activationUrl = APP_BASE_PATH . '/backend/auth/activate.php?id=' . $userId;

    success_response('Cadastro realizado. Ative sua conta usando o link de simulacao enviado.', [
        'user_id' => $userId,
        'activation_url' => $activationUrl,
    ], 201);
} catch (PDOException $exception) {
    error_response('Nao foi possivel cadastrar o utilizador.', 500);
}
