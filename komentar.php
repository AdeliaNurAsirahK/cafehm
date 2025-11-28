<?php
session_start();
include "koneksi.php";
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$komentar = $_POST['komentar'];
$user = $_SESSION['username'];
mysqli_query($koneksi, "INSERT INTO komentar2(username, isi) VALUES('$user', '$komentar')");
header("Location: list.php");
?>