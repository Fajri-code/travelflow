<?php
session_start();
if (!isset($_SESSION['user'])) { header("Location: ../auth/login.php"); exit; }
include '../config/koneksi.php';

$nama    = $_SESSION['nama'];
$inisial = strtoupper(substr($nama, 0, 1));

if (isset($_POST['lanjut'])) {
    $tanggal = $_POST['tanggal'];
    $durasi  = $_POST['durasi'];
    $map = ['1H' => [1,0], '2H1M' => [2,1], '3H2M' => [3,2], '4H3M' => [4,3]];
    [$hari, $malam] = $map[$durasi] ?? [1,0];

    $_SESSION['perjalanan'] = [
        'tanggal_berangkat' => $tanggal,
        'durasi'            => $durasi,
        'jumlah_hari'       => $hari,
        'jumlah_malam'      => $malam,
        'itinerary'         => [],
        'id_kendaraan'      => null,
        'jumlah_peserta'    => 1,
        'id_hotel'          => null,
        'titik_jemput'      => '',
        'detail_jemput'     => '',
        'preselect_wisata'  => (int)($_POST['preselect_wisata'] ?? 0),
        'preselect_paket'   => (int)($_POST['preselect_paket'] ?? 0),
    ];
    header("Location: destinasi.php");
    exit;
}

$p = $_SESSION['perjalanan'] ?? [];
$preselect_wisata = (int)($_GET['preselect_wisata'] ?? 0);
$preselect_paket  = (int)($_GET['preselect_paket'] ?? 0);

// Ambil nama wisata preselect untuk ditampilkan
$preselect_nama = '';
if ($preselect_wisata) {
    $pw = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama_wisata FROM wisata WHERE id_wisata=$preselect_wisata"));
    $preselect_nama = $pw['nama_wisata'] ?? '';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Rencana Perjalanan – TravelFlow</title>
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
      <div class="flow-step active"><div class="step-num">1</div> Rencana</div>
      <div class="flow-step-line"></div>
      <div class="flow-step"><div class="step-num">2</div> Destinasi</div>
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
        <div class="flow-header-badge">✈ Langkah 1 dari 6</div>
        <h1>Rencanakan Perjalanan Anda</h1>
        <p>Tentukan tanggal berangkat dan durasi perjalanan ke Yogyakarta</p>
      </div>
    </div>

    <form method="POST">
      <input type="hidden" name="preselect_wisata" value="<?= $preselect_wisata ?>">
      <input type="hidden" name="preselect_paket" value="<?= $preselect_paket ?>">

      <?php if ($preselect_nama): ?>
      <div class="flow-alert success">
        ✅ Destinasi <strong><?= htmlspecialchars($preselect_nama) ?></strong> sudah dipilih — akan otomatis masuk ke itinerary Hari 1 setelah Anda mengisi tanggal &amp; durasi.
      </div>
      <?php endif; ?>
      <div class="flow-card">
        <div class="flow-card-title">📅 Tanggal Berangkat</div>
        <div class="form-row single">
          <div class="form-group">
            <label class="form-label">Pilih Tanggal Keberangkatan</label>
            <input type="date" class="tf-input" name="tanggal"
              min="<?= date('Y-m-d') ?>"
              value="<?= htmlspecialchars($p['tanggal_berangkat'] ?? date('Y-m-d')) ?>" required>
            <span class="form-hint">Minimal hari ini. Pastikan tanggal sudah benar sebelum lanjut.</span>
          </div>
        </div>
      </div>

      <div class="flow-card">
        <div class="flow-card-title">⏱ Durasi Perjalanan</div>
        <div class="durasi-grid">
          <?php
          $opts = [
            '1H'   => ['1', '1 Hari',  '0 Malam (Pulang Hari)'],
            '2H1M' => ['2', '2 Hari',  '1 Malam'],
            '3H2M' => ['3', '3 Hari',  '2 Malam'],
            '4H3M' => ['4', '4 Hari',  '3 Malam'],
          ];
          $sel = $p['durasi'] ?? '1H';
          foreach ($opts as $val => [$hari, $label, $malam]):
          ?>
          <label class="durasi-pill <?= $sel === $val ? 'selected' : '' ?>" onclick="selectDurasi(this)">
            <input type="radio" name="durasi" value="<?= $val ?>" <?= $sel === $val ? 'checked' : '' ?>>
            <div class="dp-hari"><?= $hari ?></div>
            <div class="dp-label"><?= $label ?></div>
            <div class="dp-malam"><?= $malam ?></div>
          </label>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="flow-alert warning">
        💡 <strong>Tips:</strong> Setiap hari maksimal 2 destinasi wisata. Pilih durasi yang sesuai dengan jumlah tempat yang ingin dikunjungi.
      </div>

      <button type="submit" name="lanjut" class="btn-next">Lanjut: Pilih Destinasi →</button>
    </form>

  </main>
</div>
<script>
function selectDurasi(el) {
  document.querySelectorAll('.durasi-pill').forEach(p => p.classList.remove('selected'));
  el.classList.add('selected');
  el.querySelector('input').checked = true;
}
</script>
</body>
</html>
