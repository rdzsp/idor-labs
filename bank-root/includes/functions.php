<?php
// functions.php
include 'db.php';

// Fungsi untuk register user baru
function registerUser($username, $password) {
    global $db;

    $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if($user){
        return False;
    }

    $hash = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $db->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    return $stmt->execute([$username, $hash]);  // Saldo awal 1000
}

// Fungsi untuk login user
function loginUser($username, $password) {
    global $db;
    $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        return $user;
    } else {
        return false;
    }
}

// Fungsi untuk menambahkan akun bank baru
function addBankAccount($user_id, $account_number, $initial_balance = 0) {
    global $db;
    $stmt = $db->prepare("INSERT INTO bank_accounts (user_id, account_number, balance) VALUES (?, ?, ?)");
    return $stmt->execute([$user_id, $account_number, $initial_balance]);
}

// Fungsi untuk transfer uang antar akun bank
function transferMoney($from_account, $to_account, $value) {
    global $db;
    $db->beginTransaction();

    // Cek saldo akun pengirim
    $stmt = $db->prepare("SELECT balance FROM bank_accounts WHERE account_number = ?");
    $stmt->execute([$from_account]);
    $from_balance = $stmt->fetchColumn();

    if ($from_balance >= $value) {
        // Kurangi saldo akun pengirim
        $stmt = $db->prepare("UPDATE bank_accounts SET balance = balance - ? WHERE account_number = ?");
        $stmt->execute([$value, $from_account]);

        // Tambahkan saldo ke akun penerima
        $stmt = $db->prepare("UPDATE bank_accounts SET balance = balance + ? WHERE account_number = ?");
        $stmt->execute([$value, $to_account]);

        // Catat transaksi
        $stmt = $db->prepare("INSERT INTO transactions (from_account, to_account, value) VALUES (?, ?, ?)");
        $stmt->execute([$from_account, $to_account, $value]);

        $db->commit();
        return true;
    } else {
        $db->rollBack();
        return false;
    }
}

function getBankAccounts($userId) {
    global $db;
    $db->beginTransaction();
    
    // Ambil semua akun bank untuk pengguna
    $stmt = $db->prepare("SELECT * FROM bank_accounts WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $userId);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
