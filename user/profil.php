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

$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id_user=$id_user"));

$success = '';
$error   = '';

// Update profil
if (isset($_POST['update_profil'])) {
    $nama_baru  = mysqli_real_escape_string($conn, $_POST['nama']);
    $email_baru = mysqli_real_escape_string($conn, $_POST['email']);
    $no_telp    = mysqli_real_escape_string($conn, $_POST['no_telp']);
    $alamat     = mysqli_real_escape_string($conn, $_POST['alamat']);

    // Cek email duplikat
    $cek = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id_user FROM users WHERE email='$email_baru' AND id_user != $id_user"));
    if ($cek) {
        $error = 'Email sudah digunakan akun lain!';
    } else {
        // Upload foto
        $foto = $user['foto'];
        if (!empty($_FILES['foto']['name'])) {
            $ext      = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            $allowed  = ['jpg','jpeg','png','webp'];
            if (in_array(strtolower($ext), $allowed)) {
                $foto = 'user_' . $id_user . '.' . $ext;
                move_uploaded_file($_FILES['foto']['tmp_name'], "../assets/img/users/$foto");
            } else {
                $error = 'Format foto tidak didukung! Gunakan jpg/png/webp';
            }
        }

        if (!$error) {
            mysqli_query($conn, "UPDATE users SET nama='$nama_baru', email='$email_baru', no_telp='$no_telp', alamat='$alamat', foto='$foto' WHERE id_user=$id_user");
            $_SESSION['nama'] = $nama_baru;
            $success = 'Profil berhasil diperbarui!';
            $user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id_user=$id_user"));
            $nama = $nama_baru;
            $inisial = strtoupper(substr($nama, 0, 1));
        }
    }
}

// Ganti password
if (isset($_POST['ganti_password'])) {
    $pass_lama = $_POST['password_lama'];
    $pass_baru = $_POST['password_baru'];
    $pass_konfirm = $_POST['password_konfirm'];

    if (!password_verify($pass_lama, $user['password'])) {
        $error = 'Password lama salah!';
    } elseif ($pass_baru !== $pass_konfirm) {
        $error = 'Konfirmasi password tidak cocok!';
    } elseif (strlen($pass_baru) < 6) {
        $error = 'Password baru minimal 6 karakter!';
    } else {
        $hash = password_hash($pass_baru, PASSWORD_DEFAULT);
        mysqli_query($conn, "UPDATE users SET password='$hash' WHERE id_user=$id_user");
        $success = 'Password berhasil diubah!';
    }
}

// Statistik user
$total_booking = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM transaksi WHERE id_user=$id_user"))['total'];
$total_spend   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total) as total FROM transaksi WHERE id_user=$id_user"))['total'] ?? 0;
$wisata_dikunjungi = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(DISTINCT id_wisata) as total FROM transaksi WHERE id_user=$id_user"))['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profil - TravelFlow</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="../assets/css/profil.css" rel="stylesheet">
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
        <a href="dashboard.php" class="sidebar-item"><span class="si-icon"></span> Dashboard</a>
        <a href="wisata.php" class="sidebar-item"><span class="si-icon"></span> Daftar Wisata</a>
        <a href="keranjang.php" class="sidebar-item"><span class="si-icon"></span> Booking</a>
        <a href="riwayat.php" class="sidebar-item"><span class="si-icon"></span> Riwayat Transaksi</a>
        <a href="ulasan.php" class="sidebar-item"><span class="si-icon"></span> Ulasan Saya</a>
        <a href="profil.php" class="sidebar-item active"><span class="si-icon"></span> Profil Saya</a>
        <div class="sidebar-divider"></div>
        <div class="sidebar-logout">
            <a href="../auth/logout.php" class="sidebar-item"><span class="si-icon">⇥</span> Logout</a>
        </div>
    </aside>

    <main class="tf-main">

        <!-- Header -->
        <div class="profil-header">
            <div class="profil-header-bg"></div>
            <div class="profil-header-content">
                <div class="profil-avatar-wrap">
                    <?php if (!empty($user['foto'])): ?>
                        <img src="../assets/img/users/<?= htmlspecialchars($user['foto']) ?>" alt="" class="profil-avatar-img">
                    <?php else: ?>
                        <div class="profil-avatar-placeholder"><?= $inisial ?></div>
                    <?php endif; ?>
                </div>
                <div class="profil-info">
                    <h2><?= htmlspecialchars($user['nama']) ?></h2>
                    <p><?= htmlspecialchars($user['email']) ?></p>
                    <span class="profil-badge"> TravelFlow Member</span>
                </div>
            </div>
            <!-- Stats -->
            <div class="profil-stats">
                <div class="profil-stat">
                    <div class="profil-stat-val"><?= $total_booking ?></div>
                    <div class="profil-stat-lbl">Total Booking</div>
                </div>
                <div class="profil-stat-divider"></div>
                <div class="profil-stat">
                    <div class="profil-stat-val"><?= $wisata_dikunjungi ?></div>
                    <div class="profil-stat-lbl">Wisata Dikunjungi</div>
                </div>
                <div class="profil-stat-divider"></div>
                <div class="profil-stat">
                    <div class="profil-stat-val">Rp <?= number_format($total_spend, 0, ',', '.') ?></div>
                    <div class="profil-stat-lbl">Total Pengeluaran</div>
                </div>
            </div>
        </div>

        <?php if ($success): ?>
        <div class="alert-success"> <?= $success ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
        <div class="alert-error"><?= $error ?></div>
        <?php endif; ?>

        <div class="profil-layout">

            <!-- Edit Profil -->
            <div class="profil-card">
                <div class="profil-card-title"> Edit Profil</div>
                <form method="POST" enctype="multipart/form-data">
                    <!-- Foto -->
                    <div class="foto-upload-wrap">
                        <div class="foto-preview" id="fotoPreview">
                            <?php if (!empty($user['foto'])): ?>
                                <img src="../assets/img/users/<?= htmlspecialchars($user['foto']) ?>" alt="" id="previewImg">
                            <?php else: ?>
                                <div class="foto-placeholder" id="fotoPlaceholder"><?= $inisial ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="foto-upload-info">
                            <label for="fotoInput" class="btn-upload-foto"> Ganti Foto</label>
                            <input type="file" id="fotoInput" name="foto" accept="image/*" style="display:none" onchange="previewFoto(this)">
                            <div class="foto-hint">JPG, PNG, WEBP. Maks 2MB</div>
                        </div>
                    </div>

                    <div class="form-group-row">
                        <div class="form-group">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" class="tf-input" name="nama" value="<?= htmlspecialchars($user['nama']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" class="tf-input" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">No. Telepon</label>
                        <input type="text" class="tf-input" name="no_telp" value="<?= htmlspecialchars($user['no_telp'] ?? '') ?>" placeholder="08123456789">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Alamat</label>
                        <textarea class="tf-input tf-textarea" name="alamat" rows="3" placeholder="Masukkan alamat lengkap"><?= htmlspecialchars($user['alamat'] ?? '') ?></textarea>
                    </div>
                    <button type="submit" name="update_profil" class="btn-save"> Simpan Perubahan</button>
                </form>
            </div>

            <!-- Ganti Password -->
            <div class="profil-card">
                <div class="profil-card-title"> Ganti Password</div>
                <form method="POST">
                    <div class="form-group">
                        <label class="form-label">Password Lama</label>
                        <input type="password" class="tf-input" name="password_lama" required placeholder="Masukkan password lama">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Password Baru</label>
                        <input type="password" class="tf-input" name="password_baru" required placeholder="Minimal 6 karakter" minlength="6">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" class="tf-input" name="password_konfirm" required placeholder="Ulangi password baru">
                    </div>
                    <button type="submit" name="ganti_password" class="btn-save"> Ganti Password</button>
                </form>

                <!-- Info Akun -->
                <div class="akun-info">
                    <div class="akun-info-title">Info Akun</div>
                    <div class="akun-info-item">
                        <span>Member sejak</span>
                        <span><?= date('d M Y', strtotime($user['created_at'] ?? 'now')) ?></span>
                    </div>
                    <div class="akun-info-item">
                        <span>Status Akun</span>
                        <span class="badge-aktif"> Aktif</span>
                    </div>
                </div>
            </div>

        </div>
    </main>
</div>

<script>
function previewFoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            const placeholder = document.getElementById('fotoPlaceholder');
            let img = document.getElementById('previewImg');
            if (!img) {
                img = document.createElement('img');
                img.id = 'previewImg';
                document.getElementById('fotoPreview').appendChild(img);
            }
            if (placeholder) placeholder.style.display = 'none';
            img.src = e.target.result;
            img.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
</body>
</html>
