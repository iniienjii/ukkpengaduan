<?php
include 'koneksi.php';

if (isset($_POST['register'])) {
    $nis      = mysqli_real_escape_string($conn, $_POST['nis']);
    $nama     = mysqli_real_escape_string($conn, $_POST['nama']);
    $kelas    = mysqli_real_escape_string($conn, $_POST['kelas']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); 

    $cek = mysqli_query($conn, "SELECT * FROM siswa WHERE nis='$nis'");
    
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('NIS sudah terdaftar!'); window.location='register.php';</script>";
    } else {
        $query = mysqli_query($conn, "INSERT INTO siswa (nis, nama, kelas, password) 
                                     VALUES ('$nis', '$nama', '$kelas', '$password')");

        if ($query) {
            echo "<script>alert('Registrasi Berhasil! Silakan Login.'); window.location='login.php';</script>";
        } else {
            echo "<script>alert('Gagal Registrasi: " . mysqli_error($conn) . "');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f7f6;
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 20px 0; /* Tambahan padding biar gak mepet atas bawah di HP */
        }

        .register-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            border: none;
            overflow: hidden;
            width: 100%;
        }

        .register-header {
            background: #1e293b;
            color: white;
            padding: 25px;
            text-align: center;
        }

        .form-label {
            font-size: 0.8rem;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
        }

        .form-control {
            border-radius: 8px;
            padding: 10px 15px;
            border: 1px solid #e2e8f0;
            background-color: #f8fafc;
        }

        .form-control:focus {
            background-color: #fff;
            border-color: #3b82f6;
            box-shadow: none;
        }

        .btn-register {
            background: #1e293b;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
            transition: 0.3s;
        }

        .btn-register:hover {
            background: #334155;
            transform: translateY(-2px);
        }
        
        /* Mengatur ukuran card di berbagai layar */
        @media (max-width: 576px) {
            .register-header { padding: 20px; }
            .p-content { padding: 25px !important; } /* Padding form lebih kecil di HP */
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-11 col-sm-8 col-md-6 col-lg-4">
            <div class="register-card">
                <div class="register-header">
                    <i class="bi bi-person-plus-fill fs-1 mb-2"></i>
                    <h4 class="fw-bold mb-1">Join Us!</h4>
                    <p class="small mb-0 opacity-75">Daftar akun aspirasi siswa</p>
                </div>
                
                <div class="p-content p-4 p-md-5">
                    <form action="" method="POST">
                        <div class="mb-3">
                            <label class="form-label">NIS</label>
                            <input type="number" name="nis" class="form-control" placeholder="Masukkan NIS" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control" placeholder="Nama Lengkap" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Kelas</label>
                            <input type="text" name="kelas" class="form-control" placeholder="Contoh: XII RPL 1" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" placeholder="******" required>
                        </div>

                        <button type="submit" name="register" class="btn btn-register w-100 mb-3">
                            Daftar Sekarang
                        </button>
                        
                        <div class="text-center">
                            <small class="text-muted">Sudah punya akun? <br class="d-block d-sm-none"> 
                                <a href="login.php" class="text-primary fw-bold text-decoration-none">Login ke Dashboard</a>
                            </small>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>