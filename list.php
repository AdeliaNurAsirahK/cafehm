<?php
session_start();
include "koneksi.php";

if (!isset($_SESSION['username'])) {
  header("Location: login.php");
  exit;
}

$username = $_SESSION['username'];
$role = $_SESSION['role']; // ambil peran user
?>

<!DOCTYPE html>
<html lang="id">
<head>
 <meta charset="UTF-8">
 <title>Daftar Mahasiswa - Coffeetime</title>
 <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
 <div class="d-flex justify-content-between align-items-center mb-4">
   <h2>📋 Daftar Mahasiswa</h2>
   <div>
     <b>Login sebagai:</b> <?php echo $username; ?> 
     (<?php echo $role; ?>)
     <a href="logout.php" class="btn btn-danger btn-sm ms-3">Logout</a>
   </div>
 </div>

 <?php if ($role == 'admin') { ?>
   <a href="tambah.php" class="btn btn-success mb-4">+ Tambah Mahasiswa</a>
 <?php } ?>

 <div class="row">
   <?php
   $query = mysqli_query($koneksi, "SELECT * FROM students");
   while ($data = mysqli_fetch_assoc($query)) { ?>
     <div class="col-md-4 mb-4">
       <div class="card shadow-sm">
         <img src="<?php echo $data['photo']; ?>" class="card-img-top" alt="Foto Mahasiswa" style="height: 250px; object-fit: cover;">
         <div class="card-body text-center">
           <h5 class="card-title"><?php echo $data['nama']; ?></h5>
           <p class="card-text">NIM: <?php echo $data['nim']; ?></p>

           <?php if ($role == 'admin') { ?>
             <a href="edit.php?id=<?php echo $data['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
             <a href="hapus.php?id=<?php echo $data['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus?')">Hapus</a>
           <?php } ?>
         </div>
       </div>
     </div>
   <?php } ?>
 </div>
</div>

</body>
</html>