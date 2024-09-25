<?php
session_start();
include '../includes/functions.php';

$message = ''; // Untuk menyimpan pesan sukses atau error

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (registerUser($username, $password)) {
        $message = "Registrasi berhasil. Silakan login.";
    } else {
        $message = "Registrasi gagal. Username atau nomor akun sudah digunakan.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
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
    <h2 class="text-center">Daftar Akun Baru</h2>
    <?php if ($message): ?>
        <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST" class="mx-auto" style="max-width: 400px;">
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Daftar</button>
        <p class="text-center mt-3">Sudah punya akun? <a href="/bank-root/pages/login.php">Login di sini</a></p>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>