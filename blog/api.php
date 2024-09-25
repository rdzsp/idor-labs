<?php
require 'JWT.php'; // Include file JWT.php
require 'Key.php';

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

$key = "asdsadassfg123123"; // Kunci rahasia untuk encoding dan decoding JWT

// Menghubungkan ke SQLite database
$db = new PDO('sqlite:blog.db');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Mendapatkan metode HTTP
$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'],'/'));
$input = json_decode(file_get_contents('php://input'), true);

// Fungsi untuk menghasilkan JWT token
function generate_jwt($user_id, $key) {
    $payload = array(
        "iss" => "http://127.0.0.1", // Issuer
        "iat" => time(),             // Waktu token dibuat
        "exp" => time() + (60*60),   // Expiry (1 jam)
        "user_id" => $user_id        // ID pengguna
    );
    return JWT::encode($payload, $key, 'HS256');
}

// Fungsi untuk memverifikasi JWT token
function verify_jwt($jwt, $key) {
    try {
        return JWT::decode($jwt, new Key($key, 'HS256'));
    } catch (Exception $e) {
        return false;
    }
}

// Endpoint untuk register
if ($method == 'POST' && $request[0] == 'v2' && $request[1] == 'register') {
    $username = $input['username'];
    $password = password_hash($input['password'], PASSWORD_BCRYPT);
    
    // Simpan user ke dalam database
    $stmt = $db->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
    $stmt->execute([$username, $password]);
    
    header('Content-Type: application/json');
    echo json_encode(['message' => 'User registered successfully']);
    exit;
}

// Endpoint untuk login
if ($method == 'POST' && $request[0] == 'v2' && $request[1] == 'login') {
    $username = $input['username'];
    $password = $input['password'];
    
    // Ambil user dari database
    $stmt = $db->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    header('Content-Type: application/json');
    if ($user && password_verify($password, $user['password'])) {
        // Berhasil login, generate token
        $token = generate_jwt($user['id'], $key);
        echo json_encode(['token' => $token]);
    } else {
        echo json_encode(['message' => 'Invalid credentials']);
    }
    exit;
}

// Melindungi endpoint lainnya dengan Bearer Token
$headers = apache_request_headers();
$authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : '';

if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
    header('Content-Type: application/json');
    $jwt = $matches[1];
    try{
        $decoded = verify_jwt($jwt, $key);
    }catch(Exception $e){
        echo json_encode(['message' => 'Invalid token']);
        exit;
    }
    
    if (!$decoded) {
        echo json_encode(['message' => 'Invalid token']);
        exit;
    }
} else {
    echo json_encode(['message' => 'Token not provided']);
    exit;
}

// Endpoint untuk mendapatkan semua blog user
if ($method == 'GET' && $request[0] == 'v1' && $request[1] == 'blog') {
    $user_id = $decoded->user_id;
    
    // Ambil semua blog milik user
    $stmt = $db->prepare('SELECT * FROM blogs');
    $stmt->execute();
    $blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    header('Content-Type: application/json');
    echo json_encode($blogs);
    exit;
}

// Endpoint untuk membuat blog baru
if ($method == 'POST' && $request[0] == 'v1' && $request[1] == 'blog') {
    $user_id = $decoded->user_id;
    $title = $input['title'];
    $content = $input['content'];
    
    // Simpan blog ke dalam database
    $stmt = $db->prepare('INSERT INTO blogs (user_id, title, content) VALUES (?, ?, ?)');
    $stmt->execute([$user_id, $title, $content]);
    header('Content-Type: application/json');
    echo json_encode(['message' => 'Blog created successfully']);
    exit;
}

// Endpoint untuk memperbarui blog
if ($method == 'PUT' && $request[0] == 'v1' && $request[1] == 'blog' && isset($request[2])) {
    $blog_id = $request[2];
    $title = $input['title'];
    $content = $input['content'];
    
    // Update blog milik user
    $stmt = $db->prepare('UPDATE blogs SET title = ?, content = ? WHERE id = ?');
    $stmt->execute([$title, $content, $blog_id]);
    header('Content-Type: application/json');
    echo json_encode(['message' => 'Blog updated successfully']);
    exit;
}

// Endpoint untuk menghapus blog
if ($method == 'DELETE' && $request[0] == 'v1' && $request[1] == 'blog' && isset($request[2])) {
    $blog_id = $request[2];
    
    // Hapus blog milik user
    $stmt = $db->prepare('DELETE FROM blogs WHERE id = ?');
    $stmt->execute([$blog_id]);
    header('Content-Type: application/json');
    echo json_encode(['message' => 'Blog deleted successfully']);
    exit;
}

// Endpoint untuk mendapatkan semua blog user
if ($method == 'GET' && $request[0] == 'v2' && $request[1] == 'blog') {
    $user_id = $decoded->user_id;
    
    // Ambil semua blog milik user
    $stmt = $db->prepare('SELECT * FROM blogs');
    $stmt->execute();
    $blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    header('Content-Type: application/json');
    echo json_encode($blogs);
    exit;
}

// Endpoint untuk membuat blog baru
if ($method == 'POST' && $request[0] == 'v2' && $request[1] == 'blog') {
    $user_id = $decoded->user_id;
    $title = $input['title'];
    $content = $input['content'];
    
    // Simpan blog ke dalam database
    $stmt = $db->prepare('INSERT INTO blogs (user_id, title, content) VALUES (?, ?, ?)');
    $stmt->execute([$user_id, $title, $content]);
    header('Content-Type: application/json');
    echo json_encode(['message' => 'Blog created successfully']);
    exit;
}

// Endpoint untuk memperbarui blog (hanya milik user)
if ($method == 'PUT' && $request[0] == 'v2' && $request[1] == 'blog' && isset($request[2])) {
    $blog_id = $request[2];
    $user_id = $decoded->user_id;
    $title = $input['title'];
    $content = $input['content'];

    $stmt = $db->prepare('SELECT id FROM blogs WHERE id = ? AND user_id = ?');
    $stmt->execute([$blog_id, $user_id]);
    $blog = $stmt->fetch(PDO::FETCH_ASSOC);
    if(!$blog){
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Blog not found!']);
        exit;
    }
    
    // Update blog milik user
    $stmt = $db->prepare('UPDATE blogs SET title = ?, content = ? WHERE id = ? AND user_id = ?');
    $stmt->execute([$title, $content, $blog_id, $user_id]);
    header('Content-Type: application/json');
    echo json_encode(['message' => 'Blog updated successfully']);
    exit;
}

// Endpoint untuk menghapus blog (hanya milik user)
if ($method == 'DELETE' && $request[0] == 'v2' && $request[1] == 'blog' && isset($request[2])) {
    $blog_id = $request[2];
    $user_id = $decoded->user_id;

    $stmt = $db->prepare('SELECT id FROM blogs WHERE id = ? AND user_id = ?');
    $stmt->execute([$blog_id, $user_id]);
    $blog = $stmt->fetch(PDO::FETCH_ASSOC);
    if(!$blog){
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Blog not found!']);
        exit;
    }
    
    // Hapus blog milik user
    $stmt = $db->prepare('DELETE FROM blogs WHERE id = ? AND user_id = ?');
    $stmt->execute([$blog_id, $user_id]);
    header('Content-Type: application/json');
    echo json_encode(['message' => 'Blog deleted successfully']);
    exit;
}

