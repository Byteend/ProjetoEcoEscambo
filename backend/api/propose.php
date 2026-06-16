<?php
require_once __DIR__ . '/../lib/db.php';
require_post();
if (empty($_SESSION['user_id'])){ http_response_code(401); json_response(['error'=>'Autenticação requerida']); }

$interestId = intval($_POST['interest_id'] ?? 0);
$offeredProductId = intval($_POST['offered_product_id'] ?? 0);
if ($interestId <= 0 || $offeredProductId <= 0){ http_response_code(400); json_response(['error'=>'Parâmetros inválidos']); }

$db = get_db();
$stmt = $db->prepare('SELECT * FROM interests WHERE id = :id');
$stmt->execute([':id'=>$interestId]);
$interest = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$interest){ http_response_code(404); json_response(['error'=>'Interesse não encontrado']); }

$stmt = $db->prepare('SELECT user_id FROM products WHERE id = :pid');
$stmt->execute([':pid'=>$interest['product_id']]);
$owner = $stmt->fetchColumn();
if ($owner != $_SESSION['user_id']){ http_response_code(403); json_response(['error'=>'Só o ofertante pode propor trocas nessa oferta']); }

$stmt = $db->prepare('SELECT user_id FROM products WHERE id = :pid');
$stmt->execute([':pid'=>$offeredProductId]);
$owner2 = $stmt->fetchColumn();
if ($owner2 != $interest['user_id']){ http_response_code(422); json_response(['error'=>'O produto proposto deve pertencer ao utilizador-interessado']); }

$stmt = $db->prepare("INSERT INTO proposals (interest_id, ofertante_product_id, status) VALUES (:iid, :opid, 'pending')");
$stmt->execute([':iid'=>$interestId, ':opid'=>$offeredProductId]);
json_response(['message'=>'Proposta registada com sucesso']);
