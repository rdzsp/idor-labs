<?php
// db.php
try {
    $db = new PDO('sqlite:' . __DIR__ . '/../transfer.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    echo "Unable to connect: " . $e->getMessage();
    die();
}
?>
