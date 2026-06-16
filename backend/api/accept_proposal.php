<?php
require_once __DIR__ . '/../lib/db.php';
require_post();
if (empty($_SESSION['user_id'])){ http_response_code(401); json_response(['error'=>'Autenticação requerida']); }

$proposalId = intval($_POST['proposal_id'] ?? 0);
if ($proposalId <= 0){ http_response_code(400); json_response(['error'=>'proposal_id inválido']); }

$db = get_db();
$stmt = $db->prepare('SELECT p.*, i.user_id as interested_user_id, i.product_id as target_product_id, p.ofertante_product_id FROM proposals p JOIN interests i ON p.interest_id = i.id WHERE p.id = :id');
$stmt->execute([':id'=>$proposalId]);
$proposal = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$proposal){ http_response_code(404); json_response(['error'=>'Proposta não encontrada']); }

if ($proposal['interested_user_id'] != $_SESSION['user_id']){ http_response_code(403); json_response(['error'=>'Só o utilizador-interessado pode aceitar esta proposta']); }

$stmt = $db->prepare("UPDATE proposals SET status = 'accepted' WHERE id = :id");
$stmt->execute([':id'=>$proposalId]);

$stmt = $db->prepare("UPDATE products SET status = 'finalizada' WHERE id IN (:a, :b)");
$stmt->bindValue(':a', $proposal['target_product_id'], PDO::PARAM_INT);
$stmt->bindValue(':b', $proposal['ofertante_product_id'], PDO::PARAM_INT);
$stmt->execute();

json_response(['message'=>'Proposta aceite; produtos finalizados']);
