<?php

$action = $_POST['action'] ?? $_GET['action'] ?? '';


if ($action === 'login') {
    $uname = trim($_POST['username'] ?? '');
    $pass  = trim($_POST['password'] ?? '');

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$uname]);
    $u = $stmt->fetch();

    if ($u && $u['password'] === $pass) {
        $_SESSION['user'] = $u;
        header('Location: index.php?page=dashboard');
        exit;
    }
    $loginError = 'Username atau password salah!';
}

if ($action === 'logout') {
    unset($_SESSION['user']);
    unset($_SESSION['cart']);
    header('Location: index.php');
    exit;
}

if ($action === 'tambah_produk' && isAdmin()) {
    $nama  = trim($_POST['nama']  ?? '');
    $harga = (int)($_POST['harga'] ?? 0);
    $stok  = (int)($_POST['stok']  ?? 0);
    if ($nama && $harga > 0) {
        $pdo->prepare("INSERT INTO products (nama, harga, stok) VALUES (?, ?, ?)")
            ->execute([$nama, $harga, $stok]);
    }
    header('Location: index.php?page=produk');
    exit;
}

if ($action === 'edit_produk' && isAdmin()) {
    $id    = (int)($_POST['id']    ?? 0);
    $nama  = trim($_POST['nama']   ?? '');
    $harga = (int)($_POST['harga'] ?? 0);
    $stok  = (int)($_POST['stok']  ?? 0);
    $pdo->prepare("UPDATE products SET nama = ?, harga = ?, stok = ? WHERE id = ?")
        ->execute([$nama, $harga, $stok, $id]);
    header('Location: index.php?page=produk');
    exit;
}

if ($action === 'hapus_produk' && isAdmin()) {
    $id = (int)($_POST['id'] ?? 0);
    $pdo->prepare("DELETE FROM products WHERE id = ?")
        ->execute([$id]);
    header('Location: index.php?page=produk');
    exit;
}

if ($action === 'tambah_stok') {
    $id     = (int)($_POST['id']     ?? 0);
    $tambah = (int)($_POST['tambah'] ?? 0);
    if ($tambah > 0) {
        $pdo->prepare("UPDATE products SET stok = stok + ? WHERE id = ?")
            ->execute([$tambah, $id]);
    }
    header('Location: index.php?page=stok');
    exit;
}

if ($action === 'tambah_pelanggan') {
    $nama    = trim($_POST['nama']    ?? '');
    $alamat  = trim($_POST['alamat']  ?? '');
    $telepon = trim($_POST['telepon'] ?? '');
    if ($nama) {
        $pdo->prepare("INSERT INTO customers (nama, alamat, telepon) VALUES (?, ?, ?)")
            ->execute([$nama, $alamat, $telepon]);
    }
    header('Location: index.php?page=pelanggan');
    exit;
}

if ($action === 'edit_pelanggan') {
    $id      = (int)($_POST['id']      ?? 0);
    $nama    = trim($_POST['nama']     ?? '');
    $alamat  = trim($_POST['alamat']   ?? '');
    $telepon = trim($_POST['telepon']  ?? '');
    $pdo->prepare("UPDATE customers SET nama = ?, alamat = ?, telepon = ? WHERE id = ?")
        ->execute([$nama, $alamat, $telepon, $id]);
    header('Location: index.php?page=pelanggan');
    exit;
}

if ($action === 'hapus_pelanggan') {
    $id = (int)($_POST['id'] ?? 0);

    if ($id !== 1) {
        $pdo->prepare("DELETE FROM sales WHERE customer_id = ?")
            ->execute([$id]);

        $pdo->prepare("DELETE FROM customers WHERE id = ?")
            ->execute([$id]);
    }

    header('Location: index.php?page=pelanggan');
    exit;
}

if ($action === 'tambah_user' && isAdmin()) {
    $nama     = trim($_POST['nama']     ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $role     = ($_POST['role'] === 'administrator') ? 'administrator' : 'petugas';

    $cek = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $cek->execute([$username]);

    if ($nama && $username && $password && !$cek->fetch()) {
        $pdo->prepare("INSERT INTO users (nama, username, password, role) VALUES (?, ?, ?, ?)")
            ->execute([$nama, $username, $password, $role]);
    }
    header('Location: index.php?page=registrasi');
    exit;
}
if ($action === 'edit_users' && isAdmin()) {
    $nama  = trim($_POST['nama']   ?? '');
    $username = trim($_POST['username'] ?? 0);
    $password  = trim($_POST['password']  ?? 0);
    $pdo->prepare("UPDATE users SET nama = ?, username = ?, password = ? WHERE id = ?")
        ->execute([$nama, $username, $password, $id]);
    header('Location: index.php?page=registrasi&edit=' . $id);
    exit;
}

if ($action === 'hapus_user' && isAdmin()) {
    $id = (int)($_POST['id'] ?? 0);

    $jumlahAdmin = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'administrator'")->fetchColumn();
    $target = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $target->execute([$id]);
    $targetUser = $target->fetch();

    if ($targetUser && !($targetUser['role'] === 'administrator' && $jumlahAdmin <= 1)) {
        $pdo->prepare("DELETE FROM users WHERE id = ?")
            ->execute([$id]);
    }
    header('Location: index.php?page=registrasi');
    exit;
}

if ($action === 'add_cart') {
    $pid  = (int)($_POST['pid'] ?? 0);

    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$pid]);
    $prod = $stmt->fetch();

    if ($prod && $prod['stok'] > 0) {
        $found = false;
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['id'] === $pid) {
                if ($item['qty'] < $prod['stok']) $item['qty']++;
                $found = true;
                break;
            }
        }
        if (!$found) {
            $_SESSION['cart'][] = [
                'id'    => $pid,
                'nama'  => $prod['nama'],
                'harga' => $prod['harga'],
                'qty'   => 1,
            ];
        }
    }
    header('Location: index.php?page=penjualan');
    exit;
}

if ($action === 'update_cart') {
    $pid = (int)($_POST['pid'] ?? 0);
    $qty = (int)($_POST['qty'] ?? 0);

    $stmt = $pdo->prepare("SELECT stok FROM products WHERE id = ?");
    $stmt->execute([$pid]);
    $prod = $stmt->fetch();

    if ($qty <= 0) {
        $_SESSION['cart'] = array_values(
            array_filter($_SESSION['cart'], fn($i) => $i['id'] !== $pid)
        );
    } else {
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['id'] === $pid) {
                $item['qty'] = min($qty, $prod ? $prod['stok'] : $qty);
                break;
            }
        }
    }
    header('Location: index.php?page=penjualan');
    exit;
}

if ($action === 'remove_cart') {
    $pid = (int)($_POST['pid'] ?? 0);
    $_SESSION['cart'] = array_values(
        array_filter($_SESSION['cart'], fn($i) => $i['id'] !== $pid)
    );
    header('Location: index.php?page=penjualan');
    exit;
}

if ($action === 'clear_cart') {
    $_SESSION['cart'] = [];
    header('Location: index.php?page=penjualan');
    exit;
}

if ($action === 'checkout') {
    $cid   = (int)($_POST['customer_id'] ?? 1);
    $bayar = (int)($_POST['bayar']       ?? 0);
    $cart  = $_SESSION['cart'];
    $total = array_sum(array_map(fn($i) => $i['harga'] * $i['qty'], $cart));

    $stmtCust = $pdo->prepare("SELECT * FROM customers WHERE id = ?");
    $stmtCust->execute([$cid]);
    $cust = $stmtCust->fetch();

    if ($cart && $bayar >= $total && $cust) {
        $kode = 'INV-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));

        $pdo->prepare("
            INSERT INTO sales (kode_invoice, tanggal, customer_id, kasir_id, total_harga, bayar, kembalian)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ")->execute([
            $kode,
            today_str(),
            $cust['id'],
            $_SESSION['user']['id'],
            $total,
            $bayar,
            $bayar - $total,
        ]);

        $saleId = $pdo->lastInsertId();
        $stmtItem = $pdo->prepare("
            INSERT INTO sale_items (sale_id, product_id, nama_produk, harga, qty, subtotal)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmtStok = $pdo->prepare("UPDATE products SET stok = stok - ? WHERE id = ?");

        foreach ($cart as $item) {
            $stmtItem->execute([
                $saleId,
                $item['id'],
                $item['nama'],
                $item['harga'],
                $item['qty'],
                $item['harga'] * $item['qty'],
            ]);
            $stmtStok->execute([$item['qty'], $item['id']]);
        }
        $_SESSION['last_sale'] = [
            'id'           => $kode,
            'tanggal'      => today_str(),
            'pelangganNama'=> $cust['nama'],
            'kasir'        => $_SESSION['user']['nama'],
            'totalHarga'   => $total,
            'bayar'        => $bayar,
            'kembalian'    => $bayar - $total,
            'items'        => array_map(fn($i) => [
                'nama'     => $i['nama'],
                'qty'      => $i['qty'],
                'subtotal' => $i['harga'] * $i['qty'],
            ], $cart),
        ];

        $_SESSION['cart'] = [];
        header('Location: index.php?page=struk');
        exit;
    }
    header('Location: index.php?page=penjualan');
    exit;
}