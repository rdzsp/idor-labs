<?php
// create_database.php

// Tentukan nama file database
$dbFile = __DIR__ . '/transfer.db';

try {
    // Buat koneksi ke database SQLite
    $db = new PDO('sqlite:' . $dbFile);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // SQL untuk membuat tabel users
    $createUsersTable = "CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT NOT NULL UNIQUE,
        password TEXT NOT NULL
    );";

    // SQL untuk membuat tabel bank_accounts
    $createBankAccountsTable = "CREATE TABLE IF NOT EXISTS bank_accounts (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER,
        account_number TEXT NOT NULL UNIQUE,
        balance REAL DEFAULT 0,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    );";

    // SQL untuk membuat tabel transactions
    $createTransactionsTable = "CREATE TABLE IF NOT EXISTS transactions (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        from_account TEXT,
        to_account TEXT,
        value REAL,
        transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );";

    // Eksekusi SQL untuk membuat tabel
    $db->exec($createUsersTable);
    $db->exec($createBankAccountsTable);
    $db->exec($createTransactionsTable);

    echo "Database dan tabel berhasil dibuat.";

} catch (PDOException $e) {
    echo "Terjadi kesalahan: " . $e->getMessage();
}
?>
