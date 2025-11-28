<?php
session_start();
include 'koneksi.php';

// Cek apakah sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username']; // untuk cek apakah admin atau user
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Pelanggan - CoffeeTime</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style>
    body {
      background-image: url('img/bg-coffee.jpg');
      background-size: cover;
      background-position: center;
      background-attachment: fixed;
    }
    .content-box {
      background-color: rgba(255, 255, 255, 0.92);
      border-radius: 10px;
      padding: 20px;
    }
  </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container mt-4 content-box">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Data Pelanggan</h3>
    <a href="index.php" class="btn btn-secondary">🏠 Kembali ke Home</a>
  </div>

  <!-- Jika login sebagai admin -->
  <?php if ($username === 'admin') { ?>
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#tambahModal">+ Tambah Pelanggan</button>
  <?php } ?>

  <!-- Tabel pelanggan -->
  <table class="table table-striped">
    <thead class="table-dark">
      <tr>
        <th>No</th>
        <th>Nama</th>
        <th>No HP</th>
        <th>Email</th>
        <th>Alamat</th>
        <?php if ($username === 'admin') echo '<th>Aksi</th>'; ?>
      </tr>
    </thead>
    <tbody>
      <?php
      $no = 1;
      $data = mysqli_query($koneksi, "SELECT * FROM pelanggan");
      while ($d = mysqli_fetch_array($data)) {
        echo '<tr>
                <td>'.$no.'</td>
                <td>'.$d['nama'].'</td>
                <td>'.$d['no_hp'].'</td>
                <td>'.$d['email'].'</td>
                <td>'.$d['alamat'].'</td>';
        if ($username === 'admin') {
          echo '<td>
                  <button class="btn btn-warning btn-sm editBtn"
                    data-id="'.$d['id_pelanggan'].'"
                    data-nama="'.$d['nama'].'"
                    data-nohp="'.$d['no_hp'].'"
                    data-email="'.$d['email'].'"
                    data-alamat="'.$d['alamat'].'">Edit</button>
                  <a href="?hapus='.$d['id_pelanggan'].'" class="btn btn-danger btn-sm" onclick="return confirm(\'Yakin ingin menghapus data ini?\')">Hapus</a>
                </td>';
        }
        echo '</tr>';
        $no++;
      }
      ?>
    </tbody>
  </table>
</div>

<!-- Modal Tambah (hanya admin) -->
<?php if ($username === 'admin') { ?>
<div class="modal fade" id="tambahModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Tambah Pelanggan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="formPelanggan" method="post">
          <input name="nama" type="text" class="form-control mb-2" placeholder="Nama" required>
          <input name="no_hp" type="text" class="form-control mb-2" placeholder="No HP" required>
          <input name="email" type="email" class="form-control mb-2" placeholder="Email">
          <textarea name="alamat" class="form-control mb-2" placeholder="Alamat"></textarea>
          <button class="btn btn-success w-100" type="submit" name="simpan">Simpan</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="editModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-warning">
        <h5 class="modal-title">Edit Pelanggan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form method="post" id="formEditPelanggan">
          <input type="hidden" name="id_edit" id="id_edit">
          <input name="nama_edit" id="nama_edit" type="text" class="form-control mb-2" placeholder="Nama" required>
          <input name="no_hp_edit" id="no_hp_edit" type="text" class="form-control mb-2" placeholder="No HP" required>
          <input name="email_edit" id="email_edit" type="email" class="form-control mb-2" placeholder="Email">
          <textarea name="alamat_edit" id="alamat_edit" class="form-control mb-2" placeholder="Alamat"></textarea>
          <button class="btn btn-success w-100" type="submit" name="update">Simpan Perubahan</button>
        </form>
      </div>
    </div>
  </div>
</div>
<?php } ?>

<?php
// === CRUD hanya untuk admin ===
if ($username === 'admin') {
  // Tambah data
  if (isset($_POST['simpan'])) {
    $nama = $_POST['nama'];
    $no_hp = $_POST['no_hp'];
    $email = $_POST['email'];
    $alamat = $_POST['alamat'];
    mysqli_query($koneksi, "INSERT INTO pelanggan (nama, no_hp, email, alamat) VALUES ('$nama','$no_hp','$email','$alamat')");
    echo "<script>alert('Data berhasil ditambahkan!'); window.location='pelanggan.php';</script>";
  }

  // Update data
  if (isset($_POST['update'])) {
    $id = $_POST['id_edit'];
    $nama = $_POST['nama_edit'];
    $no_hp = $_POST['no_hp_edit'];
    $email = $_POST['email_edit'];
    $alamat = $_POST['alamat_edit'];
    mysqli_query($koneksi, "UPDATE pelanggan SET nama='$nama', no_hp='$no_hp', email='$email', alamat='$alamat' WHERE id_pelanggan='$id'");
    echo "<script>alert('Data berhasil diubah!'); window.location='pelanggan.php';</script>";
  }

  // Hapus data
  if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM pelanggan WHERE id_pelanggan='$id'");
    echo "<script>alert('Data berhasil dihapus!'); window.location='pelanggan.php';</script>";
  }
}
?>

<script>
$(document).ready(function(){
  $(".editBtn").click(function(){
    $("#id_edit").val($(this).data("id"));
    $("#nama_edit").val($(this).data("nama"));
    $("#no_hp_edit").val($(this).data("nohp"));
    $("#email_edit").val($(this).data("email"));
    $("#alamat_edit").val($(this).data("alamat"));
    $("#editModal").modal("show");
  });
});
</script>

<!-- jQuery Validation Plugin -->
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>

<script>
$(document).ready(function(){

    // VALIDASI FORM TAMBAH
    $("#formPelanggan").validate({
        rules: {
            nama: { required: true, minlength: 3 },
            no_hp: { required: true, digits: true, minlength: 10 },
            email: { email: true },
            alamat: { required: true }
        },
        messages: {
            nama: { required: "Nama wajib diisi", minlength: "Minimal 3 karakter" },
            no_hp: { required: "No HP wajib diisi", digits: "Harus angka", minlength: "Minimal 10 angka" },
            email: { email: "Format email salah" },
            alamat: { required: "Alamat tidak boleh kosong" }
        },
        errorClass: "text-danger",
        errorElement: "small"
    });

    // VALIDASI FORM EDIT
    $("#formEditPelanggan").validate({
        rules: {
            nama_edit: { required: true, minlength: 3 },
            no_hp_edit: { required: true, digits: true, minlength: 10 },
            email_edit: { email: true },
            alamat_edit: { required: true }
        },
        messages: {
            nama_edit: { required: "Nama wajib diisi", minlength: "Minimal 3 karakter" },
            no_hp_edit: { required: "No HP wajib diisi", digits: "Harus angka", minlength: "Minimal 10 angka" },
            email_edit: { email: "Email tidak valid" },
            alamat_edit: { required: "Alamat wajib diisi" }
        },
        errorClass: "text-danger",
        errorElement: "small"
    });


    // VALIDASI MANUAL + ANIMASI SHAKE
    function shake(el) {
        $(el).css("border", "2px solid red");
        $(el).animate({ left: "-10px" }, 50)
             .animate({ left: "10px" }, 50)
             .animate({ left: "-10px" }, 50)
             .animate({ left: "10px" }, 50)
             .animate({ left: "0px" }, 50);
    }

    // Tambah Pelanggan
    $("#formPelanggan").on("submit", function(e){
        let nama = $("input[name='nama']");
        let nohp = $("input[name='no_hp']");
        let ok = true;

        if (nama.val().length < 3) { shake(nama); ok = false; }
        if (nohp.val().length < 10) { shake(nohp); ok = false; }

        if (!ok) e.preventDefault();
    });

    // Edit Pelanggan
    $("#formEditPelanggan").on("submit", function(e){
        let nama = $("#nama_edit");
        let nohp = $("#no_hp_edit");
        let ok = true;

        if (nama.val().length < 3) { shake(nama); ok = false; }
        if (nohp.val().length < 10) { shake(nohp); ok = false; }

        if (!ok) e.preventDefault();
    });

});
</script>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>