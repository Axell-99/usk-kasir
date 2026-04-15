<?php
// Halaman Data Pelanggan
$editCust = null;
if (isset($_GET['edit'])) {
    $ceid = (int)$_GET['edit'];
    foreach ($customers as $c) {
        if ($c['id'] === $ceid) { $editCust = $c; break; }
    }
}
$showAddC = isset($_GET['add']);
?>
<div class="page">
  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
    <h2 class="page-title" style="margin-bottom:0">👥 Data Pelanggan</h2>
    <a href="index.php?page=pelanggan&add=1" class="btn btn-primary">+ Tambah Pelanggan</a>
  </div>

  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>No</th>
          <th>Nama</th>
          <th>Alamat</th>
          <th>No. Telepon</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($customers as $i => $c): ?>
        <tr>
          <td style="color:#94a3b8"><?= $i + 1 ?></td>
          <td>
            <?= h($c['nama']) ?>
            <?php if ($c['id'] === 1): ?>
              <span class="badge badge-gray" style="margin-left:6px">default</span>
            <?php endif ?>
          </td>
          <td style="color:#64748b"><?= h($c['alamat']) ?></td>
          <td><?= h($c['telepon']) ?></td>
          <td style="display:flex;gap:12px">
            <a href="index.php?page=pelanggan&edit=<?= $c['id'] ?>" class="btn-link btn-link-blue">Edit</a>
            <?php if ($c['id'] !== 1): ?>
            <form method="POST" style="display:inline" onsubmit="return confirm('Hapus pelanggan ini?')">
              <input type="hidden" name="action" value="hapus_pelanggan">
              <input type="hidden" name="id"     value="<?= $c['id'] ?>">
              <button class="btn-link btn-link-red" type="submit">Hapus</button>
            </form>
            <?php endif ?>
          </td>
        </tr>
        <?php endforeach ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal Tambah / Edit Pelanggan -->
<?php if ($showAddC || $editCust): ?>
<div class="modal-overlay" onclick="location='index.php?page=pelanggan'">
  <div class="modal-box" onclick="event.stopPropagation()">
    <div class="modal-title"><?= $editCust ? '✏️ Edit Pelanggan' : '+ Tambah Pelanggan' ?></div>
    <form method="POST">
      <input type="hidden" name="action" value="<?= $editCust ? 'edit_pelanggan' : 'tambah_pelanggan' ?>">
      <?php if ($editCust): ?>
        <input type="hidden" name="id" value="<?= $editCust['id'] ?>">
      <?php endif ?>
      <div class="form-group">
        <label class="form-label">Nama Pelanggan *</label>
        <input class="form-input" name="nama" value="<?= h($editCust['nama'] ?? '') ?>" placeholder="Nama lengkap" required>
      </div>
      <div class="form-group">
        <label class="form-label">Alamat</label>
        <textarea class="form-input" name="alamat" rows="2" placeholder="Alamat lengkap"><?= h($editCust['alamat'] ?? '') ?></textarea>
      </div>
      <div class="form-group">
        <label class="form-label">Nomor Telepon</label>
        <input class="form-input" name="telepon" value="<?= h($editCust['telepon'] ?? '') ?>" placeholder="08xxxxxxxxxx">
      </div>
      <div style="display:flex;gap:10px">
        <button class="btn btn-primary" style="flex:1" type="submit">Simpan</button>
        <a href="index.php?page=pelanggan" class="btn btn-ghost" style="flex:1;text-align:center">Batal</a>
      </div>
    </form>
  </div>
</div>
<?php endif ?>
