<?php
session_start();
if (!isset($_SESSION['user'])) { header("Location: ../auth/login.php"); exit; }
if (empty($_SESSION['perjalanan'])) { header("Location: rencana.php"); exit; }
if (empty($_SESSION['perjalanan']['id_kendaraan'])) { header("Location: kendaraan.php"); exit; }
include '../config/koneksi.php';

$nama    = $_SESSION['nama'];
$inisial = strtoupper(substr($nama, 0, 1));
$p       = &$_SESSION['perjalanan'];
$malam   = (int)$p['jumlah_malam'];

// Jika 1 hari, skip langsung
if ($malam === 0) { header("Location: penjemputan.php"); exit; }

if (isset($_POST['lanjut'])) {
    $p['id_hotel'] = (int)($_POST['id_hotel'] ?? 0);
    header("Location: penjemputan.php");
    exit;
}

$hotel_list = [];
$res = mysqli_query($conn, "SELECT * FROM hotel ORDER BY bintang DESC, harga_per_malam ASC");
while ($r = mysqli_fetch_assoc($res)) $hotel_list[] = $r;

function bintang_str($n) { return str_repeat('⭐', (int)$n); }
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Pilih Penginapan – TravelFlow</title>
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
      <div class="flow-step active"><div class="step-num">4</div> Penginapan</div>
      <div class="flow-step-line"></div>
      <div class="flow-step"><div class="step-num">5</div> Penjemputan</div>
      <div class="flow-step-line"></div>
      <div class="flow-step"><div class="step-num">6</div> Ringkasan</div>
    </div>

    <div class="flow-header">
      <img class="flow-header-img" src="../assets/img/wisata/bag.png" alt="" onerror="this.style.display='none'">
      <div class="flow-header-bg"></div>
      <div class="flow-header-content">
        <div class="flow-header-badge">🏨 Langkah 4 dari 6</div>
        <h1>Pilih Penginapan</h1>
        <p>Perjalanan <?= $p['jumlah_hari'] ?> hari <?= $malam ?> malam — biaya hotel dihitung per malam</p>
      </div>
    </div>

    <div class="flow-alert info">
      ℹ️ Penginapan bersifat <strong>opsional</strong>. Anda bisa skip jika sudah punya hotel sendiri atau tidak memerlukan penginapan.
    </div>

    <form method="POST">
      <div class="flow-card">
        <div class="flow-card-title">🏨 Pilih Hotel</div>
        <div class="hotel-grid">
          <?php foreach ($hotel_list as $h):
            $kapasitas_kamar = 2;
            $jumlah_peserta_now = (int)($p['jumlah_peserta'] ?? 1);
            $jumlah_kamar = max(1, ceil($jumlah_peserta_now / $kapasitas_kamar));
            $total_hotel = $h['harga_per_malam'] * $malam * $jumlah_kamar;
            $sisa = $jumlah_peserta_now % $kapasitas_kamar;
            $kamar_info = $jumlah_kamar . ' kamar';
            if ($jumlah_kamar == 1) {
                $kamar_info .= ' (maks 2 orang)';
            } else {
                $isi_kamar = [];
                for ($ki = 1; $ki <= $jumlah_kamar; $ki++) {
                    if ($ki == $jumlah_kamar && $sisa > 0) {
                        $isi_kamar[] = 'Kamar ' . $ki . ': ' . $sisa . ' orang';
                    } else {
                        $isi_kamar[] = 'Kamar ' . $ki . ': 2 orang';
                    }
                }
                $kamar_info .= ' (' . implode(', ', $isi_kamar) . ')';
            }
          ?>
          <label class="hotel-card <?= $p['id_hotel'] == $h['id_hotel'] ? 'selected' : '' ?>"
            onclick="selectHotel(this)">
            <input type="radio" name="id_hotel" value="<?= $h['id_hotel'] ?>"
              <?= $p['id_hotel'] == $h['id_hotel'] ? 'checked' : '' ?>>
            <div class="hotel-card-check">✓</div>
            <div class="hotel-card-name"><?= htmlspecialchars($h['nama_hotel']) ?></div>
            <div class="hotel-card-stars"><?= bintang_str($h['bintang']) ?> <?= $h['bintang'] ?> Bintang</div>
            <div class="hotel-card-alamat">📍 <?= htmlspecialchars($h['alamat'] ?? 'Yogyakarta') ?></div>
            <div class="hotel-card-price">Rp <?= number_format($h['harga_per_malam'], 0, ',', '.') ?> / kamar / malam</div>
            <div style="font-size:.72rem;color:var(--text-sub);margin-top:4px;padding:6px 8px;background:#f8fafc;border-radius:6px;line-height:1.7;">
              👥 Kapasitas: <strong>2 orang/kamar</strong><br>
              🛌 <?= $kamar_info ?><br>
              💰 Rp <?= number_format($h['harga_per_malam'], 0, ',', '.') ?> &times; <?= $jumlah_kamar ?> kamar &times; <?= $malam ?> malam
            </div>
            <div class="hotel-card-total" style="margin-top:6px;font-size:.82rem;">Total: <strong style="color:var(--primary)">Rp <?= number_format($total_hotel, 0, ',', '.') ?></strong></div>
          </label>
          <?php endforeach; ?>
        </div>

        <button type="submit" name="lanjut" class="hotel-skip-btn" style="margin-top:16px;">
          ⏭ Lewati — Tidak memilih penginapan
        </button>
      </div>

      <button type="submit" name="lanjut" class="btn-next">Lanjut: Pilih Penjemputan →</button>
      <a href="kendaraan.php" class="btn-back">← Kembali</a>
    </form>

  </main>
</div>
<script>
function selectHotel(el) {
  document.querySelectorAll('.hotel-card').forEach(c => c.classList.remove('selected'));
  el.classList.add('selected');
  el.querySelector('input').checked = true;
}
</script>
</body>
</html>
