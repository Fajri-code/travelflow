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

$success = '';
$error   = '';

// Submit ulasan
if (isset($_POST['submit_ulasan'])) {
    $id_transaksi = (int)$_POST['id_transaksi'];
    $id_wisata    = (int)$_POST['id_wisata'];
    $rating       = (int)$_POST['rating'];
    $komentar     = mysqli_real_escape_string($conn, $_POST['komentar']);

    // Cek sudah pernah ulasan
    $cek = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id_ulasan FROM ulasan WHERE id_user=$id_user AND id_transaksi=$id_transaksi"));
    if ($cek) {
        $error = 'Kamu sudah memberikan ulasan untuk transaksi ini!';
    } elseif ($rating < 1 || $rating > 5) {
        $error = 'Rating harus antara 1-5!';
    } else {
        mysqli_query($conn, "INSERT INTO ulasan (id_user, id_wisata, id_transaksi, rating, komentar)
            VALUES ($id_user, $id_wisata, $id_transaksi, $rating, '$komentar')");
        kirim_notif($conn, $id_user,
            'Ulasan Terkirim ⭐',
            'Terima kasih! Ulasan kamu dengan rating ' . $rating . '/5 sudah berhasil dikirim.',
            'ulasan'
        );
        $success = 'Ulasan berhasil dikirim! Terima kasih 😊';
    }
}

// Ambil transaksi yang belum diulas
$transaksi_belum = mysqli_query($conn, "
    SELECT t.*, w.nama_wisata, w.gambar
    FROM transaksi t
    JOIN wisata w ON t.id_wisata = w.id_wisata
    LEFT JOIN ulasan u ON t.id_transaksi = u.id_transaksi AND u.id_user = $id_user
    WHERE t.id_user = $id_user AND u.id_ulasan IS NULL
    ORDER BY t.tanggal DESC
");

// Ambil ulasan yang sudah diberikan
$ulasan_saya = mysqli_query($conn, "
    SELECT u.*, w.nama_wisata, w.gambar
    FROM ulasan u
    JOIN wisata w ON u.id_wisata = w.id_wisata
    WHERE u.id_user = $id_user
    ORDER BY u.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ulasan - TravelFlow</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="../assets/css/ulasan.css" rel="stylesheet">
    <link href="../assets/css/sidebar.css" rel="stylesheet">
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

<div class="tf-layout">
  <!-- SIDEBAR -->
    <aside class="tf-sidebar">
        <a href="dashboard.php" class="sidebar-item"><span class="si-icon">⊞</span> Dashboard</a>
        <a href="rencana.php" class="sidebar-item"><span class="si-icon">✈</span> Buat Perjalanan</a>
        <a href="wisata.php" class="sidebar-item"><span class="si-icon">🏝</span> Daftar Wisata</a>
        <a href="riwayat.php" class="sidebar-item"><span class="si-icon">🕐</span> Riwayat Transaksi</a>
        <a href="ulasan.php" class="sidebar-item active"><span class="si-icon">⭐</span> Ulasan Saya</a>
        <a href="profil.php" class="sidebar-item"><span class="si-icon">👤</span> Profil Saya</a>
        <div class="sidebar-divider"></div>
        <div class="sidebar-logout">
            <a href="../auth/logout.php" class="sidebar-item"><span class="si-icon">⇥</span> Logout</a>
        </div>
    </aside>

    <main class="tf-main">

        <!-- Header -->
        <div class="ulasan-header">
            <img class="ulasan-hero-img" src="../assets/img/wisata/bag.png" alt="" onerror="this.style.display='none'">
            <div class="ulasan-header-overlay"></div>
            <div class="ulasan-header-content">
                <div class="ulasan-badge"> Review & Rating</div>
                <h1>Ulasan Perjalanan</h1>
                <p>Bagikan pengalamanmu dan bantu traveller lain memilih destinasi terbaik</p>
            </div>
            <div class="ulasan-header-img"></div>
        </div>

        <?php if ($success): ?>
        <div class="alert-success">✅ <?= $success ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
        <div class="alert-error">❌ <?= $error ?></div>
        <?php endif; ?>

        <div class="ulasan-layout">

            <!-- Belum Diulas -->
            <div class="ulasan-left">
                <div class="section-card">
                    <div class="section-card-title"> Perjalanan yang Belum Diulas</div>

                    <?php if (mysqli_num_rows($transaksi_belum) == 0): ?>
                    <div class="empty-ulasan">
                        <div></div>
                        <p>Semua perjalanan sudah diulas!</p>
                    </div>
                    <?php else: ?>
                    <?php while ($t = mysqli_fetch_assoc($transaksi_belum)): ?>
                    <div class="ulasan-item" id="item-<?= $t['id_transaksi'] ?>">
                        <div class="ulasan-item-header">
                            <div class="ulasan-item-img">
                                <?php if (!empty($t['gambar'])): ?>
                                    <img src="../assets/img/wisata/<?= htmlspecialchars($t['gambar']) ?>" alt="">
                                <?php else: ?>
                                    <div class="ulasan-img-placeholder"></div>
                                <?php endif; ?>
                            </div>
                            <div class="ulasan-item-info">
                                <div class="ulasan-wisata"><?= htmlspecialchars($t['nama_wisata']) ?></div>
                                <div class="ulasan-tanggal"> <?= date('d M Y', strtotime($t['tanggal'])) ?></div>
                            </div>
                            <button class="btn-tulis-ulasan" onclick="toggleForm(<?= $t['id_transaksi'] ?>)">
                                Tulis Ulasan
                            </button>
                        </div>

                        <!-- Form Ulasan -->
                        <div class="ulasan-form-wrap" id="form-<?= $t['id_transaksi'] ?>" style="display:none">
                            <form method="POST">
                                <input type="hidden" name="id_transaksi" value="<?= $t['id_transaksi'] ?>">
                                <input type="hidden" name="id_wisata" value="<?= $t['id_wisata'] ?>">

                                <!-- Star Rating -->
                                <div class="star-rating-wrap">
                                    <div class="star-label">Rating kamu:</div>
                                    <div class="star-group" id="stars-<?= $t['id_transaksi'] ?>">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <span class="star" data-val="<?= $i ?>" onclick="setRating(<?= $t['id_transaksi'] ?>, <?= $i ?>)">★</span>
                                        <?php endfor; ?>
                                    </div>
                                    <input type="hidden" name="rating" id="rating-<?= $t['id_transaksi'] ?>" value="0" required>
                                    <span class="star-text" id="star-text-<?= $t['id_transaksi'] ?>">Pilih rating</span>
                                </div>

                                <!-- Komentar -->
                                <textarea class="tf-textarea" name="komentar" rows="3"
                                    placeholder="Ceritakan pengalamanmu di <?= htmlspecialchars($t['nama_wisata']) ?>..."></textarea>

                                <div class="form-actions">
                                    <button type="button" class="btn-batal" onclick="toggleForm(<?= $t['id_transaksi'] ?>)">Batal</button>
                                    <button type="submit" name="submit_ulasan" class="btn-kirim"> Kirim Ulasan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <?php endwhile; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Ulasan Saya -->
            <div class="ulasan-right">
                <div class="section-card">
                    <div class="section-card-title"> Ulasan yang Sudah Dikirim</div>

                    <?php if (mysqli_num_rows($ulasan_saya) == 0): ?>
                    <div class="empty-ulasan">
                        <div></div>
                        <p>Belum ada ulasan</p>
                    </div>
                    <?php else: ?>
                    <?php while ($u = mysqli_fetch_assoc($ulasan_saya)): ?>
                    <div class="ulasan-sent-item">
                        <div class="sent-header">
                            <div class="sent-img">
                                <?php if (!empty($u['gambar'])): ?>
                                    <img src="../assets/img/wisata/<?= htmlspecialchars($u['gambar']) ?>" alt="">
                                <?php else: ?>
                                    <div class="ulasan-img-placeholder small"></div>
                                <?php endif; ?>
                            </div>
                            <div class="sent-info">
                                <div class="sent-wisata"><?= htmlspecialchars($u['nama_wisata']) ?></div>
                                <div class="sent-stars">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <span class="star-display <?= $i <= $u['rating'] ? 'filled' : '' ?>">★</span>
                                    <?php endfor; ?>
                                    <span class="sent-rating-val"><?= $u['rating'] ?>/5</span>
                                </div>
                            </div>
                        </div>
                        <?php if (!empty($u['komentar'])): ?>
                        <div class="sent-komentar">"<?= htmlspecialchars($u['komentar']) ?>"</div>
                        <?php endif; ?>
                        <div class="sent-tanggal"><?= date('d M Y', strtotime($u['created_at'])) ?></div>
                    </div>
                    <?php endwhile; ?>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </main>
</div>

<script>
function toggleForm(id) {
    const form = document.getElementById('form-' + id);
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
}

const starLabels = ['', 'Sangat Buruk', 'Buruk', 'Cukup', 'Bagus', 'Sangat Bagus'];

function setRating(trxId, val) {
    document.getElementById('rating-' + trxId).value = val;
    document.getElementById('star-text-' + trxId).textContent = starLabels[val];
    const stars = document.querySelectorAll('#stars-' + trxId + ' .star');
    stars.forEach((s, i) => {
        s.classList.toggle('active', i < val);
    });
}
</script>
</body>
</html>
