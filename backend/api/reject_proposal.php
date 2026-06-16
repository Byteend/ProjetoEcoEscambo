<?php
require_once __DIR__ . '/../lib/db.php';
require_post();
if (empty($_SESSION['user_id'])){ http_response_code(401); json_response(['error'=>'Autenticação requerida']); }

$proposalId = intval($_POST['proposal_id'] ?? 0);
if ($proposalId <= 0){ http_response_code(400); json_response(['error'=>'proposal_id inválido']); }

$db = get_db();
$stmt = $db->prepare('SELECT p.*, i.user_id as interested_user_id, i.id as interest_ref_id FROM proposals p JOIN interests i ON p.interest_id = i.id WHERE p.id = :id');
$stmt->execute([':id'=>$proposalId]);
$proposal = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$proposal){ http_response_code(404); json_response(['error'=>'Proposta não encontrada']); }

$stmt = $db->prepare('SELECT p.user_id as ofertante_id FROM products p JOIN interests i ON i.product_id = p.id WHERE i.id = :iid');
$stmt->execute([':iid'=>$proposal['interest_ref_id']]);
$ofertante = $stmt->fetchColumn();
if ($_SESSION['user_id'] != $proposal['interested_user_id'] && $_SESSION['user_id'] != $ofertante){ http_response_code(403); json_response(['error'=>'Sem permissão para rejeitar']); }

$stmt = $db->prepare("UPDATE proposals SET status = 'rejected' WHERE id = :id");
$stmt->execute([':id'=>$proposalId]);
json_response(['message'=>'Proposta rejeitada']);
