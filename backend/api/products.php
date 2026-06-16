<?php
require_once __DIR__ . '/../lib/db.php';

$db = get_db();
$page = max(1, intval($_GET['page'] ?? 1));
$per = max(1, min(100, intval($_GET['per_page'] ?? 10)));
$offset = ($page - 1) * $per;

$stmt = $db->prepare('SELECT p.id, p.title, p.description, p.status, p.image, u.name as user_name FROM products p JOIN users u ON p.user_id = u.id WHERE p.status = "aberto" ORDER BY p.created_at DESC LIMIT :limit OFFSET :offset');
$stmt->bindValue(':limit', $per, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$count = $db->query('SELECT COUNT(*) FROM products WHERE status = "aberto"')->fetchColumn();

json_response(['page'=>$page,'per_page'=>$per,'total'=>intval($count),'items'=>$items]);
