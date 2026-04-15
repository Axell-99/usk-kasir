<?php
include '../config.php';
// Destroy session
session_destroy();

// Redirect ke halaman login dengan pesan sukses
header('Location: ../pages/login.php');
exit;
?>