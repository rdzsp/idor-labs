<?php
session_start();
include '../includes/functions.php';

if (!isset($_SESSION['user'])) {
    header('Location: /bank-root/pages/login.php');
    exit;
}

$user = $_SESSION['user'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $from_account = $_POST['from_account'];
    $to_account = $_POST['to_account'];
    $value = $_POST['value'];

    if (transferMoney($from_account, $to_account, $value)) {
        $message = "Transfer berhasil.";
    } else {
        $message = "Transfer gagal. Saldo tidak mencukupi.";
    }
}

// Ambil semua akun bank pengguna
$bankAccounts = getBankAccounts($user['id']); // Pastikan ada fungsi ini
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
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
    <h2 class="text-center">Dashboard</h2>
    <p>Selamat datang, <?= htmlspecialchars($user['username']) ?>!</p>

    <?php if (isset($message)): ?>
        <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST" class="mx-auto" style="max-width: 400px;">
        <div class="mb-3">
            <label class="form-label">Akun Pengirim</label>
            <?php if (count($bankAccounts) > 0): ?>
                <?php foreach ($bankAccounts as $account): ?>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="from_account" value="<?= htmlspecialchars($account['account_number']) ?>" required>
                        <label class="form-check-label">
                            <?= htmlspecialchars($account['account_number']) ?> - Saldo: Rp <?= number_format($account['balance'], 2, ',', '.') ?>
                        </label>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Belum ada akun bank.</p>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="to_account" class="form-label">Akun Penerima</label>
            <input type="text" name="to_account" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="value" class="form-label">Jumlah Transfer</label>
            <input type="number" name="value" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Transfer</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
