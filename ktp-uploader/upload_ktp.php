<?php
session_start();
$db = new SQLite3('database/database.db');

if (!isset($_SESSION['user_id'])) {
    header('Location: /ktp-uploader/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $db->prepare('SELECT * FROM uploads WHERE user_id = :user_id');
$stmt->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
$result = $stmt->execute();
$upload = $result->fetchArray(SQLITE3_ASSOC);


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['ktp'])) {
    $file_tmp = $_FILES['ktp']['tmp_name'];
    $file_name = "uploads/ktp-{$user_id}.png";

    if (move_uploaded_file($file_tmp, $file_name)) {
        $stmt = $db->prepare('INSERT INTO uploads (user_id, filename) VALUES (:user_id, :filename)');
        $stmt->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
        $stmt->bindValue(':filename', $file_name, SQLITE3_TEXT);
        $stmt->execute();
        echo "Upload berhasil!";
    } else {
        echo "Upload gagal!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload KTP</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="/ktp-uploader/index.php">Aplikasi KTP</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="/ktp-uploader/login.php">Login</a></li>
                <li class="nav-item"><a class="nav-link" href="/ktp-uploader/register.php">Register</a></li>
                <li class="nav-item"><a class="nav-link" href="/ktp-uploader/upload_ktp.php">Upload KTP</a></li>
            </ul>
        </div>
    </nav>

    <div class="container mt-5">
    <h2>Upload KTP</h2>

    <?php
    // Cek apakah user sudah upload KTP
    if ($upload) {
        echo "<p>KTP Anda sudah diupload:</p>";
        echo "<img src='/ktp-uploader/uploads/ktp-{$user_id}.png' alt='KTP' style='max-width: 300px;'>";
    } else {
        // Jika belum upload, tampilkan form upload
        ?>
        <form action="/ktp-uploader/upload_ktp.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="ktp">Upload KTP (PNG Only):</label>
                <input type="file" class="form-control-file" name="ktp" accept="image/png" required>
            </div>
            <button type="submit" class="btn btn-primary">Upload KTP</button>
        </form>
    <?php
    }
    ?>
    </div>

</body>
</html>
