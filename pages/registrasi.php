<?php
if (!isAdmin()) {
    header('Location: index.php?page=dashboard');
    exit;
}
$showUserForm = isset($_GET['add']);
?>
<div class="page">
  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
    <h2 class="page-title" style="margin-bottom:0"> Kelola User</h2>
    <a href="index.php?page=registrasi&add=1" class="btn btn-primary">+ Tambah User</a>
  </div>

  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>No</th>
          <th>Nama</th>
          <th>Username</th>
          <th>Role</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($users as $i => $u): ?>
        <?php
          $admins = array_filter($users, fn($x) => $x['role'] === 'administrator');
          $canDel = !($u['role'] === 'administrator' && count($admins) <= 1);
        ?>
        <tr>
          <td style="color:#94a3b8"><?= $i + 1 ?></td>
          <td style="font-weight:500"><?= h($u['nama']) ?></td>
          <td style="font-family:monospace;font-size:.8rem;color:#64748b"><?= h($u['username']) ?></td>
          <td>
            <span class="badge <?= $u['role'] === 'administrator' ? 'badge-blue' : 'badge-gray' ?>">
              <?= h($u['role']) ?>
            </span>
          </td>
          <td>
            <?php if ($canDel): ?>
            <a href="index.php?page=registrasi&edit=<?= $u['id'] ?>" class="btn-link btn-link-blue">Edit</a>
            <form method="POST" style="display:inline" onsubmit="return confirm('Hapus user ini?')">
              <input type="hidden" name="action" value="hapus_user">
              <input type="hidden" name="id"     value="<?= $u['id'] ?>">
              <button class="btn-link btn-link-red" type="submit">Hapus</button>
            </form>
            <?php else: ?>
              <span style="color:#94a3b8;font-size:.8rem">—</span>
            <?php endif ?>
          </td>
        </tr>
        <?php endforeach ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal Tambah User -->
<?php if ($showUserForm): ?>
<div class="modal-overlay" onclick="location='index.php?page=registrasi'">
  <div class="modal-box" onclick="event.stopPropagation()">
    <div class="modal-title">+ Tambah User Baru</div>
    <form method="POST">
      <input type="hidden" name="action" value="tambah_user">
      <div class="form-group">
        <label class="form-label">Nama Lengkap </label>
        <input class="form-input" name="nama" placeholder="Nama lengkap" required>
      </div>
      <div class="form-group">
        <label class="form-label">Username </label>
        <input class="form-input" name="username" placeholder="Username unik" required>
      </div>
      <div class="form-group">
        <label class="form-label">Password </label>
        <input class="form-input" type="password" name="password" placeholder="Minimal 6 karakter" required>
      </div>
      <div class="form-group">
        <label class="form-label">Role</label>
        <select class="form-input" name="role">
          <option value="petugas">Petugas</option>
          <option value="administrator">Administrator</option>
        </select>
      </div>
      <div style="display:flex;gap:10px">
        <button class="btn btn-primary" style="flex:1" type="submit">Simpan</button>
        <a href="index.php?page=registrasi" class="btn btn-ghost" style="flex:1;text-align:center">Batal</a>
      </div>
    </form>
  </div>
</div>
<?php endif ?>
