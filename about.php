<?php include 'navbar.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>About Us - CoffeeTime</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f7f7f7;
      font-family: 'Poppins', sans-serif;
    }
    h2 {
      text-align: center;
      margin-top: 20px;
      margin-bottom: 10px;
    }
    p.sub {
      text-align: center;
      color: #6c757d;
      margin-bottom: 30px;
    }
    .team-card {
      border: none;
      border-radius: 15px;
      background: #fff;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      overflow: hidden;
      transition: all 0.3s ease;
      height: 100%;
      display: flex;
      flex-direction: column;
    }
    .team-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }
    .team-card img {
      width: 100%;
      height: 300px;
      object-fit: cover;
      border-bottom: 4px solid #198754;
    }
    .team-card .card-body {
      text-align: center;
      padding: 15px;
      flex-grow: 1;
    }
    .team-card h5 {
      color: #198754;
      font-weight: 600;
      margin-bottom: 10px;
    }
    .team-card p {
      color: #333;
      font-size: 0.95rem;
      margin-bottom: 0;
    }
  </style>
</head>
<body>

<div class="container mt-4">
  <h2>Tentang Tim</h2>
  <p class="sub">Kariyawan Kelompok 3</p>

  <div class="row justify-content-center g-4">
    <?php
    // Hardcode 5 anggota
    $anggota = [
      ["nama" => "Adelia", "tugas" => "Ketua Proyek, Bertanggung jawab atas halaman pelanggan dan home, serta mengumpulan dta dari anggota", "nim" => "240209502035", "foto" => "ADELIA.jpeg"],
      ["nama" => "Fajri", "tugas" => "Membuat halaman pesanan, menangani proses pemesanan, dan mengelola data", "nim" => "123457", "foto" => "FAJRI.jpeg"],
      ["nama" => "Aisyah", "tugas" => "Pengembang Front-End - Bertanggung jawab atas koding dan desain halaman menu agar tampil menarik ", "nim" => "123458", "foto" => "AISYAH.jpeg"],
      ["nama" => "Nasyiha", "tugas" => "Pengembang - Bertanggung jawab atas halaman detail pesanan dan logika tampilan data pesanan.", "nim" => "123459", "foto" => "NASYIHA.jpeg"],
      ["nama" => "Fiqri", "tugas" => "Pengembang - Membuat halaman About Us untuk menampilkan profil anggota kelompok beserta foto.", "nim" => "123460", "foto" => "FIQRI.jpeg"]
    ];

    foreach ($anggota as $d) {
        $fotoPath = "img/".$d['foto'];
        if (!file_exists($fotoPath) || empty($d['foto'])) {
            $fotoPath = "https://via.placeholder.com/300x300.png?text=No+Image";
        }

        echo '<div class="col-lg-4 col-md-6 col-sm-12 d-flex justify-content-center">
                <div class="team-card w-100" style="max-width:320px;">
                  <img src="'.$fotoPath.'" alt="'.$d['nama'].'">
                  <div class="card-body">
                    <h5>'.$d['nama'].'</h5>
                    <p>'.$d['tugas'].'<br>NIM: '.$d['nim'].'</p>
                  </div>
                </div>
              </div>';
    }
    ?>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
