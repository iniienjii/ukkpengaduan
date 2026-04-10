<?php
session_start();
include 'koneksi.php';

// CEK LOGIN ADMIN
if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit;
}

// AMBIL ID & VALIDASI
if (!isset($_GET['id'])) {
    header("Location: admin.php");
    exit;
}

$id = $_GET['id'];

// AMBIL DETAIL ASPIRASI DENGAN JOIN
$query = mysqli_query($conn, "SELECT aspirasi.*, siswa.nama, kategori.nama_kategori 
                              FROM aspirasi 
                              JOIN siswa ON aspirasi.nis = siswa.nis 
                              JOIN kategori ON aspirasi.id_kategori = kategori.id_kategori 
                              WHERE id_aspirasi = '$id'");
$data = mysqli_fetch_assoc($query);

// JIKA DATA TIDAK DITEMUKAN
if (!$data) {
    echo "<script>alert('Data tidak ditemukan!'); window.location='admin.php';</script>";
    exit;
}

// LOGIKA UPDATE STATUS & TANGGAPAN
if (isset($_POST['update_status'])) {
    $status_baru = $_POST['status'];
    $tanggapan = mysqli_real_escape_string($conn, $_POST['tanggapan']);
    
    $update = mysqli_query($conn, "UPDATE aspirasi SET status = '$status_baru', feedback = '$tanggapan' WHERE id_aspirasi = '$id'");
    
    if ($update) {
        echo "<script>alert('Status berhasil diperbarui!'); window.location='admin.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proses Laporan #<?= $data['id_aspirasi']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; font-family: 'Inter', sans-serif; color: #334155; }
        .header-section { background-color: #ffffff; border-bottom: 1px solid #e2e8f0; padding: 15px 0; margin-bottom: 25px; }
        .detail-card { background: white; border-radius: 12px; border: 1px solid #e2e8f0; padding: 25px; height: 100%; }
        .label-custom { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; color: #64748b; font-weight: 700; display: block; margin-bottom: 5px; }
        .info-value { font-weight: 600; color: #1e293b; margin-bottom: 15px; }
        .img-preview { width: 100%; border-radius: 10px; border: 1px solid #e2e8f0; margin-top: 10px; cursor: pointer; transition: 0.3s; }
        .img-preview:hover { opacity: 0.9; }
        .badge-status { padding: 6px 12px; border-radius: 6px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
        .menunggu { background: #fee2e2; color: #991b1b; }
        .proses { background: #fef3c7; color: #92400e; }
        .selesai { background: #dcfce7; color: #166534; }
        .btn-update { background-color: #1e293b; color: white; border: none; padding: 12px; border-radius: 8px; }
        .btn-update:hover { background-color: #334155; transform: translateY(-1px); color: white; }
    </style>
</head>
<body>

<div class="header-section shadow-sm sticky-top">
    <div class="container d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <a href="admin.php" class="btn btn-outline-secondary btn-sm me-3 border-0"><i class="bi bi-arrow-left"></i> Kembali</a>
            <h6 class="mb-0 fw-bold">Detail Pengaduan #<?= $data['id_aspirasi']; ?></h6>
        </div>
        <span class="badge-status <?= $data['status']; ?>"><?= $data['status']; ?></span>
    </div>
</div>

<div class="container pb-5">
    <div class="row g-4">
        
        <div class="col-lg-7">
            <div class="detail-card shadow-sm">
                <div class="row">
                    <div class="col-6">
                        <span class="label-custom">Pelapor</span>
                        <div class="info-value"><i class="bi bi-person me-1"></i> <?= $data['nama']; ?></div>
                    </div>
                    <div class="col-6">
                        <span class="label-custom">Kategori</span>
                        <div class="info-value"><span class="text-primary">#<?= $data['nama_kategori']; ?></span></div>
                    </div>
                    <div class="col-12">
                        <span class="label-custom">Tanggal Masuk</span>
                        <div class="info-value text-muted small"><?= date('d F Y - H:i', strtotime($data['tanggal'])); ?></div>
                    </div>
                </div>

                <hr class="my-3 opacity-50">

                <span class="label-custom">Isi Laporan</span>
                <p class="text-dark mb-4" style="line-height: 1.6; white-space: pre-line;"><?= $data['keterangan']; ?></p>

                <?php if($data['foto']): ?>
                    <span class="label-custom">Lampiran Bukti</span>
                    <a href="assets/img/<?= $data['foto']; ?>" target="_blank">
                        <img src="assets/img/<?= $data['foto']; ?>" class="img-preview" alt="Bukti Foto">
                    </a>
                    <p class="text-muted mt-2 small">*Klik gambar untuk memperbesar</p>
                <?php else: ?>
                    <div class="p-3 bg-light rounded text-center text-muted small">Tidak ada lampiran foto</div>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="detail-card shadow-sm border-top border-primary border-4">
                <h6 class="fw-bold mb-4 text-dark"><i class="bi bi-pencil-square me-2"></i>Tindakan Admin</h6>
                
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">UPDATE STATUS</label>
                        <select name="status" class="form-select border-light-subtle shadow-sm">
                            <option value="menunggu" <?= $data['status'] == 'menunggu' ? 'selected' : ''; ?>>Menunggu</option>
                            <option value="proses" <?= $data['status'] == 'proses' ? 'selected' : ''; ?>>Proses</option>
                            <option value="selesai" <?= $data['status'] == 'selesai' ? 'selected' : ''; ?>>Selesai</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-muted small fw-bold">TANGGAPAN / FEEDBACK</label>
                        <textarea name="tanggapan" class="form-control border-light-subtle shadow-sm" rows="8" placeholder="Berikan penjelasan atau solusi kepada pelapor..."><?= $data['feedback']; ?></textarea>
                    </div>

                    <div class="d-grid">
                        <button type="submit" name="update_status" class="btn btn-update fw-bold shadow">
                            <i class="bi bi-save me-2"></i> Simpan & Kirim Update
                        </button>
                    </div>
                </form>
            </div>

            <div class="alert alert-info mt-4 border-0 shadow-sm" style="border-radius: 12px;">
                <div class="d-flex">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    <small>Siswa akan menerima update status dan tanggapan ini secara <strong>real-time</strong> di dashboard mereka.</small>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>