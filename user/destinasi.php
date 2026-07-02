<?php
session_start();
if (!isset($_SESSION['user'])) { header("Location: ../auth/login.php"); exit; }
if (empty($_SESSION['perjalanan'])) { header("Location: rencana.php"); exit; }
include '../config/koneksi.php';

$nama    = $_SESSION['nama'];
$inisial = strtoupper(substr($nama, 0, 1));
$p       = &$_SESSION['perjalanan'];
$hari    = (int)$p['jumlah_hari'];

if (isset($_POST['lanjut'])) {
    $itinerary = [];
    for ($h = 1; $h <= $hari; $h++) {
        $wisata_hari = $_POST['wisata_hari_' . $h] ?? [];
        $paket_hari  = $_POST['paket_hari_' . $h] ?? [];
        foreach (array_slice($wisata_hari, 0, 2) as $urutan => $id_wisata) {
            if ($id_wisata) {
                $itinerary[] = [
                    'hari'      => $h,
                    'id_wisata' => (int)$id_wisata,
                    'id_paket'  => (int)($paket_hari[$urutan] ?? 0),
                    'urutan'    => $urutan + 1
                ];
            }
        }
    }
    if (empty($itinerary)) {
        $error = 'Pilih minimal 1 destinasi wisata.';
    } else {
        $p['itinerary'] = $itinerary;
        header("Location: kendaraan.php");
        exit;
    }
}

$wisata_list = [];
$res = mysqli_query($conn, "SELECT w.id_wisata, w.nama_wisata, w.lokasi, w.gambar, MIN(p.harga) as harga_mulai FROM wisata w LEFT JOIN paket p ON w.id_wisata=p.id_wisata GROUP BY w.id_wisata ORDER BY w.nama_wisata");
while ($r = mysqli_fetch_assoc($res)) $wisata_list[] = $r;

// Ambil semua paket per wisata untuk JS
$paket_per_wisata = [];
$res_paket = mysqli_query($conn, "SELECT id_paket, id_wisata, nama_paket, harga, durasi FROM paket ORDER BY id_wisata, harga ASC");
while ($rp = mysqli_fetch_assoc($res_paket)) {
    $paket_per_wisata[$rp['id_wisata']][] = $rp;
}

// Susun itinerary yang sudah ada per hari
$existing = [];
foreach ($p['itinerary'] as $item) {
    $existing[$item['hari']][$item['urutan'] - 1] = $item['id_wisata'];
}

// Auto-preselect dari rencana.php (klik dari detail wisata)
$preselect_wisata = (int)($p['preselect_wisata'] ?? 0);
$preselect_paket  = (int)($p['preselect_paket'] ?? 0);
if ($preselect_wisata && empty($existing[1][0])) {
    $existing[1][0] = $preselect_wisata;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Pilih Destinasi – TravelFlow</title>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="../assets/css/flow.css" rel="stylesheet">
  <link href="../assets/css/sidebar.css" rel="stylesheet">
</head>
<body>
<nav class="tf-navbar">
  <a href="dashboard.php" class="brand"><div class="brand-icon">✈</div> TravelFlow</a>
  <div class="nav-user"><?= $inisial ?></div>
</nav>
<div class="tf-layout">
  <aside class="tf-sidebar">
    <a href="dashboard.php" class="sidebar-item"><span class="si-icon">⊞</span> Dashboard</a>
    <a href="rencana.php" class="sidebar-item active"><span class="si-icon">✈</span> Buat Perjalanan</a>
    <a href="wisata.php" class="sidebar-item"><span class="si-icon">🏝</span> Daftar Wisata</a>
    <a href="riwayat.php" class="sidebar-item"><span class="si-icon">🕐</span> Riwayat</a>
    <a href="ulasan.php" class="sidebar-item"><span class="si-icon">⭐</span> Ulasan Saya</a>
    <a href="profil.php" class="sidebar-item"><span class="si-icon">👤</span> Profil Saya</a>
    <div class="sidebar-divider"></div>
    <div class="sidebar-logout">
      <a href="../auth/logout.php" class="sidebar-item"><span class="si-icon">⇥</span> Logout</a>
    </div>
  </aside>
  <main class="tf-main">

    <div class="flow-steps">
      <div class="flow-step done"><div class="step-num">✓</div> Rencana</div>
      <div class="flow-step-line done"></div>
      <div class="flow-step active"><div class="step-num">2</div> Destinasi</div>
      <div class="flow-step-line"></div>
      <div class="flow-step"><div class="step-num">3</div> Kendaraan</div>
      <div class="flow-step-line"></div>
      <div class="flow-step"><div class="step-num">4</div> Penginapan</div>
      <div class="flow-step-line"></div>
      <div class="flow-step"><div class="step-num">5</div> Penjemputan</div>
      <div class="flow-step-line"></div>
      <div class="flow-step"><div class="step-num">6</div> Ringkasan</div>
    </div>

    <div class="flow-header">
      <img class="flow-header-img" src="../assets/img/wisata/bag.png" alt="" onerror="this.style.display='none'">
      <div class="flow-header-bg"></div>
      <div class="flow-header-content">
        <div class="flow-header-badge">🏕 Langkah 2 dari 6</div>
        <h1>Pilih Destinasi Wisata</h1>
        <p>Perjalanan <?= $hari ?> hari — maksimal 2 destinasi per hari (total maks <?= $hari * 2 ?> destinasi)</p>
      </div>
    </div>

    <?php if (!empty($error)): ?>
    <div class="flow-alert warning"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" id="formDestinasi">
      <?php for ($h = 1; $h <= $hari; $h++):
        $tgl_hari = date('d M Y', strtotime($p['tanggal_berangkat'] . ' +' . ($h-1) . ' days'));
        $sel0 = $existing[$h][0] ?? 0;
        $sel1 = $existing[$h][1] ?? 0;
      ?>
      <div class="flow-card">
        <div class="dest-day-header">
          <div class="dest-day-title">
            <span class="dest-day-icon">📅</span>
            <span>Hari <?= $h ?> <span class="dest-day-date">— <?= $tgl_hari ?></span></span>
          </div>
          <span class="dest-day-badge">Maks 2 destinasi/hari</span>
        </div>

        <div style="margin-bottom:20px;">
          <div class="dest-slot-label"><span class="dest-slot-num">1</span> Destinasi Utama</div>
          <div class="wisata-select-grid" id="grid_h<?= $h ?>_0">
            <?php foreach ($wisata_list as $w): ?>
            <label class="wisata-select-card <?= $sel0 == $w['id_wisata'] ? 'selected' : '' ?>"
              onclick="selectWisata(this, <?= $h ?>, 0)">
              <input type="radio" name="wisata_hari_<?= $h ?>[]" value="<?= $w['id_wisata'] ?>"
                <?= $sel0 == $w['id_wisata'] ? 'checked' : '' ?>>
              <div class="wsc-check">✓</div>
              <?php if (!empty($w['gambar'])): ?>
                <div class="wsc-img-wrap">
                  <img class="wsc-img" src="../assets/img/wisata/<?= htmlspecialchars($w['gambar']) ?>" alt="<?= htmlspecialchars($w['nama_wisata']) ?>">
                  <div class="wsc-overlay"></div>
                </div>
              <?php else: ?>
                <div class="wsc-img-wrap"><div class="wsc-img-placeholder">🌏</div></div>
              <?php endif; ?>
              <div class="wsc-body">
                <div class="wsc-name"><?= htmlspecialchars($w['nama_wisata']) ?></div>
                <div class="wsc-lokasi">📍 <?= htmlspecialchars($w['lokasi'] ?? 'Yogyakarta') ?></div>
                <div class="wsc-price-badge">Mulai Rp <?= number_format($w['harga_mulai'] ?? 0, 0, ',', '.') ?></div>
              </div>
            </label>
            <?php endforeach; ?>
          </div>
          <!-- Paket dropdown untuk destinasi 1 -->
          <div id="paket_wrap_h<?= $h ?>_0" style="margin-top:12px;<?= $sel0 ? '' : 'display:none' ?>">
            <label class="form-label">Pilih Paket untuk Destinasi 1</label>
            <select class="tf-select" name="paket_hari_<?= $h ?>[]" id="paket_h<?= $h ?>_0" required>
              <option value="">-- Pilih Paket --</option>
              <?php if ($sel0 && !empty($paket_per_wisata[$sel0])): foreach ($paket_per_wisata[$sel0] as $pk): ?>
              <option value="<?= $pk['id_paket'] ?>" <?= ($p['itinerary'][0]['id_paket'] ?? 0) == $pk['id_paket'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($pk['nama_paket']) ?> — Rp <?= number_format($pk['harga'], 0, ',', '.') ?>/orang
              </option>
              <?php endforeach; endif; ?>
            </select>
          </div>
        </div>

        <div>
          <div class="dest-slot-label"><span class="dest-slot-num" style="background:#94a3b8;">2</span> Destinasi Tambahan <span class="dest-slot-opt">opsional</span></div>
          <div class="wisata-select-grid" id="grid_h<?= $h ?>_1">
            <label class="wisata-select-card <?= !$sel0 ? 'disabled' : '' ?>"
              onclick="clearDestinasi(<?= $h ?>, 1)"
              style="background:#f8fafc;border:2px dashed var(--border);">
              <div class="wsc-body" style="position:relative;inset:auto;padding:0;height:100%;display:flex;align-items:center;justify-content:center;flex-direction:column;gap:6px;">
                <div style="font-size:2rem;">➕</div>
                <div style="font-size:.8rem;color:var(--text-muted);font-weight:700;">Tidak ada destinasi ke-2</div>
              </div>
            </label>
            <?php foreach ($wisata_list as $w): ?>
            <label class="wisata-select-card <?= $sel1 == $w['id_wisata'] ? 'selected' : '' ?> <?= !$sel0 ? 'disabled' : '' ?>"
              id="dest2_h<?= $h ?>_<?= $w['id_wisata'] ?>"
              onclick="selectWisata(this, <?= $h ?>, 1)">
              <input type="radio" name="wisata_hari_<?= $h ?>[]" value="<?= $w['id_wisata'] ?>"
                <?= $sel1 == $w['id_wisata'] ? 'checked' : '' ?>>
              <div class="wsc-check">✓</div>
              <?php if (!empty($w['gambar'])): ?>
                <div class="wsc-img-wrap">
                  <img class="wsc-img" src="../assets/img/wisata/<?= htmlspecialchars($w['gambar']) ?>" alt="<?= htmlspecialchars($w['nama_wisata']) ?>">
                  <div class="wsc-overlay"></div>
                </div>
              <?php else: ?>
                <div class="wsc-img-wrap"><div class="wsc-img-placeholder">🌏</div></div>
              <?php endif; ?>
              <div class="wsc-body">
                <div class="wsc-name"><?= htmlspecialchars($w['nama_wisata']) ?></div>
                <div class="wsc-lokasi">📍 <?= htmlspecialchars($w['lokasi'] ?? 'Yogyakarta') ?></div>
                <div class="wsc-price-badge">Mulai Rp <?= number_format($w['harga_mulai'] ?? 0, 0, ',', '.') ?></div>
              </div>
            </label>
            <?php endforeach; ?>
          </div>
          <!-- Paket dropdown untuk destinasi 2 -->
          <div id="paket_wrap_h<?= $h ?>_1" style="margin-top:12px;display:none">
            <label class="form-label">Pilih Paket untuk Destinasi 2</label>
            <select class="tf-select" name="paket_hari_<?= $h ?>[]" id="paket_h<?= $h ?>_1">
              <option value="">-- Pilih Paket --</option>
            </select>
          </div>
        </div>
      </div>
      <?php endfor; ?>

      <!-- Itinerary Preview -->
      <div class="flow-card">
        <div class="flow-card-title">🗺 Preview Itinerary</div>
        <div class="itinerary-preview" id="itineraryPreview">
          <?php for ($h = 1; $h <= $hari; $h++): ?>
          <div class="itinerary-day" id="preview_hari_<?= $h ?>">
            <div class="itinerary-day-label">Hari <?= $h ?> — <?= date('d M Y', strtotime($p['tanggal_berangkat'] . ' +' . ($h-1) . ' days')) ?></div>
            <div class="itinerary-item"><div class="itinerary-dot"></div><span id="preview_h<?= $h ?>_0" class="itinerary-empty">Belum dipilih</span></div>
            <div class="itinerary-item" id="preview_row_h<?= $h ?>_1" style="display:none"><div class="itinerary-dot"></div><span id="preview_h<?= $h ?>_1"></span></div>
          </div>
          <?php endfor; ?>
        </div>
      </div>

      <button type="submit" name="lanjut" class="btn-next">Lanjut: Pilih Kendaraan →</button>
      <a href="rencana.php" class="btn-back">← Kembali</a>
    </form>

  </main>
</div>
<script>
const wisataNames = <?= json_encode(array_column($wisata_list, 'nama_wisata', 'id_wisata')) ?>;
const paketData   = <?= json_encode($paket_per_wisata) ?>;

function renderPaket(hari, slot, idWisata) {
  const wrap = document.getElementById('paket_wrap_h' + hari + '_' + slot);
  const sel  = document.getElementById('paket_h' + hari + '_' + slot);
  if (!wrap || !sel) return;
  sel.innerHTML = '<option value="">-- Pilih Paket --</option>';
  const pakets = paketData[idWisata] || [];
  pakets.forEach(p => {
    const opt = document.createElement('option');
    opt.value = p.id_paket;
    opt.textContent = p.nama_paket + ' — Rp ' + parseInt(p.harga).toLocaleString('id-ID') + '/orang';
    sel.appendChild(opt);
  });
  // Auto-select paket pertama
  if (pakets.length > 0) sel.value = pakets[0].id_paket;
  wrap.style.display = 'block';
}

function selectWisata(el, hari, slot) {
  const grid = document.getElementById('grid_h' + hari + '_' + slot);
  grid.querySelectorAll('.wisata-select-card').forEach(c => {
    c.classList.remove('selected');
    const inp = c.querySelector('input[type=radio]');
    if (inp) inp.checked = false;
  });
  el.classList.add('selected');
  const inp = el.querySelector('input[type=radio]');
  if (inp) inp.checked = true;
  const id = inp ? inp.value : 0;

  // Update preview
  const previewEl = document.getElementById('preview_h' + hari + '_' + slot);
  if (previewEl) {
    previewEl.textContent = wisataNames[id] || '';
    previewEl.classList.remove('itinerary-empty');
  }
  const rowEl = document.getElementById('preview_row_h' + hari + '_' + slot);
  if (rowEl) rowEl.style.display = 'flex';

  // Render paket dropdown
  renderPaket(hari, slot, id);

  // Enable destinasi 2 setelah destinasi 1 dipilih
  if (slot === 0) {
    const grid2 = document.getElementById('grid_h' + hari + '_1');
    if (grid2) grid2.querySelectorAll('.wisata-select-card').forEach(c => c.classList.remove('disabled'));
  }
}

function clearDestinasi(hari, slot) {
  const grid = document.getElementById('grid_h' + hari + '_' + slot);
  grid.querySelectorAll('.wisata-select-card').forEach(c => {
    c.classList.remove('selected');
    const inp = c.querySelector('input[type=radio]');
    if (inp) inp.checked = false;
  });
  const previewEl = document.getElementById('preview_h' + hari + '_' + slot);
  if (previewEl) { previewEl.textContent = 'Belum dipilih'; previewEl.classList.add('itinerary-empty'); }
  const rowEl = document.getElementById('preview_row_h' + hari + '_' + slot);
  if (rowEl) rowEl.style.display = 'none';
  const wrap = document.getElementById('paket_wrap_h' + hari + '_' + slot);
  if (wrap) wrap.style.display = 'none';
}

// Init preview & paket dari existing data
<?php for ($h = 1; $h <= $hari; $h++):
  $sel0 = $existing[$h][0] ?? 0;
  $sel1 = $existing[$h][1] ?? 0;
?>
<?php if ($sel0): ?>
(function(){
  const el = document.getElementById('preview_h<?= $h ?>_0');
  if (el) { el.textContent = wisataNames[<?= $sel0 ?>] || ''; el.classList.remove('itinerary-empty'); }
  const grid2 = document.getElementById('grid_h<?= $h ?>_1');
  if (grid2) grid2.querySelectorAll('.wisata-select-card').forEach(c => c.classList.remove('disabled'));
  renderPaket(<?= $h ?>, 0, <?= $sel0 ?>);
})();
<?php endif; ?>
<?php if ($sel1): ?>
(function(){
  const el = document.getElementById('preview_h<?= $h ?>_1');
  if (el) el.textContent = wisataNames[<?= $sel1 ?>] || '';
  const row = document.getElementById('preview_row_h<?= $h ?>_1');
  if (row) row.style.display = 'flex';
  renderPaket(<?= $h ?>, 1, <?= $sel1 ?>);
})();
<?php endif; ?>
<?php endfor; ?>
</script>
</body>
</html>
