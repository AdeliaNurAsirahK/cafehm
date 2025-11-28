<?php
session_start();
include "koneksi.php";
if ($_SESSION['role'] != 'admin') {
    die("Akses ditolak!");
}

if (isset($_POST['simpan'])) {
    $nama = $_POST['nama'];
    $nim = $_POST['nim'];
    $photo = $_POST['photo'];
    mysqli_query($koneksi, "INSERT INTO students(nama, nim, photo) VALUES('$nama', '$nim', '$photo')");
    header("Location: list.php");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Mahasiswa</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container">
    <h3>Tambah Mahasiswa</h3>
    <form method="POST">
        <div class="form-group">
            <label>Nama</label>
            <input type="text" name="nama" class="form-control" required>
        </div>
        <div class="form-group">
            <label>NIM</label>
            <input type="text" name="nim" class="form-control" required>
        </div>
        <div class="form-group">
            <label>URL Foto</label>
            <input type="text" name="photo" class="form-control">
        </div>
        <button type="submit" name="simpan" class="btn btn-success">Simpan</button>
    </form>
</body>
</html>