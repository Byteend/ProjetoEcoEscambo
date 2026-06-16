<?php
require_once __DIR__ . '/lib/db.php';

$db = get_db();

function ensure_user($db, $name, $email, $password){
    $stmt = $db->prepare('SELECT id FROM users WHERE email = :email');
    $stmt->execute([':email'=>$email]);
    $id = $stmt->fetchColumn();
    if ($id) return $id;
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $db->prepare('INSERT INTO users (name,email,password,activated) VALUES (:name,:email,:password,1)');
    $stmt->execute([':name'=>$name,':email'=>$email,':password'=>$hash]);
    return $db->lastInsertId();
}

function create_product($db, $user_id, $title, $description){
    $stmt = $db->prepare('INSERT INTO products (user_id, title, description, status) VALUES (:uid,:title,:desc, "aberto")');
    $stmt->execute([':uid'=>$user_id, ':title'=>$title, ':desc'=>$description]);
    return $db->lastInsertId();
}

echo "Seeding test data for mbelo/jamile scenarios...\n";

// Ensure users (use .br emails as in the test scenarios)
$mbelo = ensure_user($db, 'Mbelo Teste', 'mbelo@teste.com.br', 'Senha1');
$jamile = ensure_user($db, 'Jamile Teste', 'jamile@teste.com.br', 'Senha1');
echo "Users ensured: mbelo={$mbelo}, jamile={$jamile}\n";

// Ensure Jamile has at least 1 product
$stmt = $db->prepare('SELECT id FROM products WHERE user_id = :uid ORDER BY id LIMIT 1');
$stmt->execute([':uid'=>$jamile]);
$jamile_p1 = $stmt->fetchColumn();
if (!$jamile_p1){
    $jamile_p1 = create_product($db, $jamile, 'Bicicleta Usada', 'Bicicleta em bom estado, aro 26');
    echo "Created Jamile product id={$jamile_p1}\n";
} else {
    echo "Jamile product exists id={$jamile_p1}\n";
}

// Ensure Mbelo has at least 2 products
$stmt = $db->prepare('SELECT id FROM products WHERE user_id = :uid ORDER BY id LIMIT 2');
$stmt->execute([':uid'=>$mbelo]);
$rows = $stmt->fetchAll(PDO::FETCH_COLUMN);
if (count($rows) < 2){
    if (count($rows) < 1) $rows[] = create_product($db, $mbelo, 'Livro de Programação', 'Livro sobre PHP e web');
    if (count($rows) < 2) $rows[] = create_product($db, $mbelo, 'Caixa de Ferramentas', 'Ferramentas básicas, usada');
    echo "Created Mbelo products ids=".implode(',', $rows)."\n";
}
$mbelo_p1 = $rows[0];
$mbelo_p2 = $rows[1];

// Create interest: mbelo interested in jamile's first product
$stmt = $db->prepare('SELECT id FROM interests WHERE product_id = :pid AND user_id = :uid');
$stmt->execute([':pid'=>$jamile_p1, ':uid'=>$mbelo]);
$interest_id = $stmt->fetchColumn();
if (!$interest_id){
    $stmt = $db->prepare('INSERT INTO interests (product_id, user_id) VALUES (:pid, :uid)');
    $stmt->execute([':pid'=>$jamile_p1, ':uid'=>$mbelo]);
    $interest_id = $db->lastInsertId();
    echo "Created interest id={$interest_id} (mbelo -> jamile product {$jamile_p1})\n";
} else {
    echo "Interest already exists id={$interest_id}\n";
}

// Create a pending proposal by jamile choosing one of mbelo's products (so Mbelo can accept later)
$stmt = $db->prepare('SELECT id FROM proposals WHERE interest_id = :iid AND ofertante_product_id = :opid');
$stmt->execute([':iid'=>$interest_id, ':opid'=>$mbelo_p2]);
$prop = $stmt->fetchColumn();
if (!$prop){
    $stmt = $db->prepare('INSERT INTO proposals (interest_id, ofertante_product_id, status) VALUES (:iid, :opid, "pending")');
    $stmt->execute([':iid'=>$interest_id, ':opid'=>$mbelo_p2]);
    $prop = $db->lastInsertId();
    echo "Created proposal id={$prop} (jamile -> proposed mbelo product {$mbelo_p2})\n";
} else {
    echo "Proposal already exists id={$prop}\n";
}

echo "Seeding complete.\n";
echo "Summary:\n - jamile (email=jamile@teste.com.br) product id={$jamile_p1}\n - mbelo (email=mbelo@teste.com.br) products ids={$mbelo_p1},{$mbelo_p2}\n - interest id={$interest_id}\n - proposal id={$prop}\n";

?>
