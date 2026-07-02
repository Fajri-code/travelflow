<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}
include '../config/koneksi.php';

function build_itinerary(array $items, int $duration): array
{
    $duration = max(1, $duration);
    $capacity = $duration * 2;
    $days = [];

    foreach ($items as $index => $item) {
        $day = min($duration, floor($index / 2) + 1);
        $days[$day][] = array_merge($item, ['day' => $day]);
    }

    return [
        'days' => $days,
        'capacity' => $capacity,
        'overflow' => count($items) > $capacity,
    ];
}

$nama    = $_SESSION['nama'];
$inisial = strtoupper(substr($nama, 0, 1));
$id_user = $_SESSION['user'];

// Init keranjang
if (!isset($_SESSION['keranjang'])) $_SESSION['keranjang'] = [];
if (!isset($_SESSION['journey_plan'])) {
    $_SESSION['journey_plan'] = [
        'tanggal' => date('Y-m-d'),
        'durasi' => 1,
        'hotel_id' => 0,
        'hotel_nama' => '',
        'hotel_harga' => 0,
        'pickup_type' => '',
        'pickup_detail' => '',
    ];
}
$travel_date = $_SESSION['journey_plan']['tanggal'] ?? date('Y-m-d');
$travel_duration = max(1, (int)($_SESSION['journey_plan']['durasi'] ?? 1));
$travel_notice = $_SESSION['travel_notice'] ?? '';
unset($_SESSION['travel_notice']);

// Hapus hotel
if (isset($_GET['hapus_hotel'])) {
    unset($_SESSION['hotel_terpilih']);
    header("Location: keranjang.php");
    exit;
}

// Kosongkan semua
if (isset($_GET['hapus_semua'])) {
    $_SESSION['keranjang'] = [];
    header("Location: keranjang.php");
    exit;
}

// Tambah ke keranjang
if (isset($_POST['tambah'])) {
    $id_wisata    = (int)$_POST['id_wisata'];
    $id_paket     = (int)$_POST['id_paket'];
    $id_kendaraan = (int)$_POST['id_kendaraan'];
    $jumlah_orang = (int)$_POST['jumlah_orang'];
    $tanggal      = mysqli_real_escape_string($conn, $_POST['tanggal'] ?? date('Y-m-d'));
    $durasi       = max(1, (int)($_POST['durasi_hari'] ?? 1));
    $hotel_id     = (int)($_POST['hotel_id'] ?? 0);
    $pickup_type  = mysqli_real_escape_string($conn, trim($_POST['pickup_type'] ?? ''));
    $pickup_detail= mysqli_real_escape_string($conn, trim($_POST['pickup_detail'] ?? ''));

    $wisata_data    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM wisata WHERE id_wisata=$id_wisata"));
    $paket_data     = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM paket WHERE id_paket=$id_paket"));
    $kendaraan_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM kendaraan WHERE id_kendaraan=$id_kendaraan"));

    $_SESSION['journey_plan'] = [
        'tanggal' => $tanggal,
        'durasi' => $durasi,
        'hotel_id' => $hotel_id,
        'pickup_type' => $pickup_type,
        'pickup_detail' => $pickup_detail,
    ];

    if ($hotel_id) {
        $hotel_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM hotel WHERE id_hotel=$hotel_id"));
        if ($hotel_data) {
            $_SESSION['hotel_terpilih'] = $hotel_data;
            $_SESSION['journey_plan']['hotel_nama'] = $hotel_data['nama_hotel'];
            $_SESSION['journey_plan']['hotel_harga'] = $hotel_data['harga_per_malam'];
        }
    } else {
        unset($_SESSION['hotel_terpilih']);
    }

    if ($wisata_data && $paket_data && $kendaraan_data) {
        $subtotal = ((int)$paket_data['harga'] * (int)$jumlah_orang) + (int)$kendaraan_data['harga'];
        $preview_items = $_SESSION['keranjang'];
        $preview_items[] = [
            'id_wisata'      => $id_wisata,
            'id_paket'       => $id_paket,
            'id_kendaraan'   => $id_kendaraan,
            'jumlah_orang'   => $jumlah_orang,
            'tanggal'        => $tanggal,
            'nama_wisata'    => $wisata_data['nama_wisata'],
            'gambar'         => $wisata_data['gambar'],
            'nama_paket'     => $paket_data['nama_paket'],
            'durasi_paket'   => $paket_data['durasi'],
            'harga_paket'    => $paket_data['harga'],
            'nama_kendaraan' => $kendaraan_data['nama_kendaraan'],
            'harga_kendaraan'=> $kendaraan_data['harga'],
            'subtotal'       => $subtotal,
        ];

        $itinerary = build_itinerary($preview_items, $durasi);
        if ($itinerary['overflow']) {
            $_SESSION['travel_notice'] = 'Jumlah destinasi melebihi kapasitas perjalanan. Silakan tambahkan jumlah hari atau kurangi destinasi wisata.';
            header("Location: keranjang.php");
            exit;
        }

        $_SESSION['keranjang'][] = [
            'id_wisata'      => $id_wisata,
            'id_paket'       => $id_paket,
            'id_kendaraan'   => $id_kendaraan,
            'jumlah_orang'   => $jumlah_orang,
            'tanggal'        => $tanggal,
            'nama_wisata'    => $wisata_data['nama_wisata'],
            'gambar'         => $wisata_data['gambar'],
            'nama_paket'     => $paket_data['nama_paket'],
            'durasi_paket'   => $paket_data['durasi'],
            'harga_paket'    => $paket_data['harga'],
            'nama_kendaraan' => $kendaraan_data['nama_kendaraan'],
            'harga_kendaraan'=> $kendaraan_data['harga'],
            'subtotal'       => $subtotal,
        ];
    }
    header("Location: keranjang.php");
    exit;
}

// Hapus item dari keranjang
if (isset($_GET['hapus'])) {
    $idx = (int)$_GET['hapus'];
    array_splice($_SESSION['keranjang'], $idx, 1);
    header("Location: keranjang.php");
    exit;
}

// Checkout → simpan ke session dulu, redirect ke pembayaran
if (isset($_POST['checkout'])) {
    if (!empty($_SESSION['keranjang'])) {
        $_SESSION['checkout_data'] = $_SESSION['keranjang'];
        $_SESSION['checkout_hotel'] = $_SESSION['hotel_terpilih'] ?? null;
        $_SESSION['checkout_journey'] = $_SESSION['journey_plan'] ?? [];
        header("Location: pembayaran.php");
        exit;
    }
}

// Ambil data form
$wisata_list    = mysqli_query($conn, "SELECT * FROM wisata");
$paket_list     = mysqli_query($conn, "SELECT p.*, w.nama_wisata FROM paket p JOIN wisata w ON p.id_wisata=w.id_wisata ORDER BY p.id_wisata");
$kendaraan_list = mysqli_query($conn, "SELECT * FROM kendaraan");

// Array paket per wisata untuk JS
$paket_per_wisata = [];
$res_paket = mysqli_query($conn, "SELECT * FROM paket");
while ($rp = mysqli_fetch_assoc($res_paket)) {
    $paket_per_wisata[$rp['id_wisata']][] = [
        'id' => $rp['id_paket'],
        'nama' => $rp['nama_paket'],
        'harga' => $rp['harga']
    ];
}

$kendaraan_js = [];
$res2 = mysqli_query($conn, "SELECT id_kendaraan, harga FROM kendaraan");
while ($r = mysqli_fetch_assoc($res2)) $kendaraan_js[$r['id_kendaraan']] = $r['harga'];

// Auto-select dari URL
$sel_wisata = isset($_GET['id_wisata']) ? (int)$_GET['id_wisata'] : 0;
$sel_paket  = isset($_GET['id_paket'])  ? (int)$_GET['id_paket']  : 0;
$sel_hotel  = isset($_GET['id_hotel'])  ? (int)$_GET['id_hotel']  : 0;

// Simpan hotel ke session
if ($sel_hotel) {
    $hotel_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM hotel WHERE id_hotel=$sel_hotel"));
    if ($hotel_data) $_SESSION['hotel_terpilih'] = $hotel_data;
}
$hotel_terpilih = $_SESSION['hotel_terpilih'] ?? null;

$total_keranjang = array_sum(array_column($_SESSION['keranjang'], 'subtotal'));
$total_akhir = $total_keranjang;

$promo_code = $_SESSION['promo_code'] ?? '';
$promo_discount = $_SESSION['promo_discount'] ?? 0;
$promo_message = $_SESSION['promo_message'] ?? '';
unset($_SESSION['promo_message']);

if (isset($_POST['apply_promo'])) {
    $entered = strtoupper(trim($_POST['promo_code'] ?? ''));
    $current_total = array_sum(array_column($_SESSION['keranjang'], 'subtotal'));
    $booking_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM transaksi WHERE id_user=$id_user"))['cnt'] ?? 0;

    if ($entered === 'JOGJA20' && $current_total > 0) {
        if ($booking_count == 0) {
            $_SESSION['promo_code'] = 'JOGJA20';
            $_SESSION['promo_discount'] = round($current_total * 0.2);
            $_SESSION['promo_message'] = 'Diskon 20% berhasil diterapkan untuk booking pertama!';
        } else {
            $_SESSION['promo_code'] = '';
            $_SESSION['promo_discount'] = 0;
            $_SESSION['promo_message'] = 'Promo hanya berlaku untuk booking pertama.';
        }
    } else {
        $_SESSION['promo_code'] = '';
        $_SESSION['promo_discount'] = 0;
        $_SESSION['promo_message'] = 'Kode promo tidak valid atau tidak ada item di keranjang.';
    }

    header("Location: keranjang.php");
    exit;
}

if (isset($_POST['clear_promo'])) {
    unset($_SESSION['promo_code'], $_SESSION['promo_discount'], $_SESSION['promo_message']);
    header("Location: keranjang.php");
    exit;
}

if ($promo_code && $promo_discount > 0) {
    $total_akhir = max(0, $total_keranjang - $promo_discount);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Keranjang Pemesanan - TravelFlow</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="../assets/css/keranjang.css" rel="stylesheet">
    <link href="../assets/css/sidebar.css" rel="stylesheet">
</head>
<body>

<!-- NAVBAR -->
<nav class="tf-navbar">
    <a href="dashboard.php" class="brand">
        <div class="brand-icon">✈</div>
        TravelFlow
    </a>
    <div class="nav-right">
        <a href="keranjang.php" class="cart-btn">
            🛒 Keranjang
            <?php if (!empty($_SESSION['keranjang'])): ?>
            <span class="cart-badge"><?= count($_SESSION['keranjang']) ?></span>
            <?php endif; ?>
        </a>
        <div class="nav-user" title="<?= htmlspecialchars($nama) ?>"><?= $inisial ?></div>
    </div>
</nav>

<div class="tf-layout">
    <!-- SIDEBAR -->
    <aside class="tf-sidebar">
        <a href="dashboard.php" class="sidebar-item ">
            <span class="si-icon"></span> Dashboard
        </a>
          <a href="hotel.php" class="sidebar-item "><span class="si-icon"></span> Opsional: Penjemputan</a>
        <a href="wisata.php" class="sidebar-item ">
            <span class="si-icon"></span> Daftar Wisata
        </a>
        <a href="keranjang.php" class="sidebar-item active">
            <span class="si-icon"></span> Keranjang
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
        <div class="keranjang-header">
            <img class="keranjang-hero-img" src="../assets/img/wisata/bag.png" alt="" onerror="this.style.display='none'">
            <div class="keranjang-header-overlay"></div>
            <div class="keranjang-header-content">
                <div class="keranjang-badge"> Keranjang Wisata</div>
                <h1>Keranjang Pemesanan</h1>
                <p>Tambah destinasi wisata ke keranjang, lalu lanjutkan ke pembayaran</p>
            </div>
            <div class="keranjang-header-img"></div>
        </div>

        <div class="keranjang-layout">

            <!-- KIRI: Form Tambah + Keranjang -->
            <div class="keranjang-left">

                <!-- Info Rencana Perjalanan -->
                <div class="hotel-info-card">
                    <div class="hotel-info-icon">🧭</div>
                    <div class="hotel-info-content">
                        <div class="hotel-info-label">Rencana perjalanan Anda</div>
                        <div class="hotel-info-nama">Tanggal berangkat: <?= date('d M Y', strtotime($travel_date)) ?></div>
                        <div class="hotel-info-alamat">Durasi: <?= $travel_duration ?> hari • Hotel: <?= !empty($hotel_terpilih) ? htmlspecialchars($hotel_terpilih['nama_hotel']) : 'Tidak memilih hotel' ?></div>
                        <div style="margin-top:6px;font-size:.82rem;color:#64748b;">Hotel dan titik penjemputan adalah bagian dari rencana perjalanan. Anda bisa tetap melanjutkan tanpa memilih hotel.</div>
                    </div>
                </div>

                <div class="journey-progress">
                    <div class="journey-step active">1. Pilih destinasi</div>
                    <div class="journey-step active">2. Atur perjalanan</div>
                    <div class="journey-step">3. Review & bayar</div>
                </div>

                <div class="flow-help-card">
                    <div class="flow-help-title">Cara kerja halaman ini</div>
                    <div class="flow-help-list">
                        <div class="flow-help-item">
                            <span>1</span>
                            <div><strong>Pilih wisata & paket</strong><p>Masukkan destinasi, paket, dan jumlah orang.</p></div>
                        </div>
                        <div class="flow-help-item">
                            <span>2</span>
                            <div><strong>Atur perjalanan</strong><p>Tentukan tanggal, durasi, hotel, dan titik penjemputan.</p></div>
                        </div>
                        <div class="flow-help-item">
                            <span>3</span>
                            <div><strong>Lihat itinerary & bayar</strong><p>Sistem akan mengatur perjalanan Anda per hari sebelum checkout.</p></div>
                        </div>
                    </div>
                </div>

                <!-- Form Tambah Wisata -->
                <div class="form-card">
                    <div class="form-card-header">
                        <span class="form-card-header-icon"></span>
                        <div class="form-card-title">Tambah Destinasi ke Keranjang</div>
                    </div>
                    <div class="form-card-body">
                    <form method="POST">

                        <!-- Destinasi & Paket -->
                        <div class="form-section">
                            <div class="form-section-label">Langkah 1 · Pilih Destinasi & Paket</div>
                            <div class="form-group-row">
                                <div class="form-group">
                                    <label class="form-label">Destinasi Wisata</label>
                                    <?php if ($sel_wisata): ?>
                                        <?php $wisata_sel = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM wisata WHERE id_wisata=$sel_wisata")); ?>
                                        <input type="text" class="tf-input" value="<?= htmlspecialchars($wisata_sel['nama_wisata'] ?? '') ?>" readonly style="background:#f8fafc;color:#64748b;cursor:not-allowed">
                                        <input type="hidden" name="id_wisata" value="<?= $sel_wisata ?>">
                                    <?php else: ?>
                                        <select class="tf-select" name="id_wisata" id="sel_wisata" required onchange="filterPaket(this.value)">
                                            <option value=""> Pilih Wisata </option>
                                            <?php while ($w = mysqli_fetch_assoc($wisata_list)): ?>
                                            <option value="<?= $w['id_wisata'] ?>">
                                                <?= htmlspecialchars($w['nama_wisata']) ?>
                                            </option>
                                            <?php endwhile; ?>
                                        </select>
                                    <?php endif; ?>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Paket Wisata</label>
                                    <select class="tf-select" name="id_paket" id="sel_paket" required onchange="hitungSubtotal()">
                                        <option value=""> Pilih Paket </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Kendaraan & Peserta -->
                        <div class="form-section">
                            <div class="form-section-label">Langkah 2 · Kendaraan & Peserta</div>
                            <div class="form-group-row">
                                <div class="form-group">
                                    <label class="form-label">Kendaraan</label>
                                    <select class="tf-select" name="id_kendaraan" id="sel_kendaraan" required onchange="hitungSubtotal()">
                                        <option value=""> Pilih Kendaraan </option>
                                        <?php while ($k = mysqli_fetch_assoc($kendaraan_list)): ?>
                                        <option value="<?= $k['id_kendaraan'] ?>">
                                            <?= htmlspecialchars($k['nama_kendaraan']) ?> — Rp <?= number_format($k['harga'], 0, ',', '.') ?>
                                        </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Jumlah Orang</label>
                                    <input type="number" class="tf-input" name="jumlah_orang" id="jumlah_orang" min="1" max="40" value="1" required oninput="hitungSubtotal(); rekomendasiKendaraan(this.value)">
                                    <small class="rekomendasi-text" id="rekomendasi_text"></small>
                                </div>
                            </div>
                        </div>

                        <!-- Jadwal & Perjalanan -->
                        <div class="form-section">
                            <div class="form-section-label">Langkah 3 · Susun Rencana Perjalanan</div>
                            <div class="form-group-row">
                                <div class="form-group">
                                    <label class="form-label">Tanggal Berangkat</label>
                                    <input type="date" class="tf-input" name="tanggal" min="<?= date('Y-m-d') ?>" value="<?= htmlspecialchars($travel_date) ?>" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Durasi Perjalanan</label>
                                    <select class="tf-select" name="durasi_hari" required>
                                        <?php for ($d = 1; $d <= 5; $d++): ?>
                                            <option value="<?= $d ?>" <?= $travel_duration == $d ? 'selected' : '' ?>><?= $d ?> hari</option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group-row">
                                <div class="form-group">
                                    <label class="form-label">Hotel (Opsional)</label>
                                    <select class="tf-select" name="hotel_id">
                                        <option value="0">Tidak memilih hotel</option>
                                        <?php
                                        $hotel_list = mysqli_query($conn, "SELECT id_hotel, nama_hotel, harga_per_malam FROM hotel ORDER BY bintang DESC, harga_per_malam ASC");
                                        while ($h = mysqli_fetch_assoc($hotel_list)):
                                            $selected_hotel_option = (!empty($_SESSION['hotel_terpilih']) && $_SESSION['hotel_terpilih']['id_hotel'] == $h['id_hotel']) ? 'selected' : '';
                                        ?>
                                            <option value="<?= $h['id_hotel'] ?>" <?= $selected_hotel_option ?>><?= htmlspecialchars($h['nama_hotel']) ?> — Rp <?= number_format($h['harga_per_malam'], 0, ',', '.') ?>/malam</option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Titik Penjemputan</label>
                                    <select class="tf-select" name="pickup_type">
                                        <option value="">Pilih titik penjemputan</option>
                                        <?php $pickup_options = ['Hotel', 'Bandara YIA', 'Stasiun Tugu', 'Stasiun Lempuyangan', 'Terminal Giwangan', 'Alamat Saya']; ?>
                                        <?php foreach ($pickup_options as $option): ?>
                                            <option value="<?= htmlspecialchars($option) ?>" <?= ($option === ($_SESSION['journey_plan']['pickup_type'] ?? '')) ? 'selected' : '' ?>><?= htmlspecialchars($option) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Detail Lokasi Penjemputan</label>
                                <input type="text" class="tf-input" name="pickup_detail" placeholder="Isi alamat jika pilih Alamat Saya atau detail tambahan" value="<?= htmlspecialchars($_SESSION['journey_plan']['pickup_detail'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <small class="rekomendasi-text">Catatan: hotel dan titik penjemputan adalah bagian dari rencana perjalanan, bukan biaya wisata utama. Anda tetap bisa lanjut tanpa memilih hotel.</small>
                            </div>
                        </div>

                        <?php if ($travel_notice): ?>
                        <div class="promo-message" style="margin-bottom:12px;"><?= htmlspecialchars($travel_notice) ?></div>
                        <?php endif; ?>

                        <!-- Subtotal Preview -->
                        <div class="subtotal-preview">
                            <div class="subtotal-preview-left">Subtotal item ini:</div>
                            <span class="subtotal-val" id="preview_subtotal">Rp 0</span>
                        </div>

                        <button type="submit" name="tambah" class="btn-tambah">
                            <span></span> Tambah ke Keranjang
                        </button>
                    </form>
                    </div>
                </div>

                <!-- Itinerary & Daftar Keranjang -->
                <?php if (!empty($_SESSION['keranjang'])): ?>
                <div class="keranjang-list">
                    <div class="keranjang-list-title">Itinerary Perjalanan</div>
                    <?php $itinerary = build_itinerary($_SESSION['keranjang'], $travel_duration); ?>
                    <?php foreach ($itinerary['days'] as $day => $day_items): ?>
                    <div style="padding:10px 0;border-bottom:1px solid #e2e8f0;">
                        <div style="font-weight:700;color:#1e293b;margin-bottom:6px">Hari <?= $day ?> — <?= date('d M Y', strtotime($travel_date . ' +' . ($day - 1) . ' days')) ?></div>
                        <div style="display:flex;flex-direction:column;gap:6px">
                            <?php foreach ($day_items as $day_item): ?>
                            <div style="font-size:.9rem;color:#475569">• <?= htmlspecialchars($day_item['nama_wisata']) ?></div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="keranjang-list">
                    <div class="keranjang-list-title">Wisata dalam Keranjang (<?= count($_SESSION['keranjang']) ?> item)</div>
                    <?php foreach ($_SESSION['keranjang'] as $idx => $item): ?>
                    <div class="keranjang-item">
                        <div class="item-img">
                            <?php if (!empty($item['gambar'])): ?>
                                <img src="../assets/img/wisata/<?= htmlspecialchars($item['gambar']) ?>" alt="">
                            <?php else: ?>
                                <div class="item-img-placeholder"></div>
                            <?php endif; ?>
                        </div>
                        <div class="item-info">
                            <div class="item-wisata"><?= htmlspecialchars($item['nama_wisata']) ?></div>
                            <div class="item-detail">
                                 <?= htmlspecialchars($item['nama_paket']) ?>
                                <?php if (!empty($item['durasi_paket'])): ?>
                                    (<?= htmlspecialchars($item['durasi_paket']) ?>)
                                <?php endif; ?>
                                &nbsp;•&nbsp;
                                 <?= htmlspecialchars($item['nama_kendaraan']) ?> &nbsp;•&nbsp;
                                 <?= $item['jumlah_orang'] ?> orang
                            </div>
                            <div class="item-tanggal"> <?= date('d M Y', strtotime($item['tanggal'])) ?></div>
                        </div>
                        <div class="item-right">
                            <div class="item-subtotal">Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></div>
                            <a href="keranjang.php?hapus=<?= $idx ?>" class="btn-hapus" onclick="return confirm('Hapus item ini?')">🗑 Hapus</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="keranjang-empty">
                    <div class="empty-icon"></div>
                    <h5>Keranjang masih kosong</h5>
                    <p>Tambahkan destinasi wisata di atas</p>
                </div>
                <?php endif; ?>

            </div>

            <!-- KANAN: Summary Checkout -->
            <div class="keranjang-right">
                <div class="checkout-card">
                    <div class="checkout-title">Ringkasan Perjalanan & Pembayaran</div>
                    <div class="checkout-help-text">Setelah semua data lengkap, Anda bisa langsung lanjut ke pembayaran.</div>

                    <?php if (!empty($_SESSION['keranjang'])): ?>
                        <?php foreach ($_SESSION['keranjang'] as $item): ?>
                        <div class="checkout-item">
                            <span class="checkout-item-name"><?= htmlspecialchars($item['nama_wisata']) ?></span>
                            <span class="checkout-item-price">Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></span>
                        </div>
                        <?php endforeach; ?>

                        <form method="POST" class="promo-form">
                            <label class="promo-label" for="promo_code">Kode Promo</label>
                            <div class="promo-row">
                                <input type="text" name="promo_code" id="promo_code" class="tf-input" placeholder="Masukkan Kode Promo" value="<?= htmlspecialchars($promo_code) ?>">
                                <button type="submit" name="apply_promo" class="btn-promo-apply">Terapkan</button>
                            </div>
                            <?php if ($promo_message): ?>
                                <div class="promo-message"><?= htmlspecialchars($promo_message) ?></div>
                            <?php endif; ?>
                            <?php if ($promo_code && $promo_discount > 0): ?>
                                <button type="submit" name="clear_promo" class="btn-kosongkan" style="margin-top:10px;">❌ Hapus Promo</button>
                            <?php endif; ?>
                        </form>

                        <?php if ($promo_code && $promo_discount > 0): ?>
                            <div class="checkout-item">
                                <span class="checkout-item-name">Diskon <?= htmlspecialchars($promo_code) ?></span>
                                <span class="checkout-item-price">-Rp <?= number_format($promo_discount, 0, ',', '.') ?></span>
                            </div>
                        <?php endif; ?>

                        <div class="checkout-divider"></div>
                        <div class="checkout-item">
                            <span class="checkout-item-name">Tanggal Berangkat</span>
                            <span class="checkout-item-price"><?= date('d M Y', strtotime($travel_date)) ?></span>
                        </div>
                        <div class="checkout-item">
                            <span class="checkout-item-name">Durasi</span>
                            <span class="checkout-item-price"><?= $travel_duration ?> hari</span>
                        </div>
                        <div class="checkout-item">
                            <span class="checkout-item-name">Hotel</span>
                            <span class="checkout-item-price"><?= !empty($_SESSION['hotel_terpilih']) ? htmlspecialchars($_SESSION['hotel_terpilih']['nama_hotel']) : 'Tidak ada' ?></span>
                        </div>
                        <div class="checkout-item">
                            <span class="checkout-item-name">Penjemputan</span>
                            <span class="checkout-item-price"><?= !empty($_SESSION['journey_plan']['pickup_type']) ? htmlspecialchars($_SESSION['journey_plan']['pickup_type']) : 'Belum dipilih' ?></span>
                        </div>
                        <div class="checkout-divider"></div>
                        <div class="checkout-total-row">
                            <span>TOTAL PEMBAYARAN</span>
                            <span class="checkout-total-val">Rp <?= number_format($total_akhir, 0, ',', '.') ?></span>
                        </div>
                        <div class="checkout-divider"></div>

                        <form method="POST">
                            <button type="submit" name="checkout" class="btn-checkout">
                                🎫 Checkout Semua (<?= count($_SESSION['keranjang']) ?> item)
                            </button>
                        </form>
                        <a href="keranjang.php?hapus_semua=1" class="btn-kosongkan"
                           onclick="return confirm('Kosongkan semua keranjang?')">
                            🗑 Kosongkan Keranjang
                        </a>
                    <?php else: ?>
                        <div class="checkout-empty">
                            <p>Belum ada item di keranjang</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </main>
</div>

<script>
const paketPerWisata = <?= json_encode($paket_per_wisata) ?>;
const kendaraanHarga = <?= json_encode($kendaraan_js) ?>;

// FILTER PAKET BERDASARKAN WISATA
function filterPaket(idWisata) {
    const sel = document.getElementById('sel_paket');

    sel.innerHTML = '<option value="">-- Pilih Paket --</option>';

    if (!idWisata || !paketPerWisata[idWisata]) return;

    paketPerWisata[idWisata].forEach(p => {
        const opt = document.createElement('option');

        opt.value = p.id;
        opt.textContent =
            p.nama + ' — Rp ' + parseInt(p.harga).toLocaleString('id-ID');

        opt.dataset.harga = p.harga;

        sel.appendChild(opt);
    });

    hitungSubtotal();
}

// REKOMENDASI KENDARAAN
function rekomendasiKendaraan(jumlah) {
    jumlah = parseInt(jumlah) || 0;

    const sel = document.getElementById('sel_kendaraan');
    const txt = document.getElementById('rekomendasi_text');

    let rekomendasiNama = '';

    if (jumlah >= 1 && jumlah <= 2) {
        rekomendasiNama = 'Motor';
    } else if (jumlah <= 4) {
        rekomendasiNama = 'Mobil';
    } else if (jumlah <= 10) {
        rekomendasiNama = 'Minibus';
    } else if (jumlah <= 20) {
        rekomendasiNama = 'Bus Medium';
    } else {
        rekomendasiNama = 'Bus';
    }

    for (let opt of sel.options) {
        if (opt.text.includes(rekomendasiNama)) {
            sel.value = opt.value;
            break;
        }
    }

    txt.textContent =
        jumlah > 0 ? '💡 Rekomendasi: ' + rekomendasiNama : '';

    hitungSubtotal();
}

// HITUNG SUBTOTAL
function hitungSubtotal() {

    const selPaket = document.getElementById('sel_paket');

    const idKendaraan =
        document.getElementById('sel_kendaraan').value;

    const jumlah =
        parseInt(document.getElementById('jumlah_orang').value) || 0;

    const hargaPaket =
        selPaket.value
            ? parseInt(
                selPaket.options[selPaket.selectedIndex]
                ?.dataset?.harga || 0
              )
            : 0;

    const hargaKendaraan =
        idKendaraan
            ? parseInt(kendaraanHarga[idKendaraan] || 0)
            : 0;

    const subtotal =
        (hargaPaket * jumlah) + hargaKendaraan;

    document.getElementById('preview_subtotal').textContent =
        'Rp ' + subtotal.toLocaleString('id-ID');
}

// EVENT
document.querySelectorAll(
    '#sel_paket, #sel_kendaraan, #jumlah_orang'
).forEach(el => {

    el.addEventListener('change', hitungSubtotal);

    el.addEventListener('input', hitungSubtotal);

});

// AUTO SELECT DARI URL
const selWisataEl = document.getElementById('sel_wisata');

const idWisataVal = <?= $sel_wisata ?>;

if (idWisataVal) {

    filterPaket(idWisataVal);

    const selPaketVal = <?= $sel_paket ?>;

    if (selPaketVal) {

        setTimeout(() => {

            document.getElementById('sel_paket').value =
                selPaketVal;

            hitungSubtotal();

        }, 100);
    }

}
</script>
</body>
</html>
