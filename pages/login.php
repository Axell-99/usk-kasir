<?php ?>
<div class="login-wrap">
  <div class="login-box">
    <div class="login-logo">
      <h1>Kasir</h1>
      <p>Sistem Manajemen Kasir</p>
    </div>

    <?php if (!empty($loginError)): ?>
      <div class="alert alert-red"><?= ($loginError) ?></div>
    <?php endif ?>q

    <form method="POST" action="index.php?page=dashboard">
      <input type="hidden" name="action" value="login">
      <div class="form-group">
        <label class="form-label">Username</label>
        <input class="form-input" name="username" placeholder="Masukkan username" required>
      </div>
      <div class="form-group">
        <label class="form-label">Password</label>
        <input class="form-input" name="password" type="password" placeholder="Masukkan password" required>
      </div>
      <button class="btn btn-primary" style="width:100%;padding:12px" type="submit">Login</button>
    </form>
  </div>
</div>
