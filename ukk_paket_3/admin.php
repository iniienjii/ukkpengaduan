<?php
session_start();
include 'koneksi.php';

// Proteksi Login Admin
if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// 1. Ambil List Kategori untuk Dropdown
$kategori_res = mysqli_query($conn, "SELECT * FROM kategori");
$kategori_list = [];
while ($k = mysqli_fetch_assoc($kategori_res)) {
    $kategori_list[] = $k;
}

// 2. Logika Filter
$where = "WHERE 1=1"; 
if (isset($_POST['cari'])) {
    $id_kat = $_POST['id_kategori'];
    $status = $_POST['status'];
    $tgl_awal = $_POST['tgl_awal'];
    $tgl_akhir = $_POST['tgl_akhir'];

    if ($id_kat != "") { $where .= " AND aspirasi.id_kategori = '$id_kat'"; }
    if ($status != "") { $where .= " AND status = '$status'"; }
    if ($tgl_awal != "" && $tgl_akhir != "") { 
        $where .= " AND tanggal BETWEEN '$tgl_awal' AND '$tgl_akhir'"; 
    } elseif ($tgl_awal != "") {
        $where .= " AND tanggal >= '$tgl_awal'"; 
    }
}

// 3. Ambil Data Aspirasi
$sql = "SELECT aspirasi.*, kategori.nama_kategori, siswa.nama 
        FROM aspirasi 
        JOIN kategori ON aspirasi.id_kategori = kategori.id_kategori
        JOIN siswa ON aspirasi.nis = siswa.nis 
        $where 
        ORDER BY aspirasi.tanggal DESC";

$query_laporan = mysqli_query($conn, $sql);
$laporan = [];
while ($row = mysqli_fetch_assoc($query_laporan)) {
    $laporan[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin | SUARA SISWA</title>
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

        /* Navbar Custom */
        .navbar-admin {
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
        .card-stat:hover { transform: translateY(-3px); }
        .card-stat h6 { font-size: 0.7rem; font-weight: 800; text-transform: uppercase; color: #64748b; margin-bottom: 8px; letter-spacing: 0.5px; }
        .card-stat h2 { font-weight: 800; margin: 0; color: #1e293b; }
        
        .border-indigo { border-left: 5px solid var(--primary); }
        .border-red { border-left: 5px solid #ef4444; }
        .border-amber { border-left: 5px solid #f59e0b; }
        .border-emerald { border-left: 5px solid var(--accent); }

        /* White Box Containers */
        .box-white {
            background: white;
            border-radius: 15px;
            padding: 25px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }

        /* Table Alignment & Styling */
        .table-responsive { border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0; }
        .table { margin-bottom: 0; background: white; width: 100%; }
        
        .table thead th {
            background-color: #f1f5f9;
            color: #475569;
            font-weight: 700;
            font-size: 0.75rem;
            text-transform: uppercase;
            padding: 15px;
            border-bottom: 2px solid #e2e8f0;
            text-align: left; /* Header Rata Kiri */
        }

        .table tbody td {
            padding: 15px;
            vertical-align: middle;
            text-align: left; /* Isi Rata Kiri */
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.9rem;
        }

        /* Utility Alignment */
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

        /* Buttons */
        .btn-filter { background-color: var(--primary); color: white; border: none; font-weight: 600; }
        .btn-filter:hover { background-color: var(--primary-dark); color: white; }
        .btn-print { background-color: var(--accent); color: white; border: none; font-weight: 600; }
        .btn-print:hover { background-color: #059669; color: white; }
    </style>
</head>
<body>

<nav class="navbar-admin">
    <div class="container d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold"><i class="bi bi-megaphone-fill me-2"></i> SUARA SISWA</h5>
        <div class="d-flex align-items-center">
            <span class="small me-3 d-none d-md-inline">Halo, <strong>Administrator</strong></span>
            <a href="logout.php" class="btn btn-sm btn-outline-light px-3 rounded-pill">Logout</a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="row g-3 mb-4">
        <?php 
        $t = count($laporan);
        $m = count(array_filter($laporan, fn($x) => $x['status']=='menunggu'));
        $p = count(array_filter($laporan, fn($x) => $x['status']=='proses'));
        $s = count(array_filter($laporan, fn($x) => $x['status']=='selesai'));
        ?>
        <div class="col-6 col-md-3">
            <div class="card-stat border-indigo">
                <h6>Laporan Masuk</h6>
                <h2><?= $t ?></h2>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card-stat border-red">
                <h6>Menunggu</h6>
                <h2><?= $m ?></h2>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card-stat border-amber">
                <h6>Dalam Proses</h6>
                <h2><?= $p ?></h2>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card-stat border-emerald">
                <h6>Selesai</h6>
                <h2><?= $s ?></h2>
            </div>
        </div>
    </div>

    <div class="box-white mb-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-filter-square-fill me-2"></i>FILTER LAPORAN</h6>
            <a href="cetak.php" target="_blank" class="btn btn-print btn-sm px-4 rounded-3 shadow-sm">
                <i class="bi bi-printer-fill me-2"></i>CETAK PDF
            </a>
        </div>
        <form method="post" class="row g-3">
            <div class="col-md-3">
                <label class="form-label small fw-bold">Kategori</label>
                <select name="id_kategori" class="form-select form-select-sm">
                    <option value="">Semua Kategori</option>
                    <?php foreach($kategori_list as $k) : ?>
                        <option value="<?= $k['id_kategori']; ?>"><?= $k['nama_kategori']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    <option value="menunggu">Menunggu</option>
                    <option value="proses">Proses</option>
                    <option value="selesai">Selesai</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold">Mulai Tanggal</label>
                <input type="date" name="tgl_awal" class="form-control form-control-sm">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold">Sampai Tanggal</label>
                <input type="date" name="tgl_akhir" class="form-control form-control-sm">
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" name="cari" class="btn btn-filter btn-sm w-100 py-2">
                    <i class="bi bi-search me-2"></i>TERAPKAN FILTER
                </button>
            </div>
        </form>
    </div>

    <div class="table-responsive shadow-sm">
        <table class="table">
            <thead>
                <tr>
                    <th class="t-center" width="50">NO</th>
                    <th width="120">TANGGAL</th>
                    <th width="180">PELAPOR</th>
                    <th width="150">KATEGORI</th>
                    <th>ISI LAPORAN</th>
                    <th class="t-center" width="80">BUKTI</th>
                    <th class="t-center" width="130">STATUS</th>
                    <th class="t-center" width="80">AKSI</th>
                </tr>
            </thead>
            <tbody>
                <?php $no=1; foreach($laporan as $row) : ?>
                <tr>
                    <td class="t-center fw-bold text-muted"><?= $no++; ?></td>
                    <td class="text-secondary small"><?= date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                    <td class="fw-bold text-dark"><?= $row['nama']; ?></td>
                    <td>
                        <span class="badge bg-light text-primary border border-primary border-opacity-25 px-3">
                            <?= $row['nama_kategori']; ?>
                        </span>
                    </td>
                    <td class="text-muted small"><?= $row['keterangan']; ?></td>
                    
                    <td class="t-center">
                        <?php if($row['foto']): ?>
                            <a href="assets/img/<?= $row['foto']; ?>" target="_blank" class="text-primary">
                                <i class="bi bi-image-fill fs-5"></i>
                            </a>
                        <?php else: ?>
                            <span class="text-muted small">-</span>
                        <?php endif; ?>
                    </td>

                    <td class="t-center">
                        <span class="status-pill <?= $row['status']; ?>">
                            <?= $row['status'] == 'proses' ? 'DIPROSES' : strtoupper($row['status']); ?>
                        </span>
                    </td>
                    <td class="t-center">
                        <a href="proses_laporan.php?id=<?= $row['id_aspirasi']; ?>" class="btn btn-sm btn-outline-primary rounded-3">
                            <i class="bi bi-pencil-square"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                
                <?php if(empty($laporan)) : ?>
                <tr>
                    <td colspan="8" class="text-center py-5 text-muted small">
                        <i class="bi bi-info-circle me-1"></i> Tidak ada laporan ditemukan.
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>