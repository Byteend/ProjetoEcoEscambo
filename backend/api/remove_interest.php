<?php
require_once __DIR__ . '/../lib/db.php';
require_post();
if (empty($_SESSION['user_id'])){ http_response_code(401); json_response(['error'=>'Autenticação requerida']); }

$productId = intval($_POST['product_id'] ?? 0);
if ($productId <= 0) { http_response_code(400); json_response(['error'=>'product_id inválido']); }

$db = get_db();
$stmt = $db->prepare('DELETE FROM interests WHERE product_id = :pid AND user_id = :uid');
$stmt->execute([':pid'=>$productId, ':uid'=>$_SESSION['user_id']]);

json_response(['message'=>'Interesse removido com sucesso']);

?>
