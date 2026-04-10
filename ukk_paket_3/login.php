<?php
session_start();
include 'koneksi.php';

if (isset($_POST['login'])) {
    $user_input = mysqli_real_escape_string($conn, $_POST['username']);
    $pass_input = $_POST['password'];

    // 1. CEK DI TABEL ADMIN DULU
    $cek_admin = mysqli_query($conn, "SELECT * FROM admin WHERE username='$user_input'");
    $data_admin = mysqli_fetch_assoc($cek_admin);

    if ($data_admin) {
        // Cek password (pakai password_verify atau ganti ke == jika tidak di-hash)
        if (password_verify($pass_input, $data_admin['password']) || $pass_input == $data_admin['password']) {
            $_SESSION['login'] = true;
            $_SESSION['role'] = 'admin';
            $_SESSION['username'] = $data_admin['username'];
            header("Location: admin.php");
            exit;
        }
    }

    // 2. KALAU ADMIN GAK KETEMU, CEK DI TABEL SISWA
    $cek_siswa = mysqli_query($conn, "SELECT * FROM siswa WHERE nis='$user_input'");
    $data_siswa = mysqli_fetch_assoc($cek_siswa);

    if ($data_siswa) {
        if (password_verify($pass_input, $data_siswa['password']) || $pass_input == $data_siswa['password']) {
            $_SESSION['login'] = true;
            $_SESSION['role'] = 'siswa';
            $_SESSION['nis'] = $data_siswa['nis'];
            $_SESSION['nama'] = $data_siswa['nama'];
            header("Location: siswa.php");
            exit;
        }
    }

    // Jika semua gagal
    $error = "ID atau Password salah!";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Pengaduan Sekolah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background: #f4f7f6;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
            padding: 20px;
        }
        .login-card {
            padding: 30px;
            border-radius: 20px;
            background: white;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            border: 1px solid #e2e8f0;
        }
        .header-text h4 { font-weight: 700; color: #1e293b; margin-bottom: 5px; }
        .form-label { font-size: 0.85rem; font-weight: 600; color: #64748b; }
        .form-control { border-radius: 10px; padding: 12px; background: #f8fafc; border: 1px solid #e2e8f0; }
        .btn-login { border-radius: 10px; padding: 12px; font-weight: 600; background: #1e293b; border: none; color: white; transition: 0.3s; }
        .btn-login:hover { background: #334155; transform: translateY(-2px); }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-11 col-sm-8 col-md-5 col-lg-4">
            <div class="login-card">
                <div class="header-text text-center mb-4">
                    <div class="mb-3">
                        <i class="bi bi-megaphone-fill text-primary" style="font-size: 2.5rem;"></i>
                    </div>
                    <h4>Welcome</h4>
                    <p class="text-muted small">Silakan login untuk mengakses layanan aspirasi</p>
                </div>

                <?php if(isset($error)) : ?>
                    <div class="alert alert-danger text-center p-2 small border-0 mb-4" style="background: #fee2e2; color: #991b1b;">
                        <i class="bi bi-exclamation-circle me-1"></i> <?= $error; ?>
                    </div>
                <?php endif; ?>

                <form action="" method="post">
                    <div class="mb-3">
                        <label class="form-label">Username / NIS</label>
                        <input type="text" name="username" class="form-control" placeholder="Masukkan ID anda" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                    </div>

                    <button type="submit" name="login" class="btn btn-login w-100">
                        Login ke Akun <i class="bi bi-arrow-right ms-1"></i>
                    </button>
                </form>

                <div class="register-link text-center mt-4 pt-3 border-top">
                    <small class="text-muted">Belum punya akun? 
                        <a href="register.php" class="text-primary fw-bold text-decoration-none">Daftar Sekarang</a>
                    </small>
                </div>
            </div> 
        </div>
    </div>
</div>

</body>
</html>