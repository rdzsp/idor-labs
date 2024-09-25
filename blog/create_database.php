<?php
$dbFile = __DIR__ . '/blog.db';

// Buat atau buka database SQLite
$db = new PDO('sqlite:' . $dbFile);

// Set error mode ke exception
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {
    // Buat tabel users
    $db->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT NOT NULL UNIQUE,
        password TEXT NOT NULL
    )");

    // Buat tabel blogs
    $db->exec("CREATE TABLE IF NOT EXISTS blogs (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        title TEXT NOT NULL,
        content TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    echo "Database and tables created successfully.";
} catch (PDOException $e) {
    echo "Error creating database or tables: " . $e->getMessage();
}

// Tutup koneksi
$db = null;
?>
