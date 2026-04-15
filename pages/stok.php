<?php
// Halaman Stok Barang
$stokEdit = null;
if (isset($_GET['stok'])) {
    $sid = (int)$_GET['stok'];
    foreach ($products as $p) {
        if ($p['id'] === $sid) { $stokEdit = $p; break; }
    }
}
?>
<div class="page">
  <h2 class="page-title">📋 Stok Barang</h2>
  <p class="subtitle">Pantau dan kelola stok produk</p>

  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Nama Produk</th>
          <th>Harga</th>
          <th>Stok</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($products as $p): ?>
        <tr>
          <td><?= h($p['nama']) ?></td>
          <td><?= formatRp($p['harga']) ?></td>
          <td style="font-weight:700;font-size:1.05rem"><?= $p['stok'] ?></td>
          <td>
            <?php if ($p['stok'] == 0): ?>
              <span class="badge badge-red">Habis</span>
            <?php elseif ($p['stok'] < 10): ?>
              <span class="badge badge-yellow">Menipis</span>
            <?php else: ?>
              <span class="badge badge-green">Tersedia</span>
            <?php endif ?>
          </td>
          <td>
            <a href="index.php?page=stok&stok=<?= $p['id'] ?>" class="btn-link btn-link-blue">+ Tambah Stok</a>
          </td>
        </tr>
        <?php endforeach ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal Tambah Stok -->
<?php if ($stokEdit): ?>
<div class="modal-overlay" onclick="location='index.php?page=stok'">
  <div class="modal-box" onclick="event.stopPropagation()">
    <div class="modal-title"> Tambah Stok</div>
    <p style="font-size:.9rem;color:#475569;margin-bottom:4px"><?= h($stokEdit['nama']) ?></p>
    <p style="font-size:.8rem;color:#94a3b8;margin-bottom:16px">
      Stok saat ini: <b style="color:#334155"><?= $stokEdit['stok'] ?> unit</b>
    </p>
    <form method="POST">
      <input type="hidden" name="action" value="tambah_stok">
      <input type="hidden" name="id"     value="<?= $stokEdit['id'] ?>">
      <div class="form-group">
        <label class="form-label">Jumlah Penambahan</label>
        <input class="form-input" name="tambah" type="number" min="1" placeholder="Masukkan jumlah..." required>
      </div>
      <div style="display:flex;gap:10px">
        <button class="btn btn-primary" style="flex:1" type="submit">Simpan</button>
        <a href="index.php?page=stok" class="btn btn-ghost" style="flex:1;text-align:center">Batal</a>
      </div>
    </form>
  </div>
</div>
<?php endif ?>
