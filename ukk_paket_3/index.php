<?php
session_start();

if(isset($_SESSION['login'])){
    if($_SESSION['role'] == 'admin'){
        header("Location: admin.php");
    } else {
        header("Location: siswa.php");
    }
} else {
    header("Location: login.php");
}

if (isset($_POST['register'])) {
    $nis = htmlspecialchars($_POST['nis']);
    $nama = htmlspecialchars($_POST['nama']);
    $kelas = htmlspecialchars($_POST['kelas']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // cek NIS sudah ada
    $cek = query("SELECT * FROM siswa WHERE nis='$nis'");
    if ($cek) {
        $pesan = "NIS sudah terdaftar!";
    } else {
        mysqli_query($conn, "INSERT INTO siswa VALUES ('$nis','$nama','$kelas','$password')");
        $pesan = "Registrasi berhasil! Silakan login.";
    }
}
?>
