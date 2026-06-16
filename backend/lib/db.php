<?php
function get_db(){
    static $db = null;
    if ($db) return $db;

    $configFile = __DIR__ . '/../config.php';
    $config = file_exists($configFile) ? require $configFile : ['driver' => 'sqlite'];
    $driver = $config['driver'] ?? 'sqlite';

    if ($driver === 'mysql') {
        $charset = $config['charset'] ?? 'utf8mb4';
        $dsn = 'mysql:host=' . $config['host'] . ';dbname=' . $config['database'] . ';charset=' . $charset;
        $db = new PDO($dsn, $config['username'], $config['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);
        return $db;
    }

    $dir = __DIR__ . '/../data';
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    $path = $dir . '/database.sqlite';
    $db = new PDO('sqlite:' . $path);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $db;
}

function json_response($data){
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function require_post(){
    if ($_SERVER['REQUEST_METHOD'] !== 'POST'){
        http_response_code(405);
        json_response(['error' => 'Method not allowed']);
    }
}

session_start();
