<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}
if (empty($_SESSION['checkout_data'])) {
    header("Location: keranjang.php");
    exit;
}
include '../config/koneksi.php';
include '../config/notif.php';

$nama    = $_SESSION['nama'];
$inisial = strtoupper(substr($nama, 0, 1));
$id_user = $_SESSION['user'];
$items   = $_SESSION['checkout_data'];
$total   = array_sum(array_column($items, 'subtotal'));
$promo_code = $_SESSION['promo_code'] ?? '';
$promo_discount = $_SESSION['promo_discount'] ?? 0;
$total_setelah_diskon = max(0, $total - $promo_discount);

$rekening = [
    'BCA'     => ['no' => '1234567890', 'atas_nama' => 'TravelFlow Indonesia'],
    'BNI'     => ['no' => '9876543210', 'atas_nama' => 'TravelFlow Indonesia'],
    'Mandiri' => ['no' => '1122334455', 'atas_nama' => 'TravelFlow Indonesia'],
    'BRI'     => ['no' => '5566778899', 'atas_nama' => 'TravelFlow Indonesia'],
];

// Proses konfirmasi pembayaran
if (isset($_POST['konfirmasi'])) {
    $metode       = mysqli_real_escape_string($conn, $_POST['metode']);
    $nama_pemesan = mysqli_real_escape_string($conn, $_POST['nama_pemesan']);
    $no_telp      = mysqli_real_escape_string($conn, $_POST['no_telp']);

    $origin_total = $total;
    $final_total = max(0, $origin_total - $promo_discount);
    $distributed = 0;
    $item_count = count($items);

    foreach ($items as $idx => $item) {
        $kode_booking = 'TF-' . strtoupper(substr(md5(uniqid()), 0, 8));
        if ($promo_discount > 0 && $origin_total > 0) {
            if ($idx === $item_count - 1) {
                $item_total = $final_total - $distributed;
            } else {
                $item_total = floor($item['subtotal'] * ($final_total / $origin_total));
                $distributed += $item_total;
            }
        } else {
            $item_total = $item['subtotal'];
        }

        mysqli_query($conn, "INSERT INTO transaksi (id_user, id_wisata, id_paket, id_kendaraan, jumlah_orang, tanggal, nama_pemesan, no_telp, kode_booking, status, total)
            VALUES ($id_user, {$item['id_wisata']}, {$item['id_paket']}, {$item['id_kendaraan']}, {$item['jumlah_orang']}, '{$item['tanggal']}', '$nama_pemesan', '$no_telp', '$kode_booking', 'confirmed', {$item_total})");
        
        // Kirim notifikasi
        kirim_notif($conn, $id_user,
            'Booking Berhasil! 🎉',
            'Booking ke ' . $item['nama_wisata'] . ' dengan kode ' . $kode_booking . ' telah dikonfirmasi. Total: Rp ' . number_format($item_total, 0, ',', '.'),
            'booking'
        );
    }
    $_SESSION['keranjang']     = [];
    $_SESSION['checkout_data'] = [];
    unset($_SESSION['promo_code'], $_SESSION['promo_discount']);
    $_SESSION['bayar_sukses']  = true;
    header("Location: riwayat.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pembayaran - TravelFlow</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="../assets/css/pembayaran.css" rel="stylesheet">
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
    <aside class="tf-sidebar">
        <a href="dashboard.php" class="sidebar-item"><span class="si-icon">⊞</span> Dashboard</a>
        <a href="wisata.php" class="sidebar-item"><span class="si-icon">🏝</span> Daftar Wisata</a>
        <a href="keranjang.php" class="sidebar-item"><span class="si-icon">🛒</span> Booking</a>
        <a href="riwayat.php" class="sidebar-item"><span class="si-icon">🕐</span> Riwayat Transaksi</a>
        <a href="profil.php" class="sidebar-item"><span class="si-icon">👤</span> Profil Saya</a>
        <div class="sidebar-divider"></div>
        <div class="sidebar-logout">
            <a href="../auth/logout.php" class="sidebar-item"><span class="si-icon">⇥</span> Logout</a>
        </div>
    </aside>

    <main class="tf-main">

        <!-- Step Indicator -->
        <div class="steps">
            <div class="step done">✓ Keranjang</div>
            <div class="step-line done"></div>
            <div class="step active">💳 Pembayaran</div>
            <div class="step-line"></div>
            <div class="step">✓ Selesai</div>
        </div>

        <div class="bayar-layout">

            <!-- KIRI: Pilih Metode -->
            <div class="bayar-left">

                <!-- Ringkasan Order -->
                <div class="bayar-card">
                    <div class="bayar-card-title">🧾 Ringkasan Pesanan</div>
                    <?php foreach ($items as $item): ?>
                    <div class="order-item">
                        <div class="order-img">
                            <?php if (!empty($item['gambar'])): ?>
                                <img src="../assets/img/wisata/<?= htmlspecialchars($item['gambar']) ?>" alt="">
                            <?php else: ?>
                                <div class="order-img-placeholder">🌏</div>
                            <?php endif; ?>
                        </div>
                        <div class="order-info">
                            <div class="order-wisata"><?= htmlspecialchars($item['nama_wisata']) ?></div>
                            <div class="order-detail">
                                <?= htmlspecialchars($item['nama_paket']) ?>
                                <?php if (!empty($item['durasi_paket'])): ?>
                                    (<?= htmlspecialchars($item['durasi_paket']) ?>)
                                <?php endif; ?>
                                • <?= htmlspecialchars($item['nama_kendaraan']) ?> •
                                <?= $item['jumlah_orang'] ?> orang
                            </div>
                            <div class="order-tanggal">📅 <?= date('d M Y', strtotime($item['tanggal'])) ?></div>
                        </div>
                        <div class="order-price">Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></div>
                    </div>
                    <?php endforeach; ?>
                    <div class="order-total-row">
                        <span>Total Pembayaran</span>
                        <span class="order-total-val">Rp <?= number_format($total_setelah_diskon, 0, ',', '.') ?></span>
                    </div>
                </div>

                <!-- Form Data Pemesan -->
                <form method="POST" id="formBayar">
                    <div class="bayar-card">
                        <div class="bayar-card-title">👤 Data Pemesan</div>
                        <div class="form-group-row">
                            <div class="form-group">
                                <label class="form-label">Nama Lengkap Pemesan</label>
                                <input type="text" class="tf-input" name="nama_pemesan" value="<?= htmlspecialchars($nama) ?>" required placeholder="Masukkan nama lengkap">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Nomor Telepon</label>
                                <input type="text" class="tf-input" name="no_telp" required placeholder="08123456789">
                            </div>
                        </div>
                    </div>

                    <!-- Pilih Metode -->
                    <div class="bayar-card">
                        <div class="bayar-card-title">🏦 Pilih Metode Pembayaran</div>
                        <div class="metode-grid">
                            <?php foreach ($rekening as $bank => $info): ?>
                            <label class="metode-item">
                                <input type="radio" name="metode" value="<?= $bank ?>" required>
                                <div class="metode-content">
                                    <div class="metode-bank"><?= $bank ?></div>
                                    <div class="metode-no"><?= $info['no'] ?></div>
                                    <div class="metode-nama">a.n <?= $info['atas_nama'] ?></div>
                                </div>
                                <div class="metode-check">✓</div>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Info Transfer -->
                    <div class="bayar-card transfer-info" id="infoTransfer" style="display:none">
                        <div class="bayar-card-title">📋 Instruksi Transfer</div>
                        <div class="transfer-steps">
                            <div class="transfer-step">
                                <div class="ts-num">1</div>
                                <div class="ts-text">Buka aplikasi mobile banking atau ATM bank yang dipilih</div>
                            </div>
                            <div class="transfer-step">
                                <div class="ts-num">2</div>
                                <div class="ts-text">Transfer ke nomor rekening: <strong id="noRek">-</strong> a.n <strong>TravelFlow Indonesia</strong></div>
                            </div>
                            <div class="transfer-step">
                                <div class="ts-num">3</div>
                                <div class="ts-text">Masukkan nominal tepat: <strong class="highlight">Rp <?= number_format($total_setelah_diskon, 0, ',', '.') ?></strong></div>
                            </div>
                            <div class="transfer-step">
                                <div class="ts-num">4</div>
                                <div class="ts-text">Klik tombol "Konfirmasi Pembayaran" di bawah setelah transfer selesai</div>
                            </div>
                        </div>
                    </div>

                    <button type="submit" name="konfirmasi" class="btn-konfirmasi" id="btnKonfirmasi">
                        Konfirmasi Pembayaran
                    </button>
                </form>

            </div>

            <!-- KANAN: Summary -->
            <div class="bayar-right">
                <div class="summary-card">
                    <div class="summary-title">💰 Total Tagihan</div>
                    <div class="summary-total">Rp <?= number_format($total_setelah_diskon, 0, ',', '.') ?></div>
                    <div class="summary-divider"></div>
                    <div class="summary-items">
                        <?php foreach ($items as $item): ?>
                        <div class="summary-item">
                            <span><?= htmlspecialchars($item['nama_wisata']) ?></span>
                            <span>Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></span>
                        </div>
                        <?php endforeach; ?>
                        <?php if ($promo_code && $promo_discount > 0): ?>
                        <div class="summary-item">
                            <span>Diskon <?= htmlspecialchars($promo_code) ?></span>
                            <span>-Rp <?= number_format($promo_discount, 0, ',', '.') ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="summary-divider"></div>
                    <div class="summary-note">
                        ⏰ Selesaikan pembayaran dalam <strong>24 jam</strong>
                    </div>
                    <div class="summary-note mt">
                        🔒 Transaksi aman & terenkripsi
                    </div>
                    <a href="keranjang.php" class="btn-kembali">← Kembali ke Keranjang</a>
                </div>
            </div>

        </div>
    </main>
</div>

<script>
const rekeningData = <?= json_encode($rekening) ?>;

document.querySelectorAll('input[name="metode"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const bank = this.value;
        document.getElementById('noRek').textContent = rekeningData[bank].no;
        document.getElementById('infoTransfer').style.display = 'block';

        // Highlight selected
        document.querySelectorAll('.metode-item').forEach(el => el.classList.remove('selected'));
        this.closest('.metode-item').classList.add('selected');
    });
});
</script>
</body>
</html>
