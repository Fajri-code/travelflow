<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}
header('Cache-Control: no-store, no-cache, must-revalidate');
include '../config/koneksi.php';

$nama    = $_SESSION['nama'];
$inisial = strtoupper(substr($nama, 0, 1));

$id_wisata = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id_wisata) { header("Location: wisata.php"); exit; }

$wisata = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM wisata WHERE id_wisata=$id_wisata"));
if (!$wisata) { header("Location: wisata.php"); exit; }

$paket_list = mysqli_query($conn, "SELECT * FROM paket WHERE id_wisata=$id_wisata ORDER BY harga ASC");

// Ambil harga paket termurah
$harga_termurah = mysqli_fetch_assoc(mysqli_query($conn, "SELECT MIN(harga) as min_harga FROM paket WHERE id_wisata=$id_wisata"))['min_harga'] ?? 0;

// Ambil rating wisata
$rating_data = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT AVG(rating) as avg_rating, COUNT(*) as total_ulasan
    FROM ulasan WHERE id_wisata = $id_wisata
"));
$avg_rating   = round($rating_data['avg_rating'] ?? 0, 1);
$total_ulasan = $rating_data['total_ulasan'] ?? 0;

// Paket termurah untuk auto-booking
$cheapest_paket = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id_paket FROM paket WHERE id_wisata=$id_wisata ORDER BY harga ASC LIMIT 1"));

// Ambil ulasan terbaru
$ulasan_list = mysqli_query($conn, "
    SELECT u.*, us.nama
    FROM ulasan u
    JOIN users us ON u.id_user = us.id_user
    WHERE u.id_wisata = $id_wisata
    ORDER BY u.created_at DESC LIMIT 3
");

function paket_label($nama_paket) {
    $keywords = [
        'Sunrise' => 'Paket Pagi',
        'Sunset' => 'Paket Sore',
        'Jeep' => 'Paket Adventure',
        'Camping' => 'Paket Camping',
        'ATV' => 'Paket Adventure',
        'Kuliner' => 'Paket Kuliner',
        'City Tour' => 'Paket Kota',
        'Guided Tour' => 'Paket Wisata',
        'Snorkeling' => 'Paket Snorkeling',
        'Gondola' => 'Paket Eksklusif',
        'Budaya' => 'Paket Budaya',
        'Foto' => 'Paket Foto',
        'Dinner' => 'Paket Kuliner',
        'Ramayana' => 'Paket Show',
    ];
    foreach ($keywords as $keyword => $label) {
        if (stripos($nama_paket, $keyword) !== false) return $label;
    }
    return 'Paket Wisata';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($wisata['nama_wisata']) ?> - TravelFlow</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="../assets/css/detail.css" rel="stylesheet">
    <link href="../assets/css/sidebar.css" rel="stylesheet">
</head>
<body>

<!-- NAVBAR -->
<nav class="tf-navbar">
    <a href="dashboard.php" class="brand">
        <div class="brand-icon">✈</div>
        TravelFlow
    </a>
    <div class="nav-user" title="<?= htmlspecialchars($nama) ?>"><?= $inisial ?></div>
</nav>

<div class="tf-layout">
    <!-- SIDEBAR -->
    <aside class="tf-sidebar">
        <a href="dashboard.php" class="sidebar-item"><span class="si-icon">⊞</span> Dashboard</a>
        <a href="rencana.php" class="sidebar-item"><span class="si-icon">✈</span> Buat Perjalanan</a>
        <a href="wisata.php" class="sidebar-item active"><span class="si-icon">🏝</span> Daftar Wisata</a>
        <a href="riwayat.php" class="sidebar-item"><span class="si-icon">🕐</span> Riwayat Transaksi</a>
        <a href="ulasan.php" class="sidebar-item"><span class="si-icon">⭐</span> Ulasan Saya</a>
        <a href="profil.php" class="sidebar-item"><span class="si-icon">👤</span> Profil Saya</a>
        <div class="sidebar-divider"></div>
        <div class="sidebar-logout">
            <a href="../auth/logout.php" class="sidebar-item"><span class="si-icon">⇥</span> Logout</a>
        </div>
    </aside>

    <main class="tf-main">

        <!-- Back Button -->
        <a href="wisata.php" class="btn-back">
            <span class="btn-back-arrow">←</span>
            Kembali ke Daftar Wisata
        </a>

        <!-- Hero -->
        <div class="detail-hero">
            <?php if (!empty($wisata['gambar'])): ?>
                <img src="../assets/img/wisata/<?= htmlspecialchars($wisata['gambar']) ?>" alt="<?= htmlspecialchars($wisata['nama_wisata']) ?>">
            <?php else: ?>
                <div class="hero-placeholder">🌏</div>
            <?php endif; ?>
            <div class="hero-overlay">
                <div class="hero-top">
                    <span class="hero-badge">✈ Destinasi Wisata</span>
                </div>
                <div class="hero-bottom">
                    <h1><?= htmlspecialchars($wisata['nama_wisata']) ?></h1>
                    <div class="hero-meta">
                        <?php if (!empty($wisata['lokasi'])): ?>
                        <span class="hero-meta-item">📍 <?= htmlspecialchars($wisata['lokasi']) ?></span>
                        <?php endif; ?>
                        <span class="hero-meta-item">⭐ <?= $avg_rating > 0 ? $avg_rating : '4.8' ?> / 5.0</span>
                        <span class="hero-meta-item">💬 <?= $total_ulasan ?> Ulasan</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="detail-layout">
            <!-- KIRI -->
            <div class="detail-left">

                <!-- Tentang -->
                <div class="detail-card">
                    <div class="card-header-row">
                        <span class="card-header-icon">📖</span>
                        <h3 class="section-title">Tentang Destinasi</h3>
                    </div>
                    <p class="detail-desc">
                        <?= !empty($wisata['deskripsi'])
                            ? htmlspecialchars($wisata['deskripsi'])
                            : 'Nikmati pengalaman wisata yang tak terlupakan di destinasi ini. Temukan keindahan alam, budaya, dan kuliner khas yang memanjakan.' ?>
                    </p>
                </div>

                <!-- Fasilitas -->
                <div class="detail-card">
                    <div class="card-header-row">
                        <span class="card-header-icon">🎁</span>
                        <h3 class="section-title">Yang Sudah Termasuk</h3>
                    </div>
                    <div class="fasilitas-grid">
                        <div class="fasilitas-item"><span>🧭</span> Pemandu wisata</div>
                        <div class="fasilitas-item"><span>🚐</span> Transportasi lokal</div>
                        <div class="fasilitas-item"><span>🎟</span> Tiket masuk objek wisata</div>
                        <div class="fasilitas-item"><span>📸</span> Dokumentasi foto</div>
                        <div class="fasilitas-item"><span>🛡</span> Asuransi perjalanan</div>
                        <div class="fasilitas-item"><span>💧</span> Air mineral</div>
                    </div>
                </div>

                <!-- Pilih Paket -->
                <div class="detail-card">
                    <div class="card-header-row">
                        <span class="card-header-icon">📦</span>
                        <h3 class="section-title">Pilih Paket</h3>
                    </div>
                    <div class="flow-alert-detail" style="background:#fff7ed;border:1px solid #fed7aa;border-radius:10px;padding:12px 16px;margin-bottom:16px;font-size:.83rem;color:#92400e;font-weight:600;">
                        ✈️ Memilih paket akan membawa Anda ke alur pemesanan lengkap (tanggal, kendaraan, hotel, penjemputan)
                    </div>
                    <div class="paket-list">
                        <?php
                        $colors = ['#fff5f0', '#f0fdf4', '#eff6ff', '#fdf4ff'];
                        $i = 0;
                        while ($p = mysqli_fetch_assoc($paket_list)):
                        $bg = $colors[$i % count($colors)];
                        $i++;
                        ?>
                        <div class="paket-item<?= $p['id_paket'] == ($cheapest_paket['id_paket'] ?? 0) ? ' termurah' : '' ?>" style="background:<?= $bg ?>">
                            <?php $paket_desc = $p['deskripsi'] ?? ''; ?>
                            <?php $is_termurah = $p['id_paket'] == ($cheapest_paket['id_paket'] ?? 0); ?>
                            <div class="paket-info">
                                <div class="paket-badge<?= $is_termurah ? ' termurah' : '' ?>">
                                    <?= $is_termurah ? 'Paket Termurah' : paket_label($p['nama_paket']) ?>
                                </div>
                                <div class="paket-nama"><?= htmlspecialchars($p['nama_paket']) ?></div>
                                <div class="paket-meta"><?= htmlspecialchars($p['durasi']) ?> &middot; Rp <?= number_format($p['harga'], 0, ',', '.') ?><span>/orang</span></div>
                                <?php if (!empty($paket_desc)): ?>
                                <div class="paket-desc"><?= htmlspecialchars($paket_desc) ?></div>
                                <?php endif; ?>
                            </div>
                            <a href="rencana.php?preselect_wisata=<?= $id_wisata ?>&preselect_paket=<?= $p['id_paket'] ?>" class="btn-pesan">
                                Pilih Paket &amp; Buat Perjalanan
                            </a>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </div>

                <!-- Ulasan -->
                <?php if ($total_ulasan > 0): ?>
                <div class="detail-card">
                    <div class="card-header-row">
                        <span class="card-header-icon">⭐</span>
                        <h3 class="section-title">Ulasan Wisatawan</h3>
                        <span style="margin-left:auto;font-size:.82rem;color:#f59e0b;font-weight:700"><?= $avg_rating ?>/5 (<?= $total_ulasan ?> ulasan)</span>
                    </div>
                    <?php while ($u = mysqli_fetch_assoc($ulasan_list)): ?>
                    <div style="padding:12px 0;border-bottom:1px solid #f1f5f9">
                        <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px">
                            <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#f97316,#ea580c);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:.85rem"><?= strtoupper(substr($u['nama'],0,1)) ?></div>
                            <div>
                                <div style="font-size:.85rem;font-weight:700;color:#1e293b"><?= htmlspecialchars($u['nama']) ?></div>
                                <div style="display:flex;gap:2px"><?php for($s=1;$s<=5;$s++) echo '<span style="color:'.($s<=$u['rating']?'#f59e0b':'#e2e8f0').';font-size:.9rem">★</span>'; ?></div>
                            </div>
                        </div>
                        <?php if (!empty($u['komentar'])): ?>
                        <p style="font-size:.82rem;color:#64748b;font-style:italic">"<?= htmlspecialchars($u['komentar']) ?>"</p>
                        <?php endif; ?>
                    </div>
                    <?php endwhile; ?>
                    <a href="ulasan.php" style="display:block;text-align:center;margin-top:12px;font-size:.8rem;font-weight:700;color:#f97316">Lihat semua ulasan →</a>
                </div>
                <?php endif; ?>

            </div>

            <!-- KANAN -->
            <div class="detail-right">
                <div class="summary-card">
                    <div class="summary-img">
                        <?php if (!empty($wisata['gambar'])): ?>
                            <img src="../assets/img/wisata/<?= htmlspecialchars($wisata['gambar']) ?>" alt="">
                        <?php else: ?>
                            <div class="summary-placeholder">🌏</div>
                        <?php endif; ?>
                        <div class="summary-img-overlay"></div>
                    </div>
                    <div class="summary-body">
                        <h4><?= htmlspecialchars($wisata['nama_wisata']) ?></h4>
                        <?php if (!empty($wisata['lokasi'])): ?>
                        <p class="summary-lokasi">📍 <?= htmlspecialchars($wisata['lokasi']) ?></p>
                        <?php endif; ?>
                        <div class="summary-divider"></div>
                        <div class="summary-price-label">Mulai dari</div>
                        <div class="summary-price">
                            Rp <?= number_format($harga_termurah, 0, ',', '.') ?>
                            <span>/orang</span>
                        </div>
                        <a href="rencana.php?preselect_wisata=<?= $id_wisata ?>" class="btn-booking-now">
                            ✈️ Buat Perjalanan
                        </a>
                        <div class="summary-note">
                            🔒 Pembayaran aman & terpercaya
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

</body>
</html>
