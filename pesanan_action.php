<?php
include 'koneksi.php';
if($_SERVER['REQUEST_METHOD']=='POST'){
  $pelanggan = $_POST['pelanggan'];
  $menu= $_POST['menu'];
  $jumlah = $_POST['jumlah'];
  $harga_satuan = $_POST['harga satuan'];
  $total_harga = $_POST['total harga'];
  $tanggal_pesanan = $_POST['tanggal pesanan'];
  $q = mysqli_query($koneksi, "INSERT INTO pelanggan (menu,jumlah,harga satuan,total harga,tanggal pesanan) VALUES ('$menu','$jumlah','$harga_satuan','$total_harga,'$tanggal_pesanan')");
  header('Location: pesanan.php');
}
?>