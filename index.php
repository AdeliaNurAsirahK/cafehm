<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
include 'koneksi.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>CoffeeTime - Home</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      background-image: url('img/background.jpeg');
      background-size: cover;
      background-position: center;
      background-attachment: fixed;
      color: #fff;
    }

    .content-box {
      background-color: rgba(0, 0, 0, 0.6);
      padding: 20px;
      border-radius: 10px;
    }
  </style>
</head>

<body>

<?php include 'navbar.php'; ?>

<div class="container mt-3">
  <div class="d-flex justify-content-between align-items-center">
    <h5>Halo, <?php echo htmlspecialchars($_SESSION['username']); ?> 👋</h5>
    <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
  </div>
</div>

<div class="container mt-4 content-box">
  <div class="row">
    <div class="col-md-8">
      <h1>Welcome to HM COFFE3</h1>
      <p>Nikmati suasana hangat dan berbagai minuman terbaik.</p>

      <div id="promoCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
          <div class="carousel-item active">
            <img src="img/Es Kopi Kokas.jpg" class="d-block w-100" alt="promo1">
          </div>
          <div class="carousel-item">
            <img src="img/ice Biscoff Banana latte.jpg" class="d-block w-100" alt="promo2">
          </div>
        </div>
      </div>

      <!-- ⭐ KOMENTAR PELANGGAN -->
      <div class="mt-4 p-3 bg-dark rounded">
        <h4 class="text-warning">💬 Penilaian Pelanggan</h4>

        <!-- Tombol tambah komentar -->
        <button class="btn btn-success btn-sm mb-3" data-bs-toggle="modal" data-bs-target="#tambahModal">
          + Tambah Komentar
        </button>

        <?php
        $data = mysqli_query($koneksi, "SELECT * FROM komentar ORDER BY id_komentar DESC");
        while ($row = mysqli_fetch_assoc($data)) {
        ?>
          <div class="p-2 border-bottom">
            <strong><?= htmlspecialchars($row['nama']); ?></strong>
            <p class="text-white"><?= htmlspecialchars($row['komentar']); ?></p>
            <small class="text-secondary"><?= $row['tanggal']; ?></small>

            <!-- Tombol Edit -->
            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id_komentar'] ?>">
              Edit
            </button>
          </div>

          <!-- Modal Edit -->
          <div class="modal fade" id="editModal<?= $row['id_komentar'] ?>">
            <div class="modal-dialog">
              <div class="modal-content text-dark">
                <div class="modal-header">
                  <h5>Edit Komentar</h5>
                  <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form method="POST" action="update_komentar.php">
                  <div class="modal-body">
                    <input type="hidden" name="id_komentar" value="<?= $row['id_komentar']; ?>">

                    <label>Nama:</label>
                    <input type="text" class="form-control" name="nama" value="<?= $row['nama']; ?>" required>

                    <label class="mt-2">Komentar:</label>
                    <textarea class="form-control" name="komentar" required><?= $row['komentar']; ?></textarea>
                  </div>

                  <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                  </div>
                </form>

              </div>
            </div>
          </div>

        <?php } ?>
      </div>
      <!-- ⭐ END KOMENTAR -->
    </div>

    <div class="col-md-4">
      <div class="alert alert-info">Promo: Beli 2 dapat 1 croissant gratis!</div>

      <a href="menu.php" class="btn btn-primary w-100 mb-2">Lihat Menu</a>
      <a href="pesanan.php" class="btn btn-success w-100 mb-3">Buat Pesanan</a>

      <div class="card bg-dark text-white">
        <div class="card-body">
          <h5 class="card-title">Alamat Kami</h5>
          <p class="card-text">
            HM Coffe<br>
            Jl. Pallantikan<br>
            Telp: (085) 3981-2500-4
          </p>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Tambah Komentar -->
<div class="modal fade" id="tambahModal">
  <div class="modal-dialog">
    <div class="modal-content text-dark">
      <div class="modal-header">
        <h5>Tambah Komentar</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form method="POST" action="tambah_komentar.php">
        <div class="modal-body">
          <label>Nama:</label>
          <input name="nama" class="form-control" required>

          <label class="mt-2">Komentar:</label>
          <textarea name="komentar" class="form-control" required></textarea>
        </div>

        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Tambah</button>
        </div>
      </form>

    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
