<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}
include '../config/koneksi.php';
include '../config/notif.php';

$id_user = $_SESSION['user'];
$nama    = $_SESSION['nama'];
$inisial = strtoupper(substr($nama, 0, 1));
$unread  = count_unread($conn, $id_user);

// Mark all read
if (isset($_GET['mark_read'])) {
    mark_all_read($conn, $id_user);
    header("Location: dashboard.php");
    exit;
}

$total_booking = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) as total FROM transaksi WHERE id_user=$id_user")
)['total'];

$total_spend = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT SUM(total) as total FROM transaksi WHERE id_user=$id_user")
)['total'] ?? 0;

$transaksi_terakhir = mysqli_query($conn, "
    SELECT t.*, w.nama_wisata, p.nama_paket, k.nama_kendaraan
    FROM transaksi t
    JOIN wisata w ON t.id_wisata = w.id_wisata
    JOIN paket p ON t.id_paket = p.id_paket
    JOIN kendaraan k ON t.id_kendaraan = k.id_kendaraan
    WHERE t.id_user = $id_user
    ORDER BY t.tanggal DESC
    LIMIT 3
");

// Jam untuk greeting
$jam = date('H');
if ($jam >= 5 && $jam < 12) $greeting = 'Selamat Pagi';
elseif ($jam >= 12 && $jam < 15) $greeting = 'Selamat Siang';
elseif ($jam >= 15 && $jam < 18) $greeting = 'Selamat Sore';
else $greeting = 'Selamat Malam';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard – TravelFlow</title>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="../assets/css/dashboard.css" rel="stylesheet">
  <link href="../assets/css/sidebar.css" rel="stylesheet">
</head>
<body>

<nav class="tf-navbar">
  <a href="dashboard.php" class="brand">
    <div class="brand-icon">✈</div>
    TravelFlow
  </a>
  <div class="nav-right-group">
    <div class="notif-wrap" id="notifWrap">
      <button class="notif-btn" onclick="toggleNotif()">
        🔔
        <?php if ($unread > 0): ?>
        <span class="notif-badge"><?= $unread ?></span>
        <?php endif; ?>
      </button>
      <div class="notif-dropdown" id="notifDropdown">
        <div class="notif-header">
          <span>Notifikasi</span>
          <?php if ($unread > 0): ?>
          <a href="?mark_read=1" class="notif-mark-read">Tandai dibaca</a>
          <?php endif; ?>
        </div>
        <div class="notif-list">
          <?php
          $notifs = get_notif($conn, $id_user, 5);
          if (mysqli_num_rows($notifs) == 0):
          ?>
          <div class="notif-empty">🔔 Belum ada notifikasi</div>
          <?php else: while ($n = mysqli_fetch_assoc($notifs)): ?>
          <div class="notif-item <?= $n['is_read'] ? '' : 'unread' ?>">
            <div class="notif-icon"><?= $n['icon'] == 'booking' ? '🎉' : ($n['icon'] == 'ulasan' ? '⭐' : '🔔') ?></div>
            <div class="notif-content">
              <div class="notif-judul"><?= htmlspecialchars($n['judul']) ?></div>
              <div class="notif-pesan"><?= htmlspecialchars($n['pesan']) ?></div>
              <div class="notif-time"><?= date('d M Y H:i', strtotime($n['created_at'])) ?></div>
            </div>
          </div>
          <?php endwhile; endif; ?>
        </div>
      </div>
    </div>
    <div class="nav-user" title="<?= htmlspecialchars($nama) ?>"><?= $inisial ?></div>
  </div>
</nav>

<div class="tf-layout">

 <!-- SIDEBAR -->
    <aside class="tf-sidebar">
        <a href="dashboard.php" class="sidebar-item active">
            <span class="si-icon">⊞</span> Dashboard
        </a>
        <a href="rencana.php" class="sidebar-item"><span class="si-icon">✈</span> Buat Perjalanan</a>
        <a href="wisata.php" class="sidebar-item">
            <span class="si-icon">🏝</span> Daftar Wisata
        </a>

        <a href="riwayat.php" class="sidebar-item">
            <span class="si-icon">🕐</span> Riwayat Transaksi
        </a>
        <a href="ulasan.php" class="sidebar-item">
            <span class="si-icon">⭐</span> Ulasan Saya
        </a>
        <a href="profil.php" class="sidebar-item">
            <span class="si-icon">👤</span> Profil Saya
        </a>
        <div class="sidebar-divider"></div>
        <div class="sidebar-logout">
            <a href="../auth/logout.php" class="sidebar-item">
                <span class="si-icon">⇥</span> Logout
            </a>
        </div>
    </aside>


  <!-- MAIN -->
  <main class="tf-main">

    <!-- Hero Banner -->
    <div class="tf-hero">
      <img class="hero-img" src="../assets/img/wisata/bag.png" alt="Jogja Hero" onerror="this.style.display='none'">
      <div class="hero-bg"></div>
      <div class="hero-content">
        <h1>Jelajahi Yogyakarta dengan Nyaman &amp; Berkesan</h1>
        <p>Temukan keajaiban candi, pantai, dan budaya Jogja yang tak terlupakan</p>
        
      </div>
    </div>

    <!-- Greeting -->
    <div class="greeting-row">
      <div class="greeting-left">
        <div class="greeting-avatar"><?= $inisial ?></div>
        <div>
          <div class="greeting-text"><?= $greeting ?>, <span><?= htmlspecialchars($nama) ?>!</span> </div>
          <div class="greeting-sub">Mau liburan ke mana hari ini?</div>
        </div>
      </div>
      <div class="greeting-stat">
        <div class="stat-item">
          <div class="stat-val"><?= $total_booking ?></div>
          <div class="stat-lbl">Total Booking</div>
        </div>
        <div class="stat-divider"></div>
        <div class="stat-item">
          <div class="stat-val">Rp <?= number_format($total_spend, 0, ',', '.') ?></div>
          <div class="stat-lbl">Total Pengeluaran</div>
        </div>
      </div>
    </div>


    <!-- Hotel Banner — diganti jadi CTA flow baru -->
    <div class="hotel-banner">
        <div class="hotel-banner-left">
            <div class="hotel-banner-badge">✈ Travel Agent Profesional</div>
            <h3>Rencanakan Perjalanan Impian Anda</h3>
            <p>Buat itinerary lengkap dengan destinasi pilihan, kendaraan + sopir, penginapan, dan titik penjemputan — semua dalam satu alur pemesanan yang mudah.</p>
            <div class="hotel-banner-features">
                <div class="hotel-feature-item">✅ Itinerary per hari</div>
                <div class="hotel-feature-item">✅ Maks 2 destinasi/hari</div>
                <div class="hotel-feature-item">✅ Sopir profesional</div>
                <div class="hotel-feature-item">✅ Hotel & penjemputan</div>
            </div>
            <a href="rencana.php" class="hotel-banner-btn">✈ Buat Perjalanan Sekarang</a>
        </div>
        <div class="hotel-banner-right">
            <div class="hotel-banner-cards">
                <div class="hotel-mini-card">
                    <div class="hotel-mini-icon">📅</div>
                    <div class="hotel-mini-name">Pilih Tanggal & Durasi</div>
                    <div class="hotel-mini-price">Langkah 1</div>
                </div>
                <div class="hotel-mini-card">
                    <div class="hotel-mini-icon">🏝</div>
                    <div class="hotel-mini-name">Pilih Destinasi</div>
                    <div class="hotel-mini-price">Langkah 2</div>
                </div>
                <div class="hotel-mini-card">
           
                <div class="hotel-mini-icon">🚗</div>
                    <div class="hotel-mini-name">Kendaraan & Hotel</div>
                    <div class="hotel-mini-price">Langkah 3–4</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Destinasi Populer -->
    <div class="section-header">
      <h2 class="tf-section-title mb-0"> Destinasi Populer</h2>
      <a href="wisata.php" class="lihat-semua">Lihat Semua</a>
    </div>
    <div class="tf-wisata-grid">
      <?php
      $wisata_populer = mysqli_query($conn, "SELECT w.*, MIN(p.harga) as harga_termurah FROM wisata w LEFT JOIN paket p ON w.id_wisata=p.id_wisata GROUP BY w.id_wisata ORDER BY w.id_wisata ASC LIMIT 4");
      while ($w = mysqli_fetch_assoc($wisata_populer)):
        $gambar = !empty($w['gambar']) ? '../assets/img/wisata/' . htmlspecialchars($w['gambar']) : '';
      ?>
      <div class="tf-card">
        <div class="card-img-wrap">
          <?php if (!empty($gambar)): ?>
            <img src="<?= $gambar ?>" alt="<?= htmlspecialchars($w['nama_wisata']) ?>">
          <?php else: ?>
            <div style="height:100%;display:flex;align-items:center;justify-content:center;font-size:3rem;background:linear-gradient(135deg,#fff5f0,#fed7aa)">🌏</div>
          <?php endif; ?>
        </div>
        <div class="card-body">
          <div class="card-title"><?= htmlspecialchars($w['nama_wisata']) ?></div>
          <div class="card-location">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
            </svg>
            <?= htmlspecialchars($w['lokasi'] ?? 'Yogyakarta') ?>
          </div>
          <div class="card-footer-row">
            <div>
              <div class="card-price-label">Mulai dari</div>
              <div class="card-price">Rp <?= number_format($w['harga_termurah'] ?? 0, 0, ',', '.') ?><span>/ orang</span></div>
            </div>
            <a href="detail.php?id=<?= $w['id_wisata'] ?>">
              <button class="btn-pilih">Lihat<br>Detail</button>
            </a>
          </div>
        </div>
      </div>
      <?php endwhile; ?>
    </div>

    <!-- Tips Perjalanan -->
    <h2 class="tf-section-title">

  <span>Tips Perjalanan</span>
</h2>
    <div class="tips-grid">
      <div class="tips-card">
        <div class="tips-icon">🎒</div>
        <div class="tips-title">Bawa Perlengkapan</div>
        <div class="tips-desc">Siapkan pakaian nyaman, sunscreen, dan kamera untuk mengabadikan momen.</div>
      </div>
      <div class="tips-card">
        <div class="tips-icon">🗺️</div>
        <div class="tips-title">Rencanakan Rute</div>
        <div class="tips-desc">Susun itinerary agar perjalanan lebih efisien dan tidak ada destinasi yang terlewat.</div>
      </div>
      <div class="tips-card">
        <div class="tips-icon">💰</div>
        <div class="tips-title">Atur Budget</div>
        <div class="tips-desc">Siapkan dana cadangan 20% dari total budget untuk keperluan tak terduga.</div>
      </div>
      <div class="tips-card">
        <div class="tips-icon">📱</div>
        <div class="tips-title">Simpan Kontak</div>
        <div class="tips-desc">Simpan nomor pemandu wisata dan kontak darurat sebelum berangkat.</div>
      </div>
    </div>

    <!-- Promo Banner -->
    <div class="promo-banner">
      <div class="promo-content">
        <div class="promo-badge">Penawaran Spesial</div>
        <h3>Diskon 20% untuk Booking Pertama!</h3>
        <p>Gunakan kode <strong>JOGJA20</strong> saat booking dan dapatkan potongan harga spesial.</p>
        <a href="rencana.php" class="promo-btn">Mulai Buat Perjalanan</a>
      </div>
      <div class="promo-img"></div>
    </div>


    <div class="tion-header">
      <h2 class="tf-section-title mb-0">Transaksi Terakhir</h2>
      <a href="riwayat.php" class="lihat-semua">Lihat Semua </a>
    </div>

    <?php if (mysqli_num_rows($transaksi_terakhir) == 0): ?>
      <div class="empty-transaksi">
        <div class="empty-icon">📭</div>
        <p>Belum ada transaksi. <a href="rencana.php">Buat perjalanan pertama sekarang!</a></p>
      </div>
    <?php else: ?>
      <div class="transaksi-list">
        <?php while ($t = mysqli_fetch_assoc($transaksi_terakhir)): ?>
        <div class="transaksi-card">
          <div class="trx-icon">🏝</div>
          <div class="trx-mid">
            <div class="trx-wisata"><?= htmlspecialchars($t['nama_wisata']) ?></div>
            <div class="trx-detail">
              <?= htmlspecialchars($t['nama_paket']) ?> &nbsp;•&nbsp;
              <?= htmlspecialchars($t['nama_kendaraan']) ?> &nbsp;•&nbsp;
              <?= $t['jumlah_orang'] ?> orang
            </div>
            <div class="trx-tanggal">📅 <?= date('d M Y, H:i', strtotime($t['tanggal'])) ?></div>
          </div>
          <div class="trx-right">
            <div class="trx-total">Rp <?= number_format($t['total'], 0, ',', '.') ?></div>
            <a href="struk.php?id=<?= $t['id_transaksi'] ?>" class="trx-btn">🧾 Struk</a>
          </div>
        </div>
        <?php endwhile; ?>
      </div>
    <?php endif; ?>

  </main>
</div>

<script>
function toggleNotif() {
    const dd = document.getElementById('notifDropdown');
    dd.classList.toggle('show');
}
document.addEventListener('click', function(e) {
    const wrap = document.getElementById('notifWrap');
    if (wrap && !wrap.contains(e.target)) {
        document.getElementById('notifDropdown').classList.remove('show');
    }
});
</script>
</body>
</html>
