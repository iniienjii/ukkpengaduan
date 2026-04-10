<?php
include 'koneksi.php';
// Ambil semua data aspirasi
$query = mysqli_query($conn, "SELECT aspirasi.*, siswa.nama, kategori.nama_kategori 
                              FROM aspirasi 
                              JOIN siswa ON aspirasi.nis = siswa.nis 
                              JOIN kategori ON aspirasi.id_kategori = kategori.id_kategori 
                              ORDER BY tanggal DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Laporan Pengaduan Aspirasi</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; font-size: 12px; }
        th { background-color: #f2f2f2; }
        .header { text-align: center; margin-bottom: 30px; }
        .no-print { margin-bottom: 20px; }
        
        /* CSS ini yang mengatur tampilan saat diprint */
        @media print {
            .no-print { display: none; } /* Tombol cetak hilang saat diprint */
            body { padding: 0; }
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>LAPORAN PENGADUAN ASPIRASI SISWA</h2>
        <p>Laporan Data Masuk Sampai Tanggal: <?= date('d-m-Y') ?></p>
        <hr>
    </div>

    <div class="no-print">
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer;">
            🖨️ Cetak Laporan / Simpan PDF
        </button>
        <a href="admin.php">Kembali ke Dashboard</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>NIS</th>
                <th>Nama Siswa</th>
                <th>Kategori</th>
                <th>Lokasi</th>
                <th>Isi Laporan</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            while($d = mysqli_fetch_assoc($query)) : 
            ?>
            <tr>
                <td><?= $no++; ?></td>
                <td><?= date('d/m/Y', strtotime($d['tanggal'])); ?></td>
                <td><?= $d['nis']; ?></td>
                <td><?= $d['nama']; ?></td>
                <td><?= $d['nama_kategori']; ?></td>
                <td><?= $d['lokasi']; ?></td>
                <td><?= $d['keterangan']; ?></td>
                <td><?= strtoupper($d['status']); ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div style="margin-top: 50px; text-align: right; margin-right: 50px;">
        <p>Dicetak pada: <?= date('d M Y') ?></p>
        <br><br><br>
        <p>(.........................................)</p>
        <p>Petugas Admin</p>
    </div>

</body>
</html>