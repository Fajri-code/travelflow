<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}
include '../config/koneksi.php';

$nama    = $_SESSION['nama'];
$inisial = strtoupper(substr($nama, 0, 1));

$budget = isset($_GET['budget']) ? $_GET['budget'] : 'semua';

$where = "WHERE 1=1";
if ($budget == 'hemat')   $where .= " AND harga_per_malam <= 300000";
if ($budget == 'sedang')  $where .= " AND harga_per_malam BETWEEN 300001 AND 700000";
if ($budget == 'premium') $where .= " AND harga_per_malam > 700000";

$hotels = mysqli_query($conn, "SELECT * FROM hotel $where ORDER BY bintang DESC, harga_per_malam ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pilih Hotel - TravelFlow</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="../assets/css/hotel.css" rel="stylesheet">
    <link href="../assets/css/sidebar.css" rel="stylesheet">
</head>
<body>

<nav class="tf-navbar">
    <a href="dashboard.php" class="brand">
        <div class="brand-icon">✈</div>
        TravelFlow
    </a>
    <div class="nav-user" title="<?= htmlspecialchars($nama) ?>"><?= $inisial ?></div>
</nav>

<!-- SIDEBAR -->
    <aside class="tf-sidebar">
        <a href="dashboard.php" class="sidebar-item ">
            <span class="si-icon"></span> Dashboard
        </a>
          <a href="hotel.php" class="sidebar-item active"><span class="si-icon"></span> Pilih Hotel</a>
        <a href="wisata.php" class="sidebar-item ">
            <span class="si-icon"></span> Daftar Wisata
        </a>
        <a href="keranjang.php" class="sidebar-item">
            <span class="si-icon"></span> Booking
        </a>
        <a href="riwayat.php" class="sidebar-item ">
            <span class="si-icon"></span> Riwayat Transaksi
        </a>
        <a href="ulasan.php" class="sidebar-item">
            <span class="si-icon"></span> Ulasan Saya
        </a>
        <a href="profil.php" class="sidebar-item">
            <span class="si-icon"></span> Profil Saya
        </a>
        <div class="sidebar-divider"></div>
        <div class="sidebar-logout">
            <a href="../auth/logout.php" class="sidebar-item">
                <span class="si-icon">⇥</span> Logout
            </a>
        </div>
    </aside>
    <main class="tf-main">

        <!-- Header -->
        <div class="hotel-header">
            <img class="hotel-hero-img" src="../assets/img/wisata/bag.png" alt="" onerror="this.style.display='none'">
            <div class="hotel-header-overlay"></div>
            <div class="hotel-header-content">
                <div class="hotel-badge"> Titik Penjemputan</div>
                <h1>Pilih Hotel</h1>
                <p>Sopir kami menjemput tepat di depan hotelmu setiap hari</p>
            </div>
            <div class="hotel-header-img"></div>
        </div>

        <!-- Info Box -->
        <div class="info-box">
            <span class="info-icon"></span>
            <div>
                <div class="info-title">Cara Kerja</div>
                <div class="info-desc">Pilih hotel sebagai titik penjemputan. Sopir menjemput pagi hari → antar ke destinasi → kembali ke hotel. Maks <strong>2 destinasi per hari</strong>. Untuk menginap, booking langsung ke hotel.</div>
            </div>
        </div>

        <!-- Filter Budget -->
        <div class="budget-filter">
            <span class="budget-label">Filter Budget/malam:</span>
            <a href="hotel.php" class="budget-btn <?= $budget=='semua'?'active':'' ?>"> Semua</a>
            <a href="hotel.php?budget=hemat" class="budget-btn <?= $budget=='hemat'?'active':'' ?>"> Hemat (≤ Rp 300rb)</a>
            <a href="hotel.php?budget=sedang" class="budget-btn <?= $budget=='sedang'?'active':'' ?>"> Sedang (Rp 300-700rb)</a>
            <a href="hotel.php?budget=premium" class="budget-btn <?= $budget=='premium'?'active':'' ?>"> Premium (> Rp 700rb)</a>
        </div>

        <!-- Hotel Grid -->
        <?php if (mysqli_num_rows($hotels) == 0): ?>
        <div class="hotel-empty">
            <div></div>
            <h5>Tidak ada hotel untuk budget ini</h5>
            <a href="hotel.php" class="btn-reset">Lihat Semua Hotel</a>
        </div>
        <?php else: ?>
        <div class="hotel-grid">
            <?php while ($h = mysqli_fetch_assoc($hotels)): ?>
            <div class="hotel-card">
                <div class="hotel-img-wrap">
                    <?php if (!empty($h['gambar'])): ?>
                        <img src="../assets/img/hotel/<?= htmlspecialchars($h['gambar']) ?>" alt="">
                    <?php else: ?>
                        <div class="hotel-img-placeholder"></div>
                    <?php endif; ?>
                    <div class="hotel-bintang">
                        <?php for ($i = 0; $i < $h['bintang']; $i++) echo ''; ?>
                    </div>
                    <?php
                    $label = '';
                    if ($h['harga_per_malam'] <= 300000) $label = ['', 'Hemat'];
                    elseif ($h['harga_per_malam'] <= 700000) $label = ['', 'Sedang'];
                    else $label = ['', 'Premium'];
                    ?>
                    <div class="hotel-budget-badge"><?= $label[0] ?> <?= $label[1] ?></div>
                </div>
                <div class="hotel-body">
                    <div class="hotel-nama"><?= htmlspecialchars($h['nama_hotel']) ?></div>
                    <div class="hotel-lokasi"> <?= htmlspecialchars($h['lokasi']) ?></div>
                    <div class="hotel-alamat"> <?= htmlspecialchars($h['alamat']) ?></div>
                    <div class="hotel-footer">
                        <div>
                            <div class="hotel-harga-label">Referensi harga/malam</div>
                            <div class="hotel-harga">Rp <?= number_format($h['harga_per_malam'], 0, ',', '.') ?></div>
                        </div>
                        <a href="keranjang.php?id_hotel=<?= $h['id_hotel'] ?>" class="btn-pilih-hotel">
                            Pilih 
                        </a>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <?php endif; ?>

    </main>
</div>

</body>
</html>
