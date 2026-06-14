<?php
session_start();
include __DIR__ . '/../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit;
}

$email    = mysqli_real_escape_string($conn, $_POST['email']);
$password = $_POST['password'];

$query = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
$data  = mysqli_fetch_assoc($query);

if ($data && password_verify($password, $data['password'])) {
    $_SESSION['user'] = $data['id_user'];
    $_SESSION['nama'] = $data['nama'];
    header("Location: ../user/dashboard.php");
    exit;
} else {
    $_SESSION['login_error'] = 'Email atau password salah!';
    header("Location: login.php");
    exit;
}
