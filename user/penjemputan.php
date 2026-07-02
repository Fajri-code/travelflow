<?php
session_start();
if (!isset($_SESSION['user'])) { header("Location: ../auth/login.php"); exit; }
if (empty($_SESSION['perjalanan'])) { header("Location: rencana.php"); exit; }
if (empty($_SESSION['perjalanan']['id_kendaraan'])) { header("Location: kendaraan.php"); exit; }
include '../config/koneksi.php';

$nama    = $_SESSION['nama'];
$inisial = strtoupper(substr($nama, 0, 1));
$p       = &$_SESSION['perjalanan'];

if (isset($_POST['lanjut'])) {
    $titik = $_POST['titik_jemput'] ?? '';
    $detail = trim($_POST['detail_jemput'] ?? '');
    if (!$titik) {
        $error = 'Pilih titik penjemputan terlebih dahulu.';
    } else {
        $p['titik_jemput']  = $titik;
        $p['detail_jemput'] = $detail;
        header("Location: ringkasan.php");
        exit;
    }
}

// Jika sudah pilih hotel, titik jemput otomatis dari hotel
$ada_hotel = !empty($p['id_hotel']);
if ($ada_hotel) {
    $hotel_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama_hotel FROM hotel WHERE id_hotel=" . (int)$p['id_hotel']));
    $nama_hotel_jemput = $hotel_data['nama_hotel'] ?? 'Hotel';
}

$titik_opts = $ada_hotel
  ? [
      'Hotel'        => ['🏨', 'Dari Hotel', $nama_hotel_jemput ?? 'Hotel yang dipilih'],
      'Alamat Sendiri' => ['📍', 'Alamat Lain', 'Masukkan alamat lengkap di bawah'],
    ]
  : [
      'Bandara YIA'         => ['✈️', 'Bandara YIA', 'Yogyakarta International Airport'],
      'Stasiun Tugu'        => ['🚂', 'Stasiun Tugu', 'Stasiun utama Yogyakarta'],
      'Stasiun Lempuyangan' => ['🚃', 'Stasiun Lempuyangan', 'Stasiun alternatif Yogyakarta'],
      'Terminal Giwangan'   => ['🚌', 'Terminal Giwangan', 'Terminal bus utama Yogyakarta'],
      'Alamat Sendiri'      => ['📍', 'Alamat Sendiri', 'Masukkan alamat lengkap di bawah'],
    ];

// Auto-select hotel jika ada hotel dan belum ada titik jemput
if ($ada_hotel && empty($p['titik_jemput'])) {
    $p['titik_jemput'] = 'Hotel';
    $p['detail_jemput'] = $nama_hotel_jemput ?? '';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Titik Penjemputan – TravelFlow</title>
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
      <div class="flow-step done"><div class="step-num">✓</div> Kendaraan</div>
      <div class="flow-step-line done"></div>
      <div class="flow-step done"><div class="step-num">✓</div> Penginapan</div>
      <div class="flow-step-line done"></div>
      <div class="flow-step active"><div class="step-num">5</div> Penjemputan</div>
      <div class="flow-step-line"></div>
      <div class="flow-step"><div class="step-num">6</div> Ringkasan</div>
    </div>

    <div class="flow-header">
      <img class="flow-header-img" src="../assets/img/wisata/bag.png" alt="" onerror="this.style.display='none'">
      <div class="flow-header-bg"></div>
      <div class="flow-header-content">
        <div class="flow-header-badge">📍 Langkah 5 dari 6</div>
        <h1>Titik Penjemputan</h1>
        <?php if ($ada_hotel): ?>
        <p>Anda sudah memilih hotel — sopir akan menjemput dari <strong style="color:#fed7aa"><?= htmlspecialchars($nama_hotel_jemput) ?></strong>. Bisa diganti ke alamat lain jika perlu.</p>
        <?php else: ?>
        <p>Sopir akan menjemput Anda di lokasi yang dipilih pada hari keberangkatan</p>
        <?php endif; ?>
      </div>
    </div>

    <?php if (!empty($error)): ?>
    <div class="flow-alert warning"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="flow-card">
        <div class="flow-card-title">📍 Pilih Titik Jemput</div>
        <div class="jemput-grid">
          <?php foreach ($titik_opts as $val => [$icon, $label, $sub]): ?>
          <label class="jemput-card <?= ($p['titik_jemput'] ?? '') === $val ? 'selected' : '' ?>"
            onclick="selectJemput(this, '<?= $val ?>')">
            <input type="radio" name="titik_jemput" value="<?= htmlspecialchars($val) ?>"
              <?= ($p['titik_jemput'] ?? '') === $val ? 'checked' : '' ?>>
            <div class="jemput-icon"><?= $icon ?></div>
            <div class="jemput-label"><?= $label ?></div>
            <div class="jemput-sub"><?= $sub ?></div>
          </label>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="flow-card" id="detailCard" style="<?= in_array($p['titik_jemput'] ?? '', ['Hotel','Alamat Sendiri']) ? '' : 'display:none' ?>">
        <div class="flow-card-title">📝 Detail Lokasi</div>
        <div class="form-row single">
          <div class="form-group">
            <label class="form-label" id="detailLabel">Nama Hotel / Alamat Lengkap</label>
            <input type="text" class="tf-input" name="detail_jemput" id="detail_jemput"
              placeholder="Contoh: Hotel Tentrem, Jl. AM Sangaji No.72A"
              value="<?= htmlspecialchars($p['detail_jemput'] ?? '') ?>">
            <span class="form-hint">Isi dengan detail agar sopir mudah menemukan lokasi Anda</span>
          </div>
        </div>
      </div>

      <button type="submit" name="lanjut" class="btn-next">Lanjut: Lihat Ringkasan →</button>
      <a href="<?= $p['jumlah_malam'] > 0 ? 'penginapan.php' : 'kendaraan.php' ?>" class="btn-back">← Kembali</a>
    </form>

  </main>
</div>
<script>
function selectJemput(el, val) {
  document.querySelectorAll('.jemput-card').forEach(c => c.classList.remove('selected'));
  el.classList.add('selected');
  el.querySelector('input').checked = true;
  const card = document.getElementById('detailCard');
  const lbl  = document.getElementById('detailLabel');
  if (val === 'Hotel') {
    card.style.display = 'block';
    lbl.textContent = 'Nama Hotel';
  } else if (val === 'Alamat Sendiri') {
    card.style.display = 'block';
    lbl.textContent = 'Alamat Lengkap';
  } else {
    card.style.display = 'none';
  }
}
</script>
</body>
</html>
