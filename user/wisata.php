<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}
include '../config/koneksi.php';

$nama    = $_SESSION['nama'];
$inisial = strtoupper(substr($nama, 0, 1));
$category = isset($_GET['category']) ? mysqli_real_escape_string($conn, $_GET['category']) : '';
$kategori_list = mysqli_query($conn, "SELECT DISTINCT kategori FROM wisata WHERE kategori IS NOT NULL ORDER BY kategori ASC");

$wisata_sql = "
    SELECT w.*, MIN(p.harga) as harga_mulai
    FROM wisata w
    LEFT JOIN paket p ON w.id_wisata = p.id_wisata
";
if ($category) {
    $wisata_sql .= " WHERE w.kategori = '$category'";
}
$wisata_sql .= " GROUP BY w.id_wisata";
$wisata = mysqli_query($conn, $wisata_sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Wisata - TravelFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="../assets/css/wisata.css" rel="stylesheet">
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
        <a href="dashboard.php" class="sidebar-item">
            <span class="si-icon"></span> Dashboard
        </a>
        <a href="wisata.php" class="sidebar-item active">
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

    <!-- MAIN -->
    <main class="tf-main">
        <!-- Page Header -->
        <div class="wisata-page-header">
            <div class="wisata-header-content">
                <div class="wisata-header-badge">✈ Jelajahi Indonesia</div>
                <h2 class="wisata-header-title">Temukan Destinasi <span>Impianmu</span></h2>
                <p class="wisata-header-sub">Ribuan pengalaman menakjubkan menunggumu — pilih destinasi dan mulai petualangan tak terlupakan</p>
            </div>
            <div class="wisata-header-img"></div>
        </div>

        <div class="filter-bar">
            <div class="filter-left">
                <div class="filter-label">Kategori:</div>
                <div class="filter-group">
                    <a href="wisata.php" class="filter-pill<?= $category === '' ? ' active' : '' ?>">Semua</a>
                    <?php while ($k = mysqli_fetch_assoc($kategori_list)): ?>
                        <a href="wisata.php?category=<?= urlencode($k['kategori']) ?>" class="filter-pill<?= $category === $k['kategori'] ? ' active' : '' ?>"><?= htmlspecialchars($k['kategori']) ?></a>
                    <?php endwhile; ?>
                </div>
            </div>
            <?php if ($category): ?>
                <a href="wisata.php" class="filter-reset">Reset</a>
            <?php endif; ?>
        </div>

        <div class="wisata-grid">
            <?php while ($w = mysqli_fetch_assoc($wisata)): ?>
            <div class="tf-card">
                <div class="card-img-wrap">
                    <?php if (!empty($w['gambar'])): ?>
                        <img src="../assets/img/wisata/<?= htmlspecialchars($w['gambar']) ?>" alt="<?= htmlspecialchars($w['nama_wisata']) ?>">
                    <?php else: ?>
                        <div class="img-placeholder"></div>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <?php
                        // Parse structured deskripsi if present: "Short blurb. Jam Operasional: ... | Fasilitas: ... | Rating: X"
                        $desc_raw = trim($w['deskripsi'] ?? '');
                        $blurb = $desc_raw;
                        $jam = '';
                        $fasilitas = '';
                        $rating = 0;
                        if ($desc_raw !== '') {
                            $parts = array_map('trim', explode('|', $desc_raw));
                            if (count($parts) === 1 && stripos($desc_raw, 'Jam Operasional:') !== false) {
                                list($left, $right) = preg_split('/Jam Operasional:/i', $desc_raw, 2);
                                $blurb = trim($left);
                                $jam = trim($right);
                            }
                            foreach ($parts as $p) {
                                if (stripos($p, 'Jam Operasional:') !== false) {
                                    $jam = trim(preg_replace('/.*Jam Operasional:/i', '', $p));
                                } elseif (stripos($p, 'Fasilitas:') !== false) {
                                    $fasilitas = trim(preg_replace('/.*Fasilitas:/i', '', $p));
                                } elseif (stripos($p, 'Rating:') !== false) {
                                    $rating = floatval(trim(preg_replace('/.*Rating:/i', '', $p)));
                                }
                            }
                            if ($blurb === '' && !empty($parts[0])) {
                                $blurb = trim(preg_replace('/Jam Operasional:.*/i', '', $parts[0]));
                            }
                        }
                        $excerpt = strlen($blurb) > 110 ? substr($blurb, 0, 107) . '...' : $blurb;
                    ?>
                    <div class="card-badges">
                        <?php if (!empty($w['kategori'])): ?>
                            <span class="card-badge-category"><?= htmlspecialchars($w['kategori']) ?></span>
                        <?php endif; ?>
                        <?php if (!empty($jam)): ?>
                            <span class="card-badge-hour">Jam: <?= htmlspecialchars($jam) ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="card-title"><?= htmlspecialchars($w['nama_wisata']) ?></div>
                    <div class="card-meta">
                        <div class="card-lokasi"><?= htmlspecialchars($w['lokasi']) ?></div>
                        <div class="card-rating">
                            <?php
                                $full = floor($rating);
                                for ($i = 1; $i <= 5; $i++) {
                                    if ($i <= $full) {
                                        echo '<span class="star filled">★</span>';
                                    } else {
                                        echo '<span class="star">★</span>';
                                    }
                                }
                            ?>
                            <span class="rating-value"><?= $rating?number_format($rating,1):'-' ?></span>
                        </div>
                    </div>
                    <?php if (!empty($excerpt)): ?><div class="card-excerpt"><?= htmlspecialchars($excerpt) ?></div><?php endif; ?>
                    <div class="card-footer">
                        <div class="card-footer-left">
                            <?php if (!empty($fasilitas)): ?>
                                <div class="card-chip"><span></span> <?= htmlspecialchars($fasilitas) ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="card-footer-right">
                            <div class="card-price-box">
                                <div class="card-price-label">Mulai dari</div>
                                <div class="card-price">Rp<?= number_format($w['harga_mulai'], 0, ',', '.') ?><span>/orang</span></div>
                            </div>
                            <a href="detail.php?id=<?= $w['id_wisata'] ?>" class="btn-pilih">Pilih Paket</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </main>
</div>

</body>
</html>
