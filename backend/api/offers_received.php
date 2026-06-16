<?php
require_once __DIR__ . '/../lib/db.php';
if (empty($_SESSION['user_id'])){ http_response_code(401); json_response(['error'=>'Autenticação requerida']); }

$db = get_db();
$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'GET'){
    $stmt = $db->prepare('SELECT i.id as interest_id, i.product_id as target_product_id, i.user_id as interested_user_id, u.name as interested_name, p.title as target_title, i.created_at FROM interests i JOIN users u ON i.user_id = u.id JOIN products p ON i.product_id = p.id WHERE p.user_id = :uid ORDER BY i.created_at DESC');
    $stmt->execute([':uid'=>$userId]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($items as &$it){
        $stmt2 = $db->prepare('SELECT id,title,description FROM products WHERE user_id = :uid AND status = "aberto"');
        $stmt2->execute([':uid'=>$it['interested_user_id']]);
        $it['interested_user_products'] = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    }
    json_response($items);
}

require_post();
$action = $_POST['action'] ?? '';
if ($action === 'reject'){ 
    $interestId = intval($_POST['interest_id'] ?? 0);
    if ($interestId <= 0) { http_response_code(400); json_response(['error'=>'interest_id inválido']); }
    $stmt = $db->prepare('DELETE FROM interests WHERE id = :id AND product_id IN (SELECT id FROM products WHERE user_id = :uid)');
    $stmt->execute([':id'=>$interestId, ':uid'=>$userId]);
    json_response(['message'=>'Interesse excluído']);
}
if ($action === 'reject_all'){
    $stmt = $db->prepare('DELETE FROM interests WHERE product_id IN (SELECT id FROM products WHERE user_id = :uid)');
    $stmt->execute([':uid'=>$userId]);
    json_response(['message'=>'Todas as ofertas rejeitadas']);
}

json_response(['error'=>'Ação inválida']);
