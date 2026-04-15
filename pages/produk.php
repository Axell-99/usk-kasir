<?php
// Halaman Data Produk
$editProd = null;
if (isset($_GET['edit'])) {
    $eid = (int)$_GET['edit'];
    foreach ($products as $p) {
        if ($p['id'] === $eid) { $editProd = $p; break; }
    }
}
$showAdd = isset($_GET['add']);
?>
<div class="page">
  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
    <h2 class="page-title" style="margin-bottom:0">📦 Data Produk</h2>
    <?php if (isAdmin()): ?>
      <a href="index.php?page=produk&add=1" class="btn btn-primary">+ Tambah Produk</a>
    <?php endif ?>
  </div>

  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Nama Produk</th>
          <th>Harga</th>
          <th>Stok</th>
          <th>Status</th>
          <?php if (isAdmin()): ?><th>Aksi</th><?php endif ?>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($products as $p): ?>
        <tr>
          <td><?= h($p['nama']) ?></td>
          <td><?= formatRp($p['harga']) ?></td>
          <td style="font-weight:700"><?= $p['stok'] ?></td>
          <td>
            <?php if ($p['stok'] == 0): ?>
              <span class="badge badge-red">Habis</span>
            <?php elseif ($p['stok'] < 10): ?>
              <span class="badge badge-yellow">Menipis</span>
            <?php else: ?>
              <span class="badge badge-green">Tersedia</span>
            <?php endif ?>
          </td>
          <?php if (isAdmin()): ?>
          <td style="display:flex;gap:12px">
            <a href="index.php?page=produk&edit=<?= $p['id'] ?>" class="btn-link btn-link-blue">Edit</a>
            <form method="POST" style="display:inline" onsubmit="return confirm('Hapus produk ini?')">
              <input type="hidden" name="action" value="hapus_produk">
              <input type="hidden" name="id"     value="<?= $p['id'] ?>">
              <button class="btn-link btn-link-red" type="submit">Hapus</button>
            </form>
          </td>
          <?php endif ?>
        </tr>
        <?php endforeach ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal Tambah / Edit Produk -->
<?php if (isAdmin() && ($showAdd || $editProd)): ?>
<div class="modal-overlay" onclick="location='index.php?page=produk'">
  <div class="modal-box" onclick="event.stopPropagation()">
    <div class="modal-title"><?= $editProd ? ' Edit Produk' : ' Tambah Produk' ?></div>
    <form method="POST">
      <input type="hidden" name="action" value="<?= $editProd ? 'edit_produk' : 'tambah_produk' ?>">
      <?php if ($editProd): ?>
        <input type="hidden" name="id" value="<?= $editProd['id'] ?>">
      <?php endif ?>
      <div class="form-group">
        <label class="form-label">Nama Produk *</label>
        <input class="form-input" name="nama" value="<?= h($editProd['nama'] ?? '') ?>" placeholder="Nama produk" required>
      </div>
      <div class="form-group">
        <label class="form-label">Harga (Rp) *</label>
        <input class="form-input" name="harga" type="number" value="<?= $editProd['harga'] ?? '' ?>" placeholder="0" min="0" required>
      </div>
      <div class="form-group">
        <label class="form-label">Stok Awal</label>
        <input class="form-input" name="stok" type="number" value="<?= $editProd['stok'] ?? 0 ?>" min="0">
      </div>
      <div style="display:flex;gap:10px;margin-top:4px">
        <button class="btn btn-primary" style="flex:1" type="submit">Simpan</button>
        <a href="index.php?page=produk" class="btn btn-ghost" style="flex:1;text-align:center">Batal</a>
      </div>
    </form>
  </div>
</div>
<?php endif ?>
