<?php
require_once __DIR__ . '/../lib/db.php';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) { http_response_code(400); json_response(['error'=>'id inválido']); }

$db = get_db();
$stmt = $db->prepare('SELECT p.*, u.name as user_name, u.email as user_email FROM products p JOIN users u ON p.user_id = u.id WHERE p.id = :id');
$stmt->execute([':id'=>$id]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$item){ http_response_code(404); json_response(['error'=>'Produto não encontrado']); }

json_response($item);
