<?php
session_start();
require_once 'config.php';
$loginError = '';
require_once 'actions.php';
$page = $_GET['page'] ?? 'dashboard';
if (!isLoggedIn()) {
    $page = 'login';
}
$products  = $pdo->query("SELECT * FROM products ORDER BY nama")->fetchAll();
$users     = $pdo->query("SELECT * FROM users ORDER BY nama")->fetchAll();
$customers = $pdo->query("SELECT * FROM customers ORDER BY id")->fetchAll();
$sales     = $pdo->query("
    SELECT
        s.id,
        s.kode_invoice,
        s.tanggal,
        s.total_harga  AS totalHarga,
        s.bayar,
        s.kembalian,
        c.nama         AS pelangganNama,
        u.nama         AS kasir
    FROM sales s
    JOIN customers c ON c.id = s.customer_id
    JOIN users u     ON u.id = s.kasir_id
    ORDER BY s.created_at DESC
")->fetchAll();

$cart      = $_SESSION['cart'] ?? [];
$user      = $_SESSION['user'] ?? null;
$cartTotal = array_sum(array_map(fn($i) => $i['harga'] * $i['qty'], $cart));

$menus = [
    ['id' => 'dashboard',  'label' => 'Dashboard',      'icon' => '', 'roles' => ['administrator', 'petugas']],
    ['id' => 'penjualan',  'label' => 'Kasir',    'icon' => '', 'roles' => ['administrator', 'petugas']],
    ['id' => 'produk',     'label' => 'Data Produk',    'icon' => '', 'roles' => ['administrator', 'petugas']],
    ['id' => 'stok',       'label' => 'Stok Barang',    'icon' => '', 'roles' => ['administrator', 'petugas']],
    ['id' => 'pelanggan',  'label' => 'Data Pelanggan', 'icon' => '', 'roles' => ['administrator', 'petugas']],
    ['id' => 'laporan',    'label' => 'Laporan',        'icon' => '', 'roles' => ['administrator']],
    ['id' => 'registrasi', 'label' => 'Kelola User',    'icon' => '', 'roles' => ['administrator']],
];
$visibleMenus = array_filter($menus, fn($m) => $user && in_array($user['role'], $m['roles']));

?><!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kasir – Sistem Manajemen Kasir</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<?php if ($page === 'login'): ?>

  <?php require 'pages/login.php'; ?>

<?php else: ?>

  <div class="app">

    <div class="sidebar">
      <div class="sb-header">
        <div class="sb-logo"> Kasir </div>
        <div class="sb-user">
          <div class="name"><?= h($user['nama']) ?></div>
          <span class="sb-role <?= $user['role'] === 'petugas' ? 'petugas' : '' ?>">
            <?= h($user['role']) ?>
          </span>
        </div>
      </div>

      <nav class="sb-nav">
        <?php foreach ($visibleMenus as $m): ?>
          <a href="index.php?page=<?= $m['id'] ?>">
            <button class="sb-btn <?= $page === $m['id'] ? 'active' : '' ?>">
              <span><?= $m['icon'] ?></span>
              <span><?= h($m['label']) ?></span>
            </button>
          </a>
        <?php endforeach ?>
      </nav>

      <div class="sb-footer">
        <form method="POST">
          <input type="hidden" name="action" value="logout">
          <button class="sb-btn sb-logout" type="submit">Logout</button>
        </form>
      </div>
    </div>

    <div class="main">
      <?php
        $allowed_pages = ['dashboard', 'penjualan', 'struk', 'produk', 'stok', 'pelanggan', 'laporan', 'registrasi'];
        $page_file     = 'pages/' . $page . '.php';

        if (in_array($page, $allowed_pages) && file_exists($page_file)) {
            require $page_file;
        } else {
      ?>
        <div class="page" style="text-align:center;padding-top:60px;color:#94a3b8">
          <div style="font-size:3rem">🚫</div>
          <p style="margin-top:12px">Halaman tidak ditemukan atau akses ditolak.</p>
          <a href="index.php?page=dashboard" class="btn btn-primary" style="margin-top:16px;display:inline-block">Ke Dashboard</a>
        </div>
      <?php } ?>
    </div>

  </div>

<?php endif ?>

</body>
</html>