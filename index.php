<?php
session_start();
if (isset($_SESSION['user'])) {
    header("Location: user/dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TravelFlow - Pesan Wisata Jogja</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'Plus Jakarta Sans',sans-serif;color:#1e293b;overflow-x:hidden}
        a{text-decoration:none;color:inherit}

        /* Navbar */
        .navbar{
            position:fixed;top:0;left:0;right:0;z-index:100;
            display:flex;align-items:center;justify-content:space-between;
            padding:0 48px;height:68px;
            background:rgba(255,255,255,.95);backdrop-filter:blur(10px);
            border-bottom:1px solid rgba(0,0,0,.08);
            box-shadow:0 2px 12px rgba(0,0,0,.06);
        }
        .nav-brand{display:flex;align-items:center;gap:10px;font-weight:800;font-size:1.2rem;color:#f97316}
        .nav-brand-icon{width:36px;height:36px;background:linear-gradient(135deg,#f97316,#ea580c);border-radius:10px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:1rem}
        .nav-links{display:flex;align-items:center;gap:32px}
        .nav-links a{font-size:.88rem;font-weight:600;color:#64748b;transition:color .2s}
        .nav-links a:hover{color:#f97316}
        .nav-btns{display:flex;gap:10px}
        .btn-login{padding:8px 20px;border:2px solid #f97316;border-radius:20px;font-size:.85rem;font-weight:700;color:#f97316;transition:all .2s}
        .btn-login:hover{background:#f97316;color:#fff}
        .btn-daftar{padding:8px 20px;background:linear-gradient(135deg,#f97316,#ea580c);border-radius:20px;font-size:.85rem;font-weight:700;color:#fff;box-shadow:0 3px 10px rgba(249,115,22,.3);transition:opacity .2s}
        .btn-daftar:hover{opacity:.9}

        /* Hero */
        .hero{
            min-height:100vh;position:relative;
            display:flex;align-items:center;
            background:linear-gradient(135deg,#1e3a5f 0%,#2d6a8f 50%,#c47c3c 100%);
            overflow:hidden;
        }
        .hero-img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;opacity:.4}
        .hero-overlay{position:absolute;inset:0;background:linear-gradient(135deg,rgba(15,40,80,.8) 0%,rgba(30,80,110,.5) 55%,rgba(180,100,30,.4) 100%)}
        .hero-content{position:relative;z-index:1;max-width:680px;padding:0 48px;margin-top:68px}
        .hero-badge{display:inline-block;background:rgba(255,255,255,.2);color:#fff;font-size:.78rem;font-weight:700;padding:6px 16px;border-radius:20px;margin-bottom:20px;backdrop-filter:blur(4px)}
        .hero h1{font-size:3.2rem;font-weight:800;color:#fff;line-height:1.15;margin-bottom:16px;text-shadow:0 2px 20px rgba(0,0,0,.3)}
        .hero h1 span{color:#fed7aa}
        .hero p{font-size:1.05rem;color:rgba(255,255,255,.85);margin-bottom:32px;line-height:1.7}
        .hero-btns{display:flex;gap:14px;flex-wrap:wrap}
        .btn-hero-primary{padding:14px 32px;background:linear-gradient(135deg,#f97316,#ea580c);color:#fff;border-radius:30px;font-size:.95rem;font-weight:700;box-shadow:0 6px 20px rgba(249,115,22,.4);transition:transform .2s,box-shadow .2s}
        .btn-hero-primary:hover{transform:translateY(-2px);box-shadow:0 8px 28px rgba(249,115,22,.5);color:#fff}
        .btn-hero-secondary{padding:14px 32px;background:rgba(255,255,255,.15);color:#fff;border:2px solid rgba(255,255,255,.4);border-radius:30px;font-size:.95rem;font-weight:700;backdrop-filter:blur(4px);transition:background .2s}
        .btn-hero-secondary:hover{background:rgba(255,255,255,.25);color:#fff}
        .hero-stats{display:flex;gap:32px;margin-top:48px;flex-wrap:wrap}
        .hero-stat{text-align:center}
        .hero-stat-val{font-size:1.8rem;font-weight:800;color:#fff}
        .hero-stat-lbl{font-size:.75rem;color:rgba(255,255,255,.7);margin-top:2px}

        /* Section */
        section{padding:80px 48px}
        .section-badge{display:inline-block;background:#fff7ed;color:#f97316;font-size:.75rem;font-weight:700;padding:4px 14px;border-radius:20px;margin-bottom:12px}
        .section-title{font-size:2rem;font-weight:800;color:#1e293b;margin-bottom:12px}
        .section-sub{font-size:.95rem;color:#64748b;max-width:500px;line-height:1.7}

        /* Destinasi */
        .destinasi-section{background:#f8fafc}
        .destinasi-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:24px;margin-top:40px}
        .dest-card{background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 16px rgba(0,0,0,.08);transition:transform .2s,box-shadow .2s}
        .dest-card:hover{transform:translateY(-6px);box-shadow:0 8px 28px rgba(0,0,0,.14)}
        .dest-img{height:200px;overflow:hidden;position:relative}
        .dest-img img{width:100%;height:100%;object-fit:cover;transition:transform .4s}
        .dest-card:hover .dest-img img{transform:scale(1.06)}
        .dest-img-placeholder{height:100%;display:flex;align-items:center;justify-content:center;font-size:4rem;background:linear-gradient(135deg,#fff5f0,#fed7aa)}
        .dest-body{padding:18px 20px}
        .dest-name{font-size:1rem;font-weight:700;color:#1e293b;margin-bottom:4px}
        .dest-lokasi{font-size:.78rem;color:#94a3b8;margin-bottom:12px}
        .dest-footer{display:flex;align-items:center;justify-content:space-between}
        .dest-harga{font-size:1.05rem;font-weight:800;color:#f97316}
        .dest-harga span{font-size:.72rem;color:#94a3b8;font-weight:500}
        .btn-dest{background:linear-gradient(135deg,#f97316,#ea580c);color:#fff;border:none;border-radius:8px;padding:8px 16px;font-size:.78rem;font-weight:700;cursor:pointer;font-family:'Plus Jakarta Sans',sans-serif}

        /* Fitur */
        .fitur-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:24px;margin-top:40px}
        .fitur-card{background:#fff;border-radius:16px;padding:28px 24px;border:1px solid #e2e8f0;box-shadow:0 2px 8px rgba(0,0,0,.05);transition:transform .2s,box-shadow .2s}
        .fitur-card:hover{transform:translateY(-4px);box-shadow:0 6px 20px rgba(0,0,0,.1)}
        .fitur-icon{width:52px;height:52px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:1.5rem;margin-bottom:16px}
        .fitur-title{font-size:.95rem;font-weight:700;color:#1e293b;margin-bottom:8px}
        .fitur-desc{font-size:.82rem;color:#64748b;line-height:1.6}

        /* Cara Kerja */
        .cara-section{background:#f8fafc}
        .cara-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:24px;margin-top:40px;position:relative}
        .cara-card{text-align:center;padding:24px 16px}
        .cara-num{width:48px;height:48px;background:linear-gradient(135deg,#f97316,#ea580c);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:1.1rem;margin:0 auto 16px}
        .cara-icon{font-size:2rem;margin-bottom:12px}
        .cara-title{font-size:.92rem;font-weight:700;color:#1e293b;margin-bottom:6px}
        .cara-desc{font-size:.78rem;color:#64748b;line-height:1.6}

        /* CTA */
        .cta-section{background:linear-gradient(135deg,#1e3a5f 0%,#2d6a8f 50%,#c47c3c 100%);position:relative;overflow:hidden;text-align:center}
        .cta-section img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;opacity:.3}
        .cta-overlay{position:absolute;inset:0;background:linear-gradient(135deg,rgba(15,40,80,.8),rgba(180,100,30,.5))}
        .cta-content{position:relative;z-index:1}
        .cta-content h2{font-size:2.2rem;font-weight:800;color:#fff;margin-bottom:12px}
        .cta-content p{font-size:.95rem;color:rgba(255,255,255,.85);margin-bottom:28px}
        .btn-cta{display:inline-block;padding:14px 40px;background:#fff;color:#f97316;border-radius:30px;font-size:.95rem;font-weight:800;box-shadow:0 6px 20px rgba(0,0,0,.2);transition:transform .2s}
        .btn-cta:hover{transform:translateY(-2px);color:#f97316}

        /* Footer */
        footer{background:#1e293b;color:rgba(255,255,255,.6);text-align:center;padding:24px 48px;font-size:.82rem}
        footer strong{color:#f97316}

        @media(max-width:768px){
            .navbar{padding:0 20px}
            .hero-content{padding:0 20px}
            .hero h1{font-size:2rem}
            section{padding:60px 20px}
            .destinasi-grid,.fitur-grid,.cara-grid{grid-template-columns:1fr}
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar">
    <div class="nav-brand">
        <div class="nav-brand-icon">✈</div>
        TravelFlow
    </div>
    <div class="nav-links">
        <a href="#destinasi">Destinasi</a>
        <a href="#fitur">Fitur</a>
        <a href="#cara">Cara Kerja</a>
    </div>
    <div class="nav-btns">
        <a href="auth/login.php" class="btn-login">Masuk</a>
        <a href="auth/register.php" class="btn-daftar">Daftar Gratis</a>
    </div>
</nav>

<!-- Hero -->
<section class="hero">
    <img src="assets/img/wisata/bag.png" alt="" class="hero-img" onerror="this.style.display='none'">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <div class="hero-badge">✈ Platform Wisata Yogyakarta #1</div>
        <h1>Jelajahi <span>Yogyakarta</span> dengan Mudah & Berkesan</h1>
        <p>Pesan paket wisata terbaik ke Borobudur, Prambanan, Merapi, dan destinasi Jogja lainnya. Harga terjangkau, proses cepat, pengalaman tak terlupakan.</p>
        <div class="hero-btns">
            <a href="auth/register.php" class="btn-hero-primary">🚀 Mulai Sekarang</a>
            <a href="#destinasi" class="btn-hero-secondary">🏝 Lihat Destinasi</a>
        </div>
        <div class="hero-stats">
            <div class="hero-stat">
                <div class="hero-stat-val">6+</div>
                <div class="hero-stat-lbl">Destinasi Wisata</div>
            </div>
            <div class="hero-stat">
                <div class="hero-stat-val">4</div>
                <div class="hero-stat-lbl">Pilihan Kendaraan</div>
            </div>
            <div class="hero-stat">
                <div class="hero-stat-val">100%</div>
                <div class="hero-stat-lbl">Pembayaran Aman</div>
            </div>
        </div>
    </div>
</section>

<!-- Destinasi -->
<section class="destinasi-section" id="destinasi">
    <div style="text-align:center;margin-bottom:8px">
        <span class="section-badge">🏝 Destinasi Populer</span>
    </div>
    <div style="text-align:center">
        <h2 class="section-title">Wisata Terbaik di Yogyakarta</h2>
        <p class="section-sub" style="margin:0 auto">Dari candi bersejarah hingga pantai eksotis, temukan destinasi impianmu</p>
    </div>
    <div class="destinasi-grid">
        <?php
        include 'config/koneksi.php';
        $wisata = mysqli_query($conn, "SELECT * FROM wisata LIMIT 6");
        while ($w = mysqli_fetch_assoc($wisata)):
        ?>
        <div class="dest-card">
            <div class="dest-img">
                <?php if (!empty($w['gambar'])): ?>
                    <img src="assets/img/wisata/<?= htmlspecialchars($w['gambar']) ?>" alt="<?= htmlspecialchars($w['nama_wisata']) ?>">
                <?php else: ?>
                    <div class="dest-img-placeholder">🌏</div>
                <?php endif; ?>
            </div>
            <div class="dest-body">
                <div class="dest-name"><?= htmlspecialchars($w['nama_wisata']) ?></div>
                <div class="dest-lokasi">📍 <?= htmlspecialchars($w['lokasi'] ?? 'Yogyakarta') ?></div>
                <div class="dest-footer">
                    <div class="dest-harga">Rp <?= number_format($w['harga'], 0, ',', '.') ?><span>/orang</span></div>
                    <a href="auth/register.php"><button class="btn-dest">Pesan →</button></a>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</section>

<!-- Fitur -->
<section id="fitur">
    <div style="text-align:center;margin-bottom:8px">
        <span class="section-badge">✨ Keunggulan Kami</span>
    </div>
    <div style="text-align:center">
        <h2 class="section-title">Kenapa Pilih TravelFlow?</h2>
        <p class="section-sub" style="margin:0 auto">Kami hadir untuk membuat perjalananmu lebih mudah dan menyenangkan</p>
    </div>
    <div class="fitur-grid">
        <div class="fitur-card">
            <div class="fitur-icon" style="background:#fff7ed">🏝</div>
            <div class="fitur-title">Destinasi Beragam</div>
            <div class="fitur-desc">6+ destinasi wisata terbaik Yogyakarta tersedia dengan informasi lengkap</div>
        </div>
        <div class="fitur-card">
            <div class="fitur-icon" style="background:#f0fdf4">🛒</div>
            <div class="fitur-title">Keranjang Wisata</div>
            <div class="fitur-desc">Pesan beberapa destinasi sekaligus dalam satu transaksi yang praktis</div>
        </div>
        <div class="fitur-card">
            <div class="fitur-icon" style="background:#eff6ff">💳</div>
            <div class="fitur-title">Pembayaran Aman</div>
            <div class="fitur-desc">Transfer bank ke BCA, BNI, Mandiri, BRI dengan konfirmasi otomatis</div>
        </div>
        <div class="fitur-card">
            <div class="fitur-icon" style="background:#fdf4ff">🧾</div>
            <div class="fitur-title">E-Ticket Instan</div>
            <div class="fitur-desc">Struk dan e-ticket langsung tersedia setelah pembayaran dikonfirmasi</div>
        </div>
    </div>
</section>

<!-- Cara Kerja -->
<section class="cara-section" id="cara">
    <div style="text-align:center;margin-bottom:8px">
        <span class="section-badge">📋 Cara Kerja</span>
    </div>
    <div style="text-align:center">
        <h2 class="section-title">Pesan Wisata dalam 4 Langkah</h2>
        <p class="section-sub" style="margin:0 auto">Proses pemesanan yang mudah dan cepat</p>
    </div>
    <div class="cara-grid">
        <div class="cara-card">
            <div class="cara-num">1</div>
            <div class="cara-icon">👤</div>
            <div class="cara-title">Daftar Akun</div>
            <div class="cara-desc">Buat akun gratis dan lengkapi profil kamu</div>
        </div>
        <div class="cara-card">
            <div class="cara-num">2</div>
            <div class="cara-icon">🏝</div>
            <div class="cara-title">Pilih Destinasi</div>
            <div class="cara-desc">Jelajahi destinasi dan tambahkan ke keranjang</div>
        </div>
        <div class="cara-card">
            <div class="cara-num">3</div>
            <div class="cara-icon">💳</div>
            <div class="cara-title">Bayar</div>
            <div class="cara-desc">Pilih metode pembayaran dan konfirmasi transfer</div>
        </div>
        <div class="cara-card">
            <div class="cara-num">4</div>
            <div class="cara-icon">🎫</div>
            <div class="cara-title">Nikmati Perjalanan</div>
            <div class="cara-desc">Tunjukkan e-ticket dan nikmati wisata Jogja!</div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta-section">
    <img src="assets/img/wisata/bag.png" alt="" onerror="this.style.display='none'">
    <div class="cta-overlay"></div>
    <div class="cta-content">
        <h2>Siap Jelajahi Yogyakarta? 🎉</h2>
        <p>Daftar sekarang dan dapatkan pengalaman wisata terbaik bersama TravelFlow</p>
        <a href="auth/register.php" class="btn-cta">Daftar Gratis Sekarang →</a>
    </div>
</section>

<!-- Footer -->
<footer>
    <p>© 2024 <strong>TravelFlow</strong> — Platform Wisata Yogyakarta. Dibuat untuk UAS.</p>
</footer>

</body>
</html>
