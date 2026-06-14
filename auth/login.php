<?php
session_start();
$error = '';
if (isset($_SESSION['login_error'])) {
    $error = $_SESSION['login_error'];
    unset($_SESSION['login_error']);
}
if (isset($_SESSION['user'])) {
    header("Location: ../user/dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - TravelFlow</title>
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
                    <p class="text-muted">Masuk ke akun Anda</p>
                </div>
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                <form method="POST" action="proses_login.php">
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-tf w-100">Login</button>
                </form>
                <hr>
                <p class="text-center mb-0">Belum punya akun? <a href="register.php">Daftar di sini</a></p>
            </div>
        </div>
    </div>
    </div>
</div>
</body>
</html>
