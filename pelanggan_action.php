<?php
include 'koneksi.php';
if($_SERVER['REQUEST_METHOD']=='POST'){
  $nama = $_POST['nama'];
  $no_hp = $_POST['no_hp'];
  $email = $_POST['email'];
  $alamat = $_POST['alamat'];
  $q = mysqli_query($koneksi, "INSERT INTO pelanggan (nama,no_hp,email,alamat) VALUES ('$nama','$no_hp','$email','$alamat')");
  header('Location: pelanggan.php');
}
?>