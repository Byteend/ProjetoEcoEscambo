<?php
require_once __DIR__ . '/../lib/db.php';

if (empty($_SESSION['user_id'])){
    http_response_code(401);
    json_response(['error'=>'Autenticação requerida']);
}

$db = get_db();
$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'GET'){
    $stmt = $db->prepare('SELECT * FROM products WHERE user_id = :uid ORDER BY created_at DESC');
    $stmt->execute([':uid'=>$userId]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    json_response($items);
}

require_post();
$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
if ($title === ''){ http_response_code(422); json_response(['error'=>'Título obrigatório']); }

$stmt = $db->prepare('INSERT INTO products (user_id, title, description, status, image) VALUES (:uid,:title,:desc,"aberto",:image)');
$stmt->execute([':uid'=>$userId, ':title'=>$title, ':desc'=>$description, ':image'=>$_POST['image'] ?? null]);
json_response(['message'=>'Produto criado','id'=>$db->lastInsertId()]);
