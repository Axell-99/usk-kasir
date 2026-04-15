<?php
// Koneksi MySQL
$host   = 'localhost';
$dbname = 'kasirpro';
$dbuser = 'root';       
$dbpass = '';           

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $dbuser, $dbpass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die('Koneksi database gagal: ' . $e->getMessage());
}

// Helpers (tetap sama)
function formatRp($n) { return 'Rp ' . number_format($n, 0, ',', '.'); }
function today_str()  { return date('Y-m-d'); }
function h($s)        { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
function isAdmin()    { return isset($_SESSION['user']) && $_SESSION['user']['role'] === 'administrator'; }
function isLoggedIn() { return isset($_SESSION['user']); }