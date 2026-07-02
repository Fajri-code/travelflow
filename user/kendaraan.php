<?php
session_start();
if (!isset($_SESSION['user'])) { header("Location: ../auth/login.php"); exit; }
if (empty($_SESSION['perjalanan'])) { header("Location: rencana.php"); exit; }
if (empty($_SESSION['perjalanan']['itinerary'])) { header("Location: destinasi.php"); exit; }
include '../config/koneksi.php';

$nama    = $_SESSION['nama'];
$inisial = strtoupper(substr($nama, 0, 1));
$p       = &$_SESSION['perjalanan'];

if (isset($_POST['lanjut'])) {
    $id_kendaraan   = (int)$_POST['id_kendaraan'];
    $jumlah_peserta = max(1, (int)$_POST['jumlah_peserta']);
    if (!$id_kendaraan) {
        $error = 'Pilih kendaraan terlebih dahulu.';
    } else {
        $p['id_kendaraan']   = $id_kendaraan;
        $p['jumlah_peserta'] = $jumlah_peserta;
        // Skip penginapan jika 1 hari
        header("Location: " . ($p['jumlah_malam'] > 0 ? "penginapan.php" : "penjemputan.php"));
        exit;
    }
}

$kendaraan_list = [];
$res = mysqli_query($conn, "SELECT * FROM kendaraan ORDER BY harga ASC");
while ($r = mysqli_fetch_assoc($res)) $kendaraan_list[] = $r;

function kendaraan_icon($nama) {
    $n = strtolower($nama);
    if (str_contains($n, 'motor')) return '🛵';
    if (str_contains($n, 'bus medium')) return '🚌';
    if (str_contains($n, 'bus')) return '🚍';
    if (str_contains($n, 'minibus')) return '🚐';
    return '🚗';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Pilih Kendaraan – TravelFlow</title>
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
      <div class="flow-step done"><div class="step-num">✓</div> Destinasi</div>
      <div class="flow-step-line done"></div>
      <div class="flow-step active"><div class="step-num">3</div> Kendaraan</div>
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
        <div class="flow-header-badge">🚗 Langkah 3 dari 6</div>
        <h1>Pilih Kendaraan</h1>
        <p>Semua kendaraan sudah termasuk sopir profesional berpengalaman</p>
      </div>
    </div>

    <?php if (!empty($error)): ?>
    <div class="flow-alert warning"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="flow-card">
        <div class="flow-card-title">👥 Jumlah Peserta</div>
        <div class="form-row single">
          <div class="form-group">
            <label class="form-label">Berapa orang yang ikut perjalanan ini?</label>
            <input type="number" class="tf-input" name="jumlah_peserta" id="jumlah_peserta"
              min="1" max="50" value="<?= $p['jumlah_peserta'] ?? 1 ?>"
              oninput="rekomendasiKendaraan(this.value)" required>
            <span class="form-hint" id="rekomendasi_hint">💡 Masukkan jumlah peserta untuk rekomendasi kendaraan</span>
          </div>
        </div>
      </div>

      <div class="flow-card">
        <div class="flow-card-title">🚗 Pilih Kendaraan</div>
        <div class="kendaraan-grid">
          <?php foreach ($kendaraan_list as $k): ?>
          <label class="kendaraan-card <?= $p['id_kendaraan'] == $k['id_kendaraan'] ? 'selected' : '' ?>"
            onclick="selectKendaraan(this)" id="kcard_<?= $k['id_kendaraan'] ?>">
            <input type="radio" name="id_kendaraan" value="<?= $k['id_kendaraan'] ?>"
              <?= $p['id_kendaraan'] == $k['id_kendaraan'] ? 'checked' : '' ?>>
            <div class="kendaraan-icon"><?= kendaraan_icon($k['nama_kendaraan']) ?></div>
            <div class="kendaraan-info">
              <div class="kendaraan-nama"><?= htmlspecialchars($k['nama_kendaraan']) ?></div>
              <div class="kendaraan-kapasitas">Kapasitas: <?= htmlspecialchars($k['kapasitas'] ?? '-') ?> orang</div>
              <div class="kendaraan-harga">Rp <?= number_format($k['harga'], 0, ',', '.') ?> / perjalanan</div>
            </div>
            <div class="kendaraan-check">✓</div>
          </label>
          <?php endforeach; ?>
        </div>
      </div>

      <button type="submit" name="lanjut" class="btn-next">
        Lanjut: <?= $p['jumlah_malam'] > 0 ? 'Pilih Penginapan' : 'Pilih Penjemputan' ?> →
      </button>
      <a href="destinasi.php" class="btn-back">← Kembali</a>
    </form>

  </main>
</div>
<script>
const kendaraanData = <?= json_encode(array_map(fn($k) => [
  'id' => $k['id_kendaraan'],
  'nama' => $k['nama_kendaraan'],
  'kapasitas' => $k['kapasitas'] ?? 0
], $kendaraan_list)) ?>;

function selectKendaraan(el) {
  document.querySelectorAll('.kendaraan-card').forEach(c => c.classList.remove('selected'));
  el.classList.add('selected');
  el.querySelector('input').checked = true;
}

function rekomendasiKendaraan(jumlah) {
  jumlah = parseInt(jumlah) || 0;
  if (!jumlah) return;
  let best = null;
  kendaraanData.forEach(k => {
    const kap = parseInt(k.kapasitas) || 99;
    if (kap >= jumlah && (!best || kap < (parseInt(best.kapasitas) || 99))) best = k;
  });
  if (best) {
    document.querySelectorAll('.kendaraan-card').forEach(c => c.classList.remove('selected'));
    const card = document.getElementById('kcard_' + best.id);
    if (card) {
      card.classList.add('selected');
      card.querySelector('input').checked = true;
    }
    document.getElementById('rekomendasi_hint').textContent = '✅ Rekomendasi: ' + best.nama + ' (kapasitas ' + best.kapasitas + ' orang)';
  }
}

// Init rekomendasi
const initPeserta = document.getElementById('jumlah_peserta').value;
if (initPeserta > 0) rekomendasiKendaraan(initPeserta);
</script>
</body>
</html>
