<?php
// Halaman POS / Kasir
$search        = $_GET['q'] ?? '';
$filteredProds = array_filter($products, fn($p) => stripos($p['nama'], $search) !== false);
?>
<div style="display:flex;flex-direction:column;height:100%;overflow:hidden">
  <div style="padding:16px 16px 0;font-size:1.1rem;font-weight:700"> Kasir</div>
  <div class="pos-wrap" style="overflow:auto">

    <!-- KIRI: Daftar Produk -->
    <div class="pos-left">
      <form method="GET" class="pos-search" style="display:flex;gap:8px">
        <input type="hidden" name="page" value="penjualan">
        <input class="form-input" name="q" value="<?= h($search) ?>" placeholder="Cari produk...">
      </form>
      <div class="pos-grid">
        <?php foreach ($filteredProds as $p): ?>
        <form method="POST" style="display:contents">
          <input type="hidden" name="action" value="add_cart">
          <input type="hidden" name="pid" value="<?= $p['id'] ?>">
          <button type="submit" class="prod-card <?= $p['stok'] == 0 ? 'stok0' : '' ?>">
            <div class="pname"><?= h($p['nama']) ?></div>
            <div class="pprice"><?= formatRp($p['harga']) ?></div>
            <div class="pstock">Stok: <?= $p['stok'] ?></div>
          </button>
        </form>
        <?php endforeach ?>
      </div>
    </div>

    <!-- KANAN: Keranjang -->
    <div class="pos-right">
      <div class="cart-box">
        <div class="cart-header" style="display:flex;justify-content:space-between;align-items:center">
          <span> Keranjang (<?= count($cart) ?>)</span>
          <?php if (!empty($cart)): ?>
          <form method="POST" style="display:inline">
            <input type="hidden" name="action" value="clear_cart">
            <button class="btn-link btn-link-red" style="font-size:.75rem" type="submit">Kosongkan</button>
          </form>
          <?php endif ?>
        </div>

        <div class="cart-items">
          <?php if (empty($cart)): ?>
            <div class="text-center" style="padding:40px;color:#94a3b8;font-size:.85rem">
              Belum ada item di keranjang
            </div>
          <?php else: ?>
            <?php foreach ($cart as $item): ?>
            <div class="cart-item">
              <div class="iname"><?= h($item['nama']) ?></div>
              <div class="iqty">
                <form method="POST" style="display:inline">
                  <input type="hidden" name="action" value="update_cart">
                  <input type="hidden" name="pid" value="<?= $item['id'] ?>">
                  <input type="hidden" name="qty" value="<?= $item['qty'] - 1 ?>">
                  <button class="qty-btn" type="submit">−</button>
                </form>
                <span class="qty-val"><?= $item['qty'] ?></span>
                <form method="POST" style="display:inline">
                  <input type="hidden" name="action" value="update_cart">
                  <input type="hidden" name="pid" value="<?= $item['id'] ?>">
                  <input type="hidden" name="qty" value="<?= $item['qty'] + 1 ?>">
                  <button class="qty-btn" type="submit">+</button>
                </form>
              </div>
              <div class="isubtotal"><?= formatRp($item['harga'] * $item['qty']) ?></div>
            </div>
            <?php endforeach ?>
          <?php endif ?>
        </div>

        <div class="cart-footer">
          <div class="total-row">
            <span><?= count($cart) ?> item</span>
            <span><?= formatRp($cartTotal) ?></span>
          </div>
          <div class="total-row grand">
            <span>Total</span>
            <span><?= formatRp($cartTotal) ?></span>
          </div>
        </div>

        <?php if (!empty($cart)): ?>
        <form method="POST" class="checkout-form">
          <input type="hidden" name="action" value="checkout">
          <div>
            <label class="form-label">Pelanggan</label>
            <select name="customer_id" class="form-input">
              <?php foreach ($customers as $c): ?>
                <option value="<?= $c['id'] ?>"><?= h($c['nama']) ?></option>
              <?php endforeach ?>
            </select>
          </div>
          <div>
            <label class="form-label">Jumlah Bayar (Rp)</label>
            <input class="form-input" type="number" name="bayar" min="<?= $cartTotal ?>" placeholder="<?= $cartTotal ?>" required>
          </div>
          <button class="btn btn-primary" type="submit" style="width:100%">✅ Bayar</button>
        </form>
        <?php endif ?>
      </div>
    </div>

  </div>
</div>
