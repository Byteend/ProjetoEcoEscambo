<?php
// Simple PDO wrapper using SQLite (moved to lib)
function get_db(){
    static $db = null;
    if ($db) return $db;
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
