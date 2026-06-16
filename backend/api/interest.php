<?php
require_once __DIR__ . '/../lib/db.php';
require_post();
if (empty($_SESSION['user_id'])){ http_response_code(401); json_response(['error'=>'Autenticação requerida']); }

$productId = intval($_POST['product_id'] ?? 0);
if ($productId <= 0) { http_response_code(400); json_response(['error'=>'product_id inválido']); }

$db = get_db();
$stmt = $db->prepare('SELECT user_id FROM products WHERE id = :id');
$stmt->execute([':id'=>$productId]);
$owner = $stmt->fetchColumn();
if (!$owner){ http_response_code(404); json_response(['error'=>'Produto não encontrado']); }
if ($owner == $_SESSION['user_id']){ http_response_code(422); json_response(['error'=>'Não pode manifestar interesse no seu próprio produto']); }

$stmt = $db->prepare('SELECT id FROM interests WHERE product_id = :pid AND user_id = :uid');
$stmt->execute([':pid'=>$productId, ':uid'=>$_SESSION['user_id']]);
if ($stmt->fetchColumn()){ json_response(['message'=>'Interesse já registado']); }

$stmt = $db->prepare('INSERT INTO interests (product_id, user_id) VALUES (:pid, :uid)');
$stmt->execute([':pid'=>$productId, ':uid'=>$_SESSION['user_id']]);

json_response(['message'=>'Seu interesse foi registrado com sucesso. Aguarde o contato do ofertante']);
