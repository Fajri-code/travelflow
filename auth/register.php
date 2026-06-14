<?php
session_start();
include '../config/koneksi.php';

$error = '';
if (isset($_POST['daftar'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $cek = mysqli_query($conn, "SELECT id_user FROM users WHERE email='$email'");
    if (mysqli_num_rows($cek) > 0) {
        $error = 'Email sudah terdaftar!';
    } else {
        mysqli_query($conn, "INSERT INTO users (nama, email, password) VALUES ('$nama', '$email', '$password')");
        echo "<script>alert('Registrasi berhasil! Silakan login.'); window.location='login.php';</script>";
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register - TravelFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <link href="../assets/css/auth.css" rel="stylesheet">
</head>
<body>
<div class="auth-wrapper">
    <div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="auth-card p-4">
                <div class="text-center mb-4">
                    <div class="brand-title">✈️ TravelFlow</div>
                    <p class="text-muted">Buat akun baru</p>
                </div>
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" minlength="6" required>
                    </div>
                    <button type="submit" name="daftar" class="btn btn-tf w-100">Daftar</button>
                </form>
                <hr>
                <p class="text-center mb-0">Sudah punya akun? <a href="login.php">Login di sini</a></p>
            </div>
        </div>
    </div>
    </div>
</div>
</body>
</html>
