<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "pengaduan_sekolah";

$conn = mysqli_connect($host, $user, $pass, $db);

// cek koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

function query($query){
    global $conn;
    $result = mysqli_query($conn, $query);
    $rows = [];

    while($row = mysqli_fetch_assoc($result)){
        $rows[] = $row;
    }

    return $rows;
}
?>