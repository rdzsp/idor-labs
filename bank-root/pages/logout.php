<?php
session_start();
session_destroy();
header('Location: /bank-root/pages/login.php');
exit;
?>
