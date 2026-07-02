<?php
session_start();
if (!isset($_SESSION['user'])) { header("Location: ../auth/login.php"); exit; }
if (empty($_SESSION['perjalanan'])) { header("Location: rencana.php"); exit; }
if (empty($_SESSION['perjalanan']['titik_jemput'])) { header("Location: penjemputan.php"); exit; }
include '../config/koneksi.php';
include '../config/notif.php';

$nama    = $_SESSION['nama'];
$inisial = strtoupper(substr($nama, 0, 1));
$id_user = $_SESSION['user'];
$p       = $_SESSION['perjalanan'];

// Ambil data kendaraan
$kendaraan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM kendaraan WHERE id_kendaraan=" . (int)$p['id_kendaraan']));

// Ambil data hotel
$hotel = null;
if (!empty($p['id_hotel'])) {
    $hotel = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM hotel WHERE id_hotel=" . (int)$p['id_hotel']));
}

// Ambil data wisata per itinerary + paket yang dipilih user
$wisata_data = [];
$id_wisata_list = array_unique(array_column($p['itinerary'], 'id_wisata'));
foreach ($id_wisata_list as $id_w) {
    $w = mysqli_fetch_assoc(mysqli_query($conn, "SELECT w.*, MIN(pk.harga) as harga_mulai FROM wisata w LEFT JOIN paket pk ON w.id_wisata=pk.id_wisata WHERE w.id_wisata=$id_w GROUP BY w.id_wisata"));
    if ($w) $wisata_data[$id_w] = $w;
}

// Ambil harga paket yang dipilih per itinerary item
foreach ($p['itinerary'] as &$item) {
    if (!empty($item['id_paket'])) {
        $pk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT harga, nama_paket FROM paket WHERE id_paket=" . (int)$item['id_paket']));
        if ($pk) {
            $item['harga_paket']  = (int)$pk['harga'];
            $item['nama_paket']   = $pk['nama_paket'];
        }
    }
    if (empty($item['harga_paket'])) {
        $item['harga_paket'] = (int)($wisata_data[$item['id_wisata']]['harga_mulai'] ?? 0);
        $item['nama_paket']  = 'Paket Standar';
    }
}
unset($item);

// Hitung biaya
$biaya_wisata = 0;
foreach ($p['itinerary'] as $item) {
    $biaya_wisata += (int)($item['harga_paket'] ?? $wisata_data[$item['id_wisata']]['harga_mulai'] ?? 0) * (int)$p['jumlah_peserta'];
}
$biaya_kendaraan = (int)($kendaraan['harga'] ?? 0) * (int)$p['jumlah_hari'];
$kapasitas_kamar = 2;
$jumlah_kamar    = $hotel ? max(1, ceil((int)$p['jumlah_peserta'] / $kapasitas_kamar)) : 0;
$biaya_hotel     = $hotel ? (int)$hotel['harga_per_malam'] * (int)$p['jumlah_malam'] * $jumlah_kamar : 0;
$total           = $biaya_wisata + $biaya_kendaraan + $biaya_hotel;

// Info kamar detail
$kamar_detail = '';
if ($hotel && $jumlah_kamar > 0) {
    $sisa = (int)$p['jumlah_peserta'] % $kapasitas_kamar;
    $parts = [];
    for ($ki = 1; $ki <= $jumlah_kamar; $ki++) {
        $isi = ($ki == $jumlah_kamar && $sisa > 0) ? $sisa : $kapasitas_kamar;
        $parts[] = 'Kamar ' . $ki . ': ' . $isi . ' orang';
    }
    $kamar_detail = implode(' | ', $parts);
}

// Konfirmasi → simpan ke DB dan lanjut ke pembayaran
if (isset($_POST['konfirmasi'])) {
    // Simpan perjalanan
    $tgl    = mysqli_real_escape_string($conn, $p['tanggal_berangkat']);
    $durasi = mysqli_real_escape_string($conn, $p['durasi']);
    $jemput = mysqli_real_escape_string($conn, $p['titik_jemput'] . ($p['detail_jemput'] ? ' - ' . $p['detail_jemput'] : ''));

    mysqli_query($conn, "INSERT INTO perjalanan (id_user, tanggal_berangkat, durasi, jumlah_hari, jumlah_malam, id_kendaraan, jumlah_peserta, id_hotel, titik_jemput, status)
        VALUES ($id_user, '$tgl', '$durasi', {$p['jumlah_hari']}, {$p['jumlah_malam']}, {$p['id_kendaraan']}, {$p['jumlah_peserta']}, " . ($p['id_hotel'] ?: 'NULL') . ", '$jemput', 'draft')");
    $id_perjalanan = mysqli_insert_id($conn);

    // Simpan itinerary
    foreach ($p['itinerary'] as $item) {
        mysqli_query($conn, "INSERT INTO itinerary (id_perjalanan, hari, id_wisata, urutan) VALUES ($id_perjalanan, {$item['hari']}, {$item['id_wisata']}, {$item['urutan']})");
    }

    // Siapkan checkout_data untuk pembayaran (1 transaksi per wisata)
    $checkout_items = [];
    foreach ($p['itinerary'] as $item) {
        $w = $wisata_data[$item['id_wisata']] ?? null;
        if (!$w) continue;
        $subtotal = (int)($item['harga_paket'] ?? $w['harga_mulai']) * (int)$p['jumlah_peserta'];
        $checkout_items[] = [
            'id_wisata'      => $item['id_wisata'],
            'id_paket'       => $item['id_paket'] ?? $w['id_paket_termurah'] ?? 0,
            'id_kendaraan'   => $p['id_kendaraan'],
            'jumlah_orang'   => $p['jumlah_peserta'],
            'tanggal'        => $p['tanggal_berangkat'],
            'nama_wisata'    => $w['nama_wisata'],
            'gambar'         => $w['gambar'] ?? '',
            'nama_paket'     => $item['nama_paket'] ?? 'Paket Standar',
            'durasi_paket'   => '',
            'harga_paket'    => $item['harga_paket'] ?? $w['harga_mulai'],
            'nama_kendaraan' => $kendaraan['nama_kendaraan'],
            'harga_kendaraan'=> $kendaraan['harga'],
            'subtotal'       => $subtotal,
        ];
    }

    // Tambahkan biaya kendaraan & hotel ke item pertama
    if (!empty($checkout_items)) {
        $checkout_items[0]['subtotal'] += $biaya_kendaraan + $biaya_hotel;
    }

    $_SESSION['checkout_data']    = $checkout_items;
    $_SESSION['checkout_hotel']   = $hotel;
    $_SESSION['checkout_journey'] = [
        'tanggal'       => $p['tanggal_berangkat'],
        'durasi'        => $p['jumlah_hari'],
        'pickup_type'   => $p['titik_jemput'],
        'pickup_detail' => $p['detail_jemput'],
    ];
    $_SESSION['id_perjalanan_aktif'] = $id_perjalanan;
    unset($_SESSION['perjalanan']);

    header("Location: pembayaran.php");
    exit;
}

// Susun itinerary per hari
$hari_map = [];
foreach ($p['itinerary'] as $item) {
    $hari_map[$item['hari']][] = $wisata_data[$item['id_wisata']]['nama_wisata'] ?? 'Wisata';
}
ksort($hari_map);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Ringkasan Perjalanan – TravelFlow</title>
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
      <div class="flow-step done"><div class="step-num">✓</div> Penginapan</div>
      <div class="flow-step-line done"></div>
      <div class="flow-step done"><div class="step-num">✓</div> Penjemputan</div>
      <div class="flow-step-line done"></div>
      <div class="flow-step active"><div class="step-num">6</div> Ringkasan</div>
    </div>

    <div class="flow-header">
      <img class="flow-header-img" src="../assets/img/wisata/bag.png" alt="" onerror="this.style.display='none'">
      <div class="flow-header-bg"></div>
      <div class="flow-header-content">
        <div class="flow-header-badge">🎫 Langkah 6 dari 6</div>
        <h1>Ringkasan Perjalanan</h1>
        <p>Periksa kembali semua detail sebelum melanjutkan ke pembayaran</p>
      </div>
    </div>

    <div class="ringkasan-layout">

      <!-- KIRI: Detail -->
      <div>

        <!-- Itinerary -->
        <div class="flow-card">
          <div class="flow-card-title">🗺 Itinerary Perjalanan</div>
          <?php foreach ($hari_map as $hari => $wisata_list): ?>
          <div class="ringkasan-hari">
            <div class="ringkasan-hari-label">
              Hari <?= $hari ?> — <?= date('d M Y', strtotime($p['tanggal_berangkat'] . ' +' . ($hari-1) . ' days')) ?>
            </div>
            <?php foreach ($wisata_list as $i => $nama_w): ?>
            <div class="ringkasan-hari-item">
              <span style="font-size:.7rem;background:var(--primary);color:#fff;border-radius:50%;width:18px;height:18px;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-weight:800;"><?= $i+1 ?></span>
              <?= htmlspecialchars($nama_w) ?>
            </div>
            <?php endforeach; ?>
          </div>
          <?php endforeach; ?>
        </div>

        <!-- Info Perjalanan -->
        <div class="flow-card">
          <div class="flow-card-title">📋 Detail Perjalanan</div>
          <div class="ringkasan-section">
            <div class="ringkasan-section-title">Jadwal</div>
            <div class="ringkasan-row">
              <span class="ringkasan-row-label">Tanggal Berangkat</span>
              <span class="ringkasan-row-val"><?= date('d M Y', strtotime($p['tanggal_berangkat'])) ?></span>
            </div>
            <div class="ringkasan-row">
              <span class="ringkasan-row-label">Durasi</span>
              <span class="ringkasan-row-val"><?= $p['jumlah_hari'] ?> Hari <?= $p['jumlah_malam'] ?> Malam</span>
            </div>
            <div class="ringkasan-row">
              <span class="ringkasan-row-label">Jumlah Peserta</span>
              <span class="ringkasan-row-val"><?= $p['jumlah_peserta'] ?> Orang</span>
            </div>
          </div>
          <div class="ringkasan-section">
            <div class="ringkasan-section-title">Transportasi & Akomodasi</div>
            <div class="ringkasan-row">
              <span class="ringkasan-row-label">Kendaraan</span>
              <span class="ringkasan-row-val"><?= htmlspecialchars($kendaraan['nama_kendaraan'] ?? '-') ?></span>
            </div>
            <div class="ringkasan-row">
              <span class="ringkasan-row-label">Hotel</span>
              <span class="ringkasan-row-val"><?= $hotel ? htmlspecialchars($hotel['nama_hotel']) : 'Tidak memilih hotel' ?></span>
            </div>
            <div class="ringkasan-row">
              <span class="ringkasan-row-label">Titik Penjemputan</span>
              <span class="ringkasan-row-val">
                <?= htmlspecialchars($p['titik_jemput']) ?>
                <?php if ($p['detail_jemput']): ?><br><small style="color:var(--text-muted)"><?= htmlspecialchars($p['detail_jemput']) ?></small><?php endif; ?>
              </span>
            </div>
          </div>
        </div>

        <!-- Rincian Biaya -->
        <div class="flow-card">
          <div class="flow-card-title">💰 Rincian Biaya</div>
          <div class="ringkasan-section">
            <div class="ringkasan-section-title">Biaya Wisata (<?= $p['jumlah_peserta'] ?> orang)</div>
            <?php foreach ($p['itinerary'] as $item):
              $w = $wisata_data[$item['id_wisata']] ?? null;
              if (!$w) continue;
              $sub = (int)($item['harga_paket'] ?? $w['harga_mulai']) * (int)$p['jumlah_peserta'];
            ?>
            <div class="ringkasan-row">
              <span class="ringkasan-row-label">
                <?= htmlspecialchars($w['nama_wisata']) ?> (Hari <?= $item['hari'] ?>)<br>
                <small style="color:var(--text-muted);font-weight:500"><?= htmlspecialchars($item['nama_paket'] ?? 'Paket Standar') ?></small>
              </span>
              <span class="ringkasan-row-val">Rp <?= number_format($sub, 0, ',', '.') ?></span>
            </div>
            <?php endforeach; ?>
          </div>
          <div class="ringkasan-section">
            <div class="ringkasan-section-title">Transportasi & Akomodasi</div>
            <div class="ringkasan-row">
              <span class="ringkasan-row-label"><?= htmlspecialchars($kendaraan['nama_kendaraan'] ?? '-') ?> × <?= $p['jumlah_hari'] ?> hari</span>
              <span class="ringkasan-row-val">Rp <?= number_format($biaya_kendaraan, 0, ',', '.') ?></span>
            </div>
            <?php if ($hotel): ?>
            <div class="ringkasan-row">
              <span class="ringkasan-row-label">
                <?= htmlspecialchars($hotel['nama_hotel']) ?><br>
                <small style="color:var(--text-muted);font-weight:500">
                  <?= $jumlah_kamar ?> kamar &times; <?= $p['jumlah_malam'] ?> malam<br>
                  <?= $kamar_detail ?>
                </small>
              </span>
              <span class="ringkasan-row-val">Rp <?= number_format($biaya_hotel, 0, ',', '.') ?></span>
            </div>
            <?php endif; ?>
          </div>
        </div>

      </div>

      <!-- KANAN: Summary + Bayar -->
      <div>
        <div class="summary-sticky">
          <div class="summary-sticky-title">Total Pembayaran</div>
          <div class="summary-line">
            <span class="summary-line-label">Biaya Wisata</span>
            <span class="summary-line-val">Rp <?= number_format($biaya_wisata, 0, ',', '.') ?></span>
          </div>
          <div class="summary-line">
            <span class="summary-line-label">Kendaraan</span>
            <span class="summary-line-val">Rp <?= number_format($biaya_kendaraan, 0, ',', '.') ?></span>
          </div>
          <?php if ($biaya_hotel > 0): ?>
          <div class="summary-line">
            <span class="summary-line-label">Hotel</span>
            <span class="summary-line-val">Rp <?= number_format($biaya_hotel, 0, ',', '.') ?></span>
          </div>
          <?php endif; ?>
          <div class="summary-divider"></div>
          <div class="summary-total-label">Total</div>
          <div class="summary-total-val">Rp <?= number_format($total, 0, ',', '.') ?></div>

          <form method="POST">
            <button type="submit" name="konfirmasi" class="btn-next">🎫 Lanjut ke Pembayaran</button>
          </form>
          <a href="penjemputan.php" class="btn-back">← Kembali</a>

          <div style="margin-top:16px;padding:12px;background:#f8fafc;border-radius:var(--radius-sm);font-size:.75rem;color:var(--text-muted);line-height:1.6;">
            🔒 Data perjalanan akan disimpan setelah konfirmasi pembayaran.<br>
            ⏰ Selesaikan pembayaran dalam 24 jam.
          </div>
        </div>
      </div>

    </div>
  </main>
</div>
</body>
</html>
