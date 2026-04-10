<?php
session_start();
include 'koneksi.php';

// Proteksi Login
if (!isset($_SESSION['login']) || $_SESSION['role'] != 'siswa') {
    header("Location: login.php");
    exit;
}

// Ambil Kategori untuk Dropdown
$kategori_query = mysqli_query($conn, "SELECT * FROM kategori");

// Logika Simpan Data
if (isset($_POST['kirim'])) {
    $nis = $_SESSION['nis'];
    $id_kategori = $_POST['id_kategori'];
    $lokasi = mysqli_real_escape_string($conn, $_POST['lokasi']);
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);
    $tanggal = date('Y-m-d');
    
    // Handle Upload Foto
    $foto = $_FILES['foto']['name'];
    $tmp_name = $_FILES['foto']['tmp_name'];
    
    if ($foto) {
        $ekstensi = pathinfo($foto, PATHINFO_EXTENSION);
        $nama_foto_baru = time() . "_" . $nis . "." . $ekstensi;
        
        // Pastikan folder assets/img ada
        if (!file_exists('assets/img')) {
            mkdir('assets/img', 0777, true);
        }
        
        move_uploaded_file($tmp_name, "assets/img/" . $nama_foto_baru);
    } else {
        $nama_foto_baru = null;
    }

    $query = "INSERT INTO aspirasi (nis, id_kategori, lokasi, keterangan, foto, tanggal, status) 
              VALUES ('$nis', '$id_kategori', '$lokasi', '$keterangan', '$nama_foto_baru', '$tanggal', 'menunggu')";
    
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Aspirasi berhasil terkirim!'); window.location='siswa.php';</script>";
    } else {
        echo "<script>alert('Gagal mengirim aspirasi.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Aspirasi | Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; font-family: 'Inter', sans-serif; color: #334155; }
        .header-simple { background: white; border-bottom: 1px solid #e2e8f0; padding: 15px 0; margin-bottom: 20px; }
        .card-form { background: white; border-radius: 16px; border: none; padding: 25px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .form-label { font-size: 0.75rem; letter-spacing: 0.5px; margin-bottom: 8px; }
        .btn-submit { background-color: #1e293b; color: white; border: none; padding: 12px; border-radius: 10px; transition: 0.3s; }
        .btn-submit:hover { background-color: #334155; transform: translateY(-2px); }
        
        /* Penyesuaian HP */
        @media (max-width: 576px) {
            .card-form { padding: 20px; border-radius: 0; } /* Full width di HP biar lega */
            .container-mobile { padding: 0; } /* Menghilangkan padding container di HP */
        }
    </style>
</head>
<body>

<div class="header-simple shadow-sm sticky-top">
    <div class="container d-flex align-items-center">
        <a href="siswa.php" class="btn btn-outline-danger btn-sm me-3 border-0"><i class="bi bi-x-lg"></i></a>
        <h6 class="mb-0 fw-bold text-dark">Buat Laporan Baru</h6>
    </div>
</div>

<div class="container container-mobile pb-5">
    <div class="row justify-content-center g-0">
        <div class="col-md-7 col-lg-6">
            <div class="card-form">
                <form method="post" enctype="multipart/form-data">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary text-uppercase">Kategori Laporan</label>
                        <select name="id_kategori" class="form-select form-select-lg fs-6" required>
                            <option value="">-- Pilih Kategori --</option>
                            <?php while($kat = mysqli_fetch_assoc($kategori_query)) : ?>
                                <option value="<?= $kat['id_kategori']; ?>"><?= $kat['nama_kategori']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary text-uppercase">Lokasi Kejadian</label>
                        <input type="text" name="lokasi" class="form-control" placeholder="Contoh: Kantin, Lab RPL, dll" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary text-uppercase">Detail Aspirasi</label>
                        <textarea name="keterangan" class="form-control" rows="6" placeholder="Ceritakan apa yang terjadi..." required></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-secondary text-uppercase">Lampiran Foto (Opsional)</label>
                        <input type="file" name="foto" class="form-control" accept="image/*">
                        <div class="form-text mt-2"><i class="bi bi-info-circle me-1"></i> Gunakan format foto (JPG, PNG)</div>
                    </div>

                    <div class="d-grid mt-5">
                        <button type="submit" name="kirim" class="btn btn-submit fw-bold shadow">
                            <i class="bi bi-send-fill me-2"></i> Kirim Laporan
                        </button>
                    </div>

                </form>
            </div>
            <div class="px-3 px-md-0 mt-4 text-center">
                <p class="text-muted small">Laporan anda bersifat rahasia dan akan ditindak lanjuti oleh petugas terkait.</p>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>