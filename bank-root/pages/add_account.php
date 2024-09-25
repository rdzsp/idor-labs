<?php
session_start();
include '../includes/functions.php';

if (!isset($_SESSION['user'])) {
    header('Location: /bank-root/pages/login.php');
    exit;
}

$user = $_SESSION['user'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $account_number = $_POST['account_number'];
    $initial_balance = $_POST['initial_balance'];

    if (addBankAccount($user['id'], $account_number, $initial_balance)) {
        $message = "Akun bank berhasil ditambahkan.";
    } else {
        $message = "Gagal menambahkan akun bank.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Akun Bank</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">BANK Root</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link active" href="/bank-root/pages/main.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="/bank-root/pages/add_account.php">Tambah Akun Bank</a></li>
                <li class="nav-item"><a class="nav-link" href="/bank-root/pages/logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <h2 class="text-center">Tambah Akun Bank</h2>
    <?php if ($message): ?>
        <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST" class="mx-auto" style="max-width: 400px;">
        <div class="mb-3">
            <label for="account_number" class="form-label">Nomor Akun Bank</label>
            <input type="text" name="account_number" class="form-control" required>
            <input type="hidden" name="initial_balance" value=20000>
        </div>
        <button type="submit" class="btn btn-primary w-100">Tambah Akun</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
