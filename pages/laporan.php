<?php
if (!isAdmin()) {
    header('Location: index.php?page=dashboard');
    exit;
}

$filterDate = $_GET['tgl'] ?? '';
$filteredS  = $filterDate
    ? array_filter($sales, fn($s) => $s['tanggal'] === $filterDate)
    : $sales;

$totalPend = array_sum(array_map(fn($s) => $s['totalHarga'] ?? 0, $filteredS));
$totalItem = 0;
$prodStats = [];

foreach ($filteredS as $sale) {
    foreach (($sale['items'] ?? []) as $item) {
        $totalItem += $item['qty'];
        if (!isset($prodStats[$item['nama']])) {
            $prodStats[$item['nama']] = ['qty' => 0, 'total' => 0];
        }
        $prodStats[$item['nama']]['qty']   += $item['qty'];
        $prodStats[$item['nama']]['total'] += $item['subtotal'];
    }
}

uasort($prodStats, fn($a, $b) => $b['qty'] - $a['qty']);
$maxQty   = $prodStats ? max(array_column($prodStats, 'qty')) : 1;
$recentFS = array_reverse(array_values($filteredS));
?>
<div class="page">
  <h2 class="page-title">📈 Laporan Penjualan</h2>

  <div style="display:flex;align-items:center;gap:12px;margin-bottom:20px;flex-wrap:wrap">
    <form method="GET" style="display:flex;gap:8px;align-items:center">
      <input type="hidden" name="page" value="laporan">
      <input class="form-input" type="date" name="tgl" value="<?= h($filterDate) ?>" style="width:auto">
      <button class="btn btn-ghost btn-sm" type="submit">Filter</button>
    </form>
    <?php if ($filterDate): ?>
      <a href="index.php?page=laporan" class="btn-link btn-link-blue" style="font-size:.85rem">Tampilkan Semua</a>
    <?php endif ?>
    <span style="font-size:.85rem;color:#94a3b8">
      <?= count($filteredS) ?> transaksi<?= $filterDate ? " pada $filterDate" : " (semua waktu)" ?>
    </span>
  </div>

  <div class="stat-grid" style="max-width:600px">
    <div class="stat-card card-green">
      <div class="icon">💰</div>
      <div class="label">Total Pendapatan</div>
      <div class="val"><?= formatRp($totalPend) ?></div>
    </div>
    <div class="stat-card card-blue">
      <div class="icon">🧾</div>
      <div class="label">Total Transaksi</div>
      <div class="val"><?= count($filteredS) ?></div>
    </div>
    <div class="stat-card card-purple">
      <div class="icon">📦</div>
      <div class="label">Item Terjual</div>
      <div class="val"><?= $totalItem ?> item</div>
    </div>
  </div>

  <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;max-width:900px">

    <!-- Riwayat Transaksi -->
    <div class="table-wrap">
      <div style="padding:12px 16px;border-bottom:1px solid #f1f5f9;font-weight:600;font-size:.9rem">🧾 Riwayat Transaksi</div>
      <div style="overflow:auto;max-height:320px">
        <?php if (empty($filteredS)): ?>
          <div class="text-center" style="padding:40px;color:#94a3b8;font-size:.85rem">Tidak ada transaksi.</div>
        <?php else: ?>
        <table>
          <thead>
            <tr>
              <th>Invoice</th>
              <th>Pelanggan</th>
              <th>Kasir</th>
              <th class="text-right">Total</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($recentFS as $s): ?>
            <tr>
              <td style="font-family:monospace;font-size:.75rem;color:#3b82f6"><?= substr($s['id'], -8) ?></td>
              <td><?= h($s['pelangganNama']) ?></td>
              <td style="color:#94a3b8;font-size:.8rem"><?= h($s['kasir']) ?></td>
              <td class="text-right" style="font-weight:700;color:#16a34a"><?= formatRp($s['totalHarga'] ?? 0) ?></td>
            </tr>
            <?php endforeach ?>
          </tbody>
        </table>
        <?php endif ?>
      </div>
    </div>

    <!-- Produk Terlaris -->
    <div class="table-wrap" style="padding:16px">
      <div style="font-weight:600;margin-bottom:16px;font-size:.9rem">🏆 Produk Terlaris</div>
      <?php if (empty($prodStats)): ?>
        <div class="text-center" style="padding:40px;color:#94a3b8;font-size:.85rem">Belum ada data penjualan.</div>
      <?php else: ?>
        <?php $i = 1; foreach (array_slice($prodStats, 0, 6, true) as $pname => $pstat): ?>
        <div style="margin-bottom:14px">
          <div style="display:flex;justify-content:space-between;font-size:.83rem;margin-bottom:5px">
            <span style="font-weight:500">
              <span style="color:#94a3b8;margin-right:4px"><?= $i ?>.</span><?= h($pname) ?>
            </span>
            <span style="color:#16a34a;font-weight:600"><?= formatRp($pstat['total']) ?></span>
          </div>
          <div style="display:flex;align-items:center;gap:8px">
            <div class="bar-wrap">
              <div class="bar-fill" style="width:<?= round(($pstat['qty'] / $maxQty) * 100) ?>%"></div>
            </div>
            <span style="font-size:.75rem;color:#94a3b8;width:32px;text-align:right"><?= $pstat['qty'] ?>x</span>
          </div>
        </div>
        <?php $i++; endforeach ?>
      <?php endif ?>
    </div>

  </div>
</div>
