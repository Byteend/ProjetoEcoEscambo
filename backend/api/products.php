<?php
require_once __DIR__ . '/../lib/db.php';

$db = get_db();
$page = max(1, intval($_GET['page'] ?? 1));
$per = max(1, min(100, intval($_GET['per_page'] ?? 10)));
$offset = ($page - 1) * $per;

$uid = intval($_SESSION['user_id'] ?? 0);
$interestedOnly = intval($_GET['interested'] ?? 0) === 1;
$baseWhere = "FROM products p JOIN users u ON p.user_id = u.id LEFT JOIN interests i ON i.product_id = p.id AND i.user_id = :uid WHERE p.status = 'aberto'";
if ($interestedOnly) {
    $baseWhere .= ' AND i.id IS NOT NULL';
}

$stmt = $db->prepare('SELECT p.id, p.title, p.description, p.status, p.image, u.name as user_name, i.id as interest_id ' . $baseWhere . ' ORDER BY p.created_at DESC LIMIT :limit OFFSET :offset');
$stmt->bindValue(':limit', $per, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':uid', $uid, PDO::PARAM_INT);
$stmt->execute();
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$countStmt = $db->prepare('SELECT COUNT(*) ' . $baseWhere);
$countStmt->bindValue(':uid', $uid, PDO::PARAM_INT);
$countStmt->execute();
$count = $countStmt->fetchColumn();

json_response(['page'=>$page,'per_page'=>$per,'total'=>intval($count),'items'=>$items]);
