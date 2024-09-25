<?php
// Tentukan path untuk menyimpan database
$db_path = 'database/database.db';

// Cek apakah folder "database" sudah ada, jika belum buat folder tersebut
if (!file_exists('database')) {
    mkdir('database', 0755, true);
}

// Cek apakah database sudah ada
if (file_exists($db_path)) {
    echo "Database sudah ada: $db_path";
    exit;
}

// Buat koneksi ke database SQLite
$db = new SQLite3($db_path);

// Buat tabel `users`
$create_users_table = "
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL
);";

if ($db->exec($create_users_table)) {
    echo "Tabel 'users' berhasil dibuat.\n";
} else {
    echo "Gagal membuat tabel 'users'.\n";
}

// Buat tabel `uploads`
$create_uploads_table = "
CREATE TABLE IF NOT EXISTS uploads (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER,
    filename TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id)
);";

if ($db->exec($create_uploads_table)) {
    echo "Tabel 'uploads' berhasil dibuat.\n";
} else {
    echo "Gagal membuat tabel 'uploads'.\n";
}

// Tutup koneksi
$db->close();
?>
