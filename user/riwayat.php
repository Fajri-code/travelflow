<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}
include '../config/koneksi.php';

$id_user = $_SESSION['user'];
$nama    = $_SESSION['nama'];
$inisial = strtoupper(substr($nama, 0, 1));

$transaksi = mysqli_query($conn, "
    SELECT t.*, w.nama_wisata, p.nama_paket, k.nama_kendaraan
    FROM transaksi t
    JOIN wisata w ON t.id_wisata = w.id_wisata
    JOIN paket p ON t.id_paket = p.id_paket
    JOIN kendaraan k ON t.id_kendaraan = k.id_kendaraan
    WHERE t.id_user = $id_user
    ORDER BY t.tanggal DESC
");

$total_transaksi = mysqli_num_rows($transaksi);

$total_spend = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT SUM(total) as total FROM transaksi WHERE id_user=$id_user"
))['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Riwayat Transaksi - TravelFlow</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="../assets/css/riwayat.css" rel="stylesheet">
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

<!-- LAYOUT -->
<div class="tf-layout">

    <!-- SIDEBAR -->
    <aside class="tf-sidebar">
        <a href="dashboard.php" class="sidebar-item"><span class="si-icon">⊞</span> Dashboard</a>
        <a href="rencana.php" class="sidebar-item"><span class="si-icon">✈</span> Buat Perjalanan</a>
        <a href="wisata.php" class="sidebar-item"><span class="si-icon">🏝</span> Daftar Wisata</a>
        <a href="riwayat.php" class="sidebar-item active"><span class="si-icon">🕐</span> Riwayat Transaksi</a>
        <a href="ulasan.php" class="sidebar-item"><span class="si-icon">⭐</span> Ulasan Saya</a>
        <a href="profil.php" class="sidebar-item"><span class="si-icon">👤</span> Profil Saya</a>
        <div class="sidebar-divider"></div>
        <div class="sidebar-logout">
            <a href="../auth/logout.php" class="sidebar-item"><span class="si-icon">⇥</span> Logout</a>
        </div>
    </aside>

    <!-- MAIN -->
    <main class="tf-main">

        <!-- Page Header -->
        <div class="riwayat-header">
            <img class="riwayat-hero-img" src="../assets/img/wisata/bag.png" alt="" onerror="this.style.display='none'">
            <div class="riwayat-header-overlay"></div>
            <div class="riwayat-header-content">
                <div class="riwayat-badge"> Riwayat Perjalanan</div>
                <h1>Riwayat Transaksi</h1>
                <p>Semua riwayat pemesanan perjalananmu</p>
            </div>
            <div class="riwayat-header-img"></div>
        </div>

        <!-- Sukses Notif -->
        <?php if (isset($_SESSION['bayar_sukses']) && $_SESSION['bayar_sukses']): ?>
        <div class="sukses-notif">
            <span></span>
            <div>
                <div class="sukses-title">Pembayaran Berhasil!</div>
                <div class="sukses-sub">Pemesanan kamu sudah dikonfirmasi. Selamat menikmati perjalanan!</div>
            </div>
        </div>
        <?php unset($_SESSION['bayar_sukses']); endif; ?>
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon"></div>
                <div>
                    <div class="stat-value"><?= $total_transaksi ?></div>
                    <div class="stat-label">Total Pesanan</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"></div>
                <div>
                    <div class="stat-value">Rp <?= number_format($total_spend, 0, ',', '.') ?></div>
                    <div class="stat-label">Total Pengeluaran</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"></div>
                <div>
                    <div class="stat-value"><?= $total_transaksi ?></div>
                    <div class="stat-label">Transaksi Selesai</div>
                </div>
            </div>
        </div>

        <!-- List -->
        <?php if ($total_transaksi == 0): ?>
            <div class="empty-state">
                <div class="empty-icon"></div>
                <h5>Belum ada transaksi</h5>
                <p>Yuk mulai pesan perjalanan pertamamu!</p>
                <a href="rencana.php" class="btn-booking">Buat Perjalanan Sekarang</a>
            </div>
        <?php else: ?>
            <div class="riwayat-list">
            <?php
            $no = 1;
            while ($t = mysqli_fetch_assoc($transaksi)):
            ?>
                <div class="riwayat-card">
                    <div class="card-left">
                        <div class="card-no">#<?= str_pad($t['id_transaksi'], 4, '0', STR_PAD_LEFT) ?></div>
                        <div class="card-icon"></div>
                    </div>
                    <div class="card-mid">
                        <div class="card-wisata"><?= htmlspecialchars($t['nama_wisata']) ?></div>
                        <div class="card-detail">
                            <span> <?= htmlspecialchars($t['nama_paket']) ?></span>
                            <span class="dot">•</span>
                            <span> <?= htmlspecialchars($t['nama_kendaraan']) ?></span>
                            <span class="dot">•</span>
                            <span> <?= $t['jumlah_orang'] ?> orang</span>
                        </div>
                        <div class="card-tanggal"> <?= date('d M Y, H:i', strtotime($t['tanggal'])) ?></div>
                    </div>
                    <div class="card-right">
                        <div class="card-total">Rp <?= number_format($t['total'], 0, ',', '.') ?></div>
                        <span class="badge-status">Confirmed</span>
                        <a href="struk.php?id=<?= $t['id_transaksi'] ?>" class="btn-struk"> Lihat Struk</a>
                    </div>
                </div>
            <?php $no++; endwhile; ?>
            </div>
        <?php endif; ?>

    </main>
</div>

</body>
</html>
