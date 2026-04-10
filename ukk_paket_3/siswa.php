<?php
session_start();
include 'koneksi.php';

// Proteksi login
if(!isset($_SESSION['login']) || $_SESSION['role'] != 'siswa'){
    header("Location: login.php");
    exit;
}

$nis = $_SESSION['nis'];

// Ambil data aspirasi milik siswa
$q = mysqli_query($conn, "SELECT aspirasi.*, kategori.nama_kategori 
                          FROM aspirasi 
                          JOIN kategori ON aspirasi.id_kategori = kategori.id_kategori
                          WHERE nis='$nis'
                          ORDER BY tanggal DESC");

$laporan = [];
while($row = mysqli_fetch_assoc($q)){
    $laporan[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Siswa | SUARA SISWA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --accent: #10b981;
            --bg: #f8fafc;
        }

        body { 
            background-color: var(--bg); 
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            color: #334155;
        }

        /* Navbar Custom - Sama dengan Admin */
        .navbar-siswa {
            background-color: var(--primary);
            color: white;
            padding: 1rem 0;
            margin-bottom: 2rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        /* Statistik Cards */
        .card-stat {
            background: white;
            border: none;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            transition: 0.3s;
        }
        .card-stat h6 { font-size: 0.7rem; font-weight: 800; text-transform: uppercase; color: #64748b; margin-bottom: 8px; letter-spacing: 0.5px; }
        .card-stat h2 { font-weight: 800; margin: 0; color: #1e293b; }
        
        .border-indigo { border-left: 5px solid var(--primary); }
        .border-red { border-left: 5px solid #ef4444; }
        .border-amber { border-left: 5px solid #f59e0b; }
        .border-emerald { border-left: 5px solid var(--accent); }

        /* Tombol Emerald Green */
        .btn-add { 
            background-color: var(--accent); 
            color: white; 
            border: none;
            border-radius: 10px; 
            padding: 12px 25px;
            font-weight: 700;
            box-shadow: 0 4px 6px rgba(16,182,129,0.2);
            transition: 0.2s;
        }
        .btn-add:hover { 
            background-color: #059669; 
            color: white;
            transform: translateY(-2px);
        }

        /* Tabel Lurus & Rapi */
        .table-container { 
            background: white; 
            border-radius: 15px; 
            overflow: hidden; 
            border: 1px solid #e2e8f0; 
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        }
        
        .table thead th {
            background-color: #f1f5f9;
            color: #475569;
            font-weight: 700;
            font-size: 0.75rem;
            text-transform: uppercase;
            padding: 15px;
            border-bottom: 2px solid #e2e8f0;
            text-align: left;
        }

        .table tbody td {
            padding: 15px;
            vertical-align: middle;
            text-align: left;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.85rem;
        }

        .t-center { text-align: center !important; }

        /* Status Pills */
        .status-pill {
            padding: 5px 14px;
            border-radius: 30px;
            font-size: 10px;
            font-weight: 800;
            display: inline-block;
            text-transform: uppercase;
        }
        .menunggu { background: #fee2e2; color: #991b1b; }
        .proses { background: #fef3c7; color: #92400e; }
        .selesai { background: #dcfce7; color: #166534; }
    </style>
</head>
<body>

<nav class="navbar-siswa">
    <div class="container d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold"><i class="bi bi-person-circle me-2"></i> AREA SISWA</h5>
        <div class="d-flex align-items-center">
            <span class="small me-3 d-none d-md-inline">Halo, <strong><?= $_SESSION['nama'] ?? 'Siswa'; ?></strong></span>
            <a href="logout.php" class="btn btn-sm btn-outline-light px-3 rounded-pill">Logout</a>
        </div>
    </div>
</nav>

<div class="container pb-5">
    <div class="row mb-4 g-3">
        <?php 
        $total = count($laporan);
        $menunggu = count(array_filter($laporan, fn($x) => $x['status']=='menunggu'));
        $proses = count(array_filter($laporan, fn($x) => $x['status']=='proses'));
        $selesai = count(array_filter($laporan, fn($x) => $x['status']=='selesai'));
        ?>
        <div class="col-6 col-md-3">
            <div class="card-stat border-indigo">
                <h6>Total Aspirasi</h6>
                <h2><?= $total ?></h2>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card-stat border-red">
                <h6>Menunggu</h6>
                <h2><?= $menunggu ?></h2>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card-stat border-amber">
                <h6>Diproses</h6>
                <h2><?= $proses ?></h2>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card-stat border-emerald">
                <h6>Selesai</h6>
                <h2><?= $selesai ?></h2>
            </div>
        </div>
    </div>

    <div class="mb-4">
        <a href="form_aspirasi.php" class="btn btn-add">
            <i class="bi bi-plus-circle-fill me-2"></i> Buat Aspirasi Baru
        </a>
    </div>

    <div class="table-container">
        <div class="table-responsive">
            <table class="table table-hover align-middle" style="min-width: 900px;">
                <thead>
                    <tr>
                        <th class="t-center" width="50">No</th>
                        <th width="120">Tanggal</th>
                        <th width="150">Kategori</th>
                        <th width="150">Lokasi</th>
                        <th>Keterangan</th>
                        <th class="t-center" width="80">Foto</th>
                        <th class="t-center" width="130">Status</th>
                        <th width="200">Feedback Admin</th>
                    </tr>
                </thead>
                <tbody>
                <?php if(empty($laporan)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted small">Belum ada data aspirasi.</td>
                    </tr>
                <?php endif; ?>

                <?php $i=1; foreach($laporan as $row) : ?>
                    <tr>
                        <td class="t-center fw-bold text-muted"><?= $i++; ?></td>
                        <td class="text-secondary small"><?= date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                        <td>
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-10 px-2">
                                <?= $row['nama_kategori']; ?>
                            </span>
                        </td>
                        <td class="small fw-medium text-dark"><?= $row['lokasi']; ?></td>
                        <td class="text-muted small">
                            <?= strlen($row['keterangan']) > 60 ? substr($row['keterangan'], 0, 60) . '...' : $row['keterangan']; ?>
                        </td>
                        <td class="t-center">
                            <?php if($row['foto']): ?>
                                <a href="assets/img/<?= $row['foto']; ?>" target="_blank" class="text-primary">
                                  <i class="bi bi-image-fill fs-5"></i>
                                </a>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="t-center">
                            <span class="status-pill <?= $row['status']; ?>">
                                <?= $row['status'] == 'proses' ? 'Diproses' : strtoupper($row['status']); ?>
                            </span>
                        </td>
                        <td class="small">
                            <?php if($row['feedback']): ?>
                                <div class="p-2 rounded bg-light border-start border-3 border-primary">
                                    <i class="bi bi-chat-left-text-fill me-1 text-primary"></i> <?= $row['feedback']; ?>
                                </div>
                            <?php else: ?>
                                <span class="text-muted fst-italic">Belum ada tanggapan</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>