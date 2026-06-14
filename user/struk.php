<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}
include '../config/koneksi.php';

$id_user      = (int)$_SESSION['user'];
$id_transaksi = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$data = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT t.*,
           w.nama_wisata, w.lokasi, w.gambar,
           p.nama_paket, p.harga as harga_paket,
           k.nama_kendaraan, k.harga as harga_kendaraan,
           u.nama as nama_user, u.email
    FROM transaksi t
    JOIN wisata w ON t.id_wisata = w.id_wisata
    JOIN paket p ON t.id_paket = p.id_paket
    JOIN kendaraan k ON t.id_kendaraan = k.id_kendaraan
    JOIN users u ON t.id_user = u.id_user
    WHERE t.id_transaksi = $id_transaksi 
    AND t.id_user = $id_user
"));

if (!$data) {
    echo "<script>alert('Transaksi tidak ditemukan!'); window.location='riwayat.php';</script>";
    exit;
}

$kode = !empty($data['kode_booking']) ? $data['kode_booking'] : 'TF-' . str_pad($id_transaksi, 8, '0', STR_PAD_LEFT);
$nama_pemesan = !empty($data['nama_pemesan']) ? $data['nama_pemesan'] : $data['nama_user'];
$status = $data['status'] ?? 'confirmed';
$status_color = $status == 'confirmed' ? '#16a34a' : ($status == 'pending' ? '#d97706' : '#dc2626');
$status_bg    = $status == 'confirmed' ? '#dcfce7' : ($status == 'pending' ? '#fef3c7' : '#fee2e2');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Struk #<?= $kode ?> - TravelFlow</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="../assets/css/struk.css?v=3" rel="stylesheet">
</head>
<body>

<div class="no-print action-bar">
    <a href="riwayat.php" class="btn-back">Kembali</a>
    <button onclick="window.print()" class="btn-print"> Cetak / Download PDF</button>
</div>

<div class="struk-wrapper">

    <!-- Header -->
    <div class="struk-header">
        <img class="struk-header-img" src="../assets/img/wisata/bag.png" alt="" onerror="this.style.display='none'">
        <div class="struk-header-overlay"></div>
        <div class="struk-brand">
            <div class="brand-icon">✈</div>
            <div>
                <div class="brand-name">TravelFlow</div>
                <div class="brand-sub">E-Ticket & Invoice</div>
            </div>
        </div>
        <div class="struk-kode-wrap">
            <div class="struk-kode-label">Kode Booking</div>
            <div class="struk-kode"><?= $kode ?></div>
            <div class="struk-tanggal"><?= date('d M Y, H:i', strtotime($data['tanggal'])) ?> WIB</div>
        </div>
    </div>

    <!-- Status Banner -->
    <div class="status-banner" style="background:<?= $status_bg ?>; color:<?= $status_color ?>">
        <?php if ($status == 'confirmed'): ?>
            ✅ Pembayaran Dikonfirmasi — Tiket Aktif
        <?php elseif ($status == 'pending'): ?>
            ⏳ Menunggu Konfirmasi Pembayaran
        <?php else: ?>
            ❌ Transaksi Dibatalkan
        <?php endif; ?>
    </div>

    <div class="struk-body">

        <!-- Info Pemesan -->
        <div class="struk-section">
            <div class="section-title">👤 Data Pemesan</div>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Nama Pemesan</div>
                    <div class="info-val"><?= htmlspecialchars($nama_pemesan) ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Email</div>
                    <div class="info-val"><?= htmlspecialchars($data['email']) ?></div>
                </div>
                <?php if (!empty($data['no_telp'])): ?>
                <div class="info-item">
                    <div class="info-label">No. Telepon</div>
                    <div class="info-val"><?= htmlspecialchars($data['no_telp']) ?></div>
                </div>
                <?php endif; ?>
                <div class="info-item">
                    <div class="info-label">Jumlah Peserta</div>
                    <div class="info-val"><?= $data['jumlah_orang'] ?> orang</div>
                </div>
            </div>
        </div>

        <div class="struk-divider"></div>

        <!-- Detail Perjalanan -->
        <div class="struk-section">
            <div class="section-title">🏝 Detail Perjalanan</div>
            <div class="perjalanan-card">
                <div class="perjalanan-left">
                    <?php if (!empty($data['gambar'] ?? '')): ?>
                        <img src="../assets/img/wisata/<?= htmlspecialchars($data['gambar'] ?? '') ?>" alt="" class="perjalanan-img">
                    <?php else: ?>
                        <div class="perjalanan-img-placeholder">🌏</div>
                    <?php endif; ?>
                </div>
                <div class="perjalanan-right">
                    <div class="perjalanan-wisata"><?= htmlspecialchars($data['nama_wisata']) ?></div>
                    <div class="perjalanan-lokasi">📍 <?= htmlspecialchars($data['lokasi']) ?></div>
                    <div class="perjalanan-detail">
                        <span class="detail-badge">📦 <?= htmlspecialchars($data['nama_paket']) ?></span>
                        <span class="detail-badge">🚌 <?= htmlspecialchars($data['nama_kendaraan']) ?></span>
                        <span class="detail-badge">📅 <?= date('d M Y', strtotime($data['tanggal'])) ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="struk-divider"></div>

        <!-- Rincian Biaya -->
        <div class="struk-section">
            <div class="section-title">💰 Rincian Biaya</div>
            <table class="biaya-table">
                <tr>
                    <td>Harga Paket</td>
                    <td><?= $data['jumlah_orang'] ?> × Rp <?= number_format($data['harga_paket'], 0, ',', '.') ?></td>
                    <td class="text-right">Rp <?= number_format($data['harga_paket'] * $data['jumlah_orang'], 0, ',', '.') ?></td>
                </tr>
                <tr>
                    <td>Sewa Kendaraan</td>
                    <td><?= htmlspecialchars($data['nama_kendaraan']) ?></td>
                    <td class="text-right">Rp <?= number_format($data['harga_kendaraan'], 0, ',', '.') ?></td>
                </tr>
                <tr>
                    <td>Biaya Layanan</td>
                    <td>TravelFlow Service Fee</td>
                    <td class="text-right">Rp 0</td>
                </tr>
            </table>
            <div class="total-box">
                <span>TOTAL PEMBAYARAN</span>
                <span class="total-val">Rp <?= number_format($data['total'], 0, ',', '.') ?></span>
            </div>
        </div>

        <div class="struk-divider dashed"></div>

        <!-- Barcode simulasi -->
        <div class="barcode-section">
            <div class="barcode-wrap">
                <div class="barcode-bars">
                    <?php for ($i = 0; $i < 40; $i++): ?>
                    <div class="bar" style="height:<?= rand(20,45) ?>px; width:<?= rand(1,3) ?>px"></div>
                    <?php endfor; ?>
                </div>
                <div class="barcode-text"><?= $kode ?></div>
            </div>
            <div class="barcode-note">Tunjukkan kode ini kepada pemandu wisata</div>
        </div>

        <div class="struk-footer">
            <p>Terima kasih telah mempercayai <strong>TravelFlow</strong>! 🎉</p>
            <p>Selamat menikmati perjalanan Anda ke <?= htmlspecialchars($data['nama_wisata']) ?>.</p>
        </div>

    </div>
</div>

</body>
</html>
