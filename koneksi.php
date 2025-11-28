<?php
$koneksi = mysqli_connect("localhost", "root", "", "cafehm");
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>