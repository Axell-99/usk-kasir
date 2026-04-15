<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$sale = $_SESSION['last_sale'] ?? null;

if (!$sale) {
    header('Location: index.php?page=penjualan');
    exit;
}

$items = $sale['items'];
?>
<div class="page">
  <div class="struk">
    <div class="struk-head">
      <div style="font-size:3rem">✅</div>
      <div style="font-weight:700;color:#16a34a;font-size:1.1rem;margin-top:4px">Transaksi Berhasil!</div>
      <div style="font-family:monospace;font-size:.78rem;color:#94a3b8;margin-top:4px"><?= h($sale['id']) ?></div>
    </div>

    <hr class="struk-sep">

    <table class="struk-table" style="width:100%">
      <tr>
        <td>Tanggal</td>
        <td class="td-r"><?= h($sale['tanggal']) ?></td>
      </tr>
      <tr>
        <td>Pelanggan</td>
        <td class="td-r"><?= h($sale['pelangganNama']) ?></td>
      </tr>
      <tr>
        <td>Kasir</td>
        <td class="td-r"><?= h($sale['kasir']) ?></td>
      </tr>
    </table>

    <hr class="struk-sep">

    <?php foreach ($items as $item): ?>
    <div style="display:flex;justify-content:space-between;margin-bottom:6px">
      <span>
        <?= h($item['nama']) ?>
        <span style="color:#94a3b8">x<?= $item['qty'] ?></span>
      </span>
      <span style="font-weight:600"><?= formatRp($item['subtotal']) ?></span>
    </div>
    <?php endforeach ?>

    <hr class="struk-sep">

    <table class="struk-table" style="width:100%;font-weight:600">
      <tr>
        <td>Total</td>
        <td class="td-r"><?= formatRp($sale['totalHarga']) ?></td>
      </tr>
      <tr>
        <td>Bayar</td>
        <td class="td-r"><?= formatRp($sale['bayar']) ?></td>
      </tr>
      <tr style="color:#16a34a">
        <td>Kembalian</td>
        <td class="td-r"><?= formatRp($sale['kembalian']) ?></td>
      </tr>
    </table>

    <hr class="struk-sep">

    <div style="text-align:center;color:#94a3b8;font-size:.78rem">
      Terima kasih telah berbelanja! 🙏
    </div>

    <div style="margin-top:16px;display:flex;gap:8px">
      <a href="index.php?page=penjualan" class="btn btn-primary" style="flex:1;text-align:center">Transaksi Baru</a>
      <a href="index.php?page=dashboard" class="btn btn-ghost"   style="flex:1;text-align:center">Dashboard</a>
    </div>
  </div>
</div>
<script>
window.onload = function() {
  window.print();
}
</script>