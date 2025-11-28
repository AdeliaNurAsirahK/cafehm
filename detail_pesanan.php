<?php
// ...existing code...
session_start();
include 'koneksi.php';
if (!isset($_SESSION['username'])) { header("Location: login.php"); exit; }
$username = $_SESSION['username'];
$success = $error = '';

// Tambah pesanan (nama pelanggan bisa diketik — buat baru jika belum ada)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan_pesanan'])) {
    $pelanggan_input = mysqli_real_escape_string($koneksi, $_POST['pelanggan_input'] ?? '');
    $menu = mysqli_real_escape_string($koneksi, $_POST['menu'] ?? '');
    $jumlah = mysqli_real_escape_string($koneksi, $_POST['jumlah'] ?? '');
    $tanggal_pesanan = mysqli_real_escape_string($koneksi, $_POST['tanggal_pesanan'] ?? '');
    $id_pelanggan_final = '';

    if ($pelanggan_input !== '') {
        $q_check = mysqli_query($koneksi, "SELECT id_pelanggan FROM pelanggan WHERE nama = '". mysqli_real_escape_string($koneksi, $pelanggan_input) ."'");
        if (mysqli_num_rows($q_check) > 0) {
            $id_pelanggan_final = mysqli_fetch_assoc($q_check)['id_pelanggan'];
        } else {
            if (mysqli_query($koneksi, "INSERT INTO pelanggan (nama) VALUES ('". mysqli_real_escape_string($koneksi, $pelanggan_input) ."')")) {
                $id_pelanggan_final = mysqli_insert_id($koneksi);
                $success = "Pelanggan baru **". $pelanggan_input ."** telah ditambahkan. ";
            } else $error = 'Gagal menambahkan pelanggan baru: ' . mysqli_error($koneksi);
        }
    }

    if (empty($id_pelanggan_final) || empty($menu) || empty($jumlah) || empty($tanggal_pesanan)) {
        $error = empty($id_pelanggan_final) ? "Nama Pelanggan harus diisi!" : "Semua field harus diisi!";
    } elseif ($jumlah <= 0) {
        $error = "Jumlah harus lebih dari 0!";
    } else {
        $qh = mysqli_query($koneksi, "SELECT harga FROM menu WHERE id_menu = '". mysqli_real_escape_string($koneksi, $menu) ."'");
        if (mysqli_num_rows($qh) > 0) {
            if (mysqli_query($koneksi, "INSERT INTO pesanan (id_pelanggan, id_menu, jumlah, tanggal_pesan) VALUES ('$id_pelanggan_final','$menu','$jumlah','$tanggal_pesanan')")) {
                $success .= 'Pesanan berhasil ditambahkan!';
                $_POST = [];
            } else $error = 'Gagal menambahkan pesanan: ' . mysqli_error($koneksi);
        } else $error = 'Menu tidak ditemukan!';
    }
}

// Update pesanan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_pesanan'])) {
    $id_pesan = mysqli_real_escape_string($koneksi, $_POST['id_pesan_edit'] ?? '');
    $pelanggan = mysqli_real_escape_string($koneksi, $_POST['pelanggan_edit'] ?? '');
    $menu = mysqli_real_escape_string($koneksi, $_POST['menu_edit'] ?? '');
    $jumlah = mysqli_real_escape_string($koneksi, $_POST['jumlah_edit'] ?? '');
    $tanggal_pesanan = mysqli_real_escape_string($koneksi, $_POST['tanggal_pesanan_edit'] ?? '');

    if (empty($pelanggan) || empty($menu) || empty($jumlah) || empty($tanggal_pesanan)) {
        $error = "Semua field harus diisi!";
    } elseif ($jumlah <= 0) {
        $error = "Jumlah harus lebih dari 0!";
    } else {
        if (mysqli_query($koneksi, "UPDATE pesanan SET id_pelanggan='$pelanggan', id_menu='$menu', jumlah='$jumlah', tanggal_pesan='$tanggal_pesanan' WHERE id_pesan='$id_pesan'")) {
            $success = 'Pesanan berhasil diupdate!';
        } else $error = 'Gagal mengupdate pesanan: ' . mysqli_error($koneksi);
    }
}

// Hapus pesanan
if (isset($_GET['hapus'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['hapus']);
    if (mysqli_query($koneksi, "DELETE FROM pesanan WHERE id_pesan = '$id'")) {
        $success = 'Pesanan berhasil dihapus!';
        header("Location: detail_pesanan.php?success=" . urlencode($success));
        exit;
    } else $error = 'Gagal menghapus pesanan: ' . mysqli_error($koneksi);
}

if (isset($_GET['success'])) $success = htmlspecialchars($_GET['success']);
if (isset($_GET['error'])) $error = htmlspecialchars($_GET['error']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Detail Pesanan - CoffeeTime</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<style>
body{background-image:url('img/bg-coffee.jpg');background-size:cover;background-position:center;background-attachment:fixed;background-color:#f8f9fa}
.content-box{background-color:rgba(255,255,255,.97);border-radius:15px;padding:25px;box-shadow:0 0 30px rgba(0,0,0,.1);margin-bottom:20px}
.card{border:none;border-radius:15px;box-shadow:0 0 20px rgba(0,0,0,.1)}
.card-header{background:linear-gradient(135deg,#4a2c2a,#6d4c41);color:#fff;border-radius:15px 15px 0 0;font-weight:bold}
.btn-primary{background:linear-gradient(135deg,#4a2c2a,#6d4c41);border:none}
.btn-primary:hover{background:linear-gradient(135deg,#3a201e,#5d4037)}
.price-display{background:#f8f9fa;border-radius:5px;padding:10px;border-left:4px solid #4a2c2a}
.form-control:focus{border-color:#4a2c2a;box-shadow:0 0 0 .2rem rgba(74,44,42,.25)}
.btn-action{padding:.3rem .6rem;font-size:.875rem;margin:2px;border-radius:5px}
.table th{background-color:#4a2c2a;color:#fff;font-weight:600}
.table-hover tbody tr:hover{background-color:rgba(74,44,42,.05)}
.stats-card{background:linear-gradient(135deg,#4a2c2a,#6d4c41);color:#fff;border-radius:10px;padding:15px;margin-bottom:20px;text-align:center}
</style>
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container mt-4">
  <div class="content-box">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div><h3 class="mb-1"><i class="bi bi-cart-check"></i> Detail Pesanan</h3><p class="text-muted mb-0">Kelola semua pesanan CoffeeTime</p></div>
      <div class="d-flex gap-2">
        <a href="index.php" class="btn btn-outline-secondary"><i class="bi bi-house"></i> Kembali ke Home</a>
        <?php if ($username === 'admin'): ?>
          <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahModal"><i class="bi bi-plus-circle"></i> Tambah Pesanan</button>
        <?php endif; ?>
      </div>
    </div>

    <?php
      $total_pesanan = mysqli_fetch_assoc(mysqli_query($koneksi,"SELECT COUNT(*) as total FROM pesanan"))['total'] ?? 0;
      $total_pendapatan = mysqli_fetch_assoc(mysqli_query($koneksi,"SELECT SUM(p.jumlah*m.harga) as total FROM pesanan p JOIN menu m ON p.id_menu=m.id_menu"))['total'] ?? 0;
    ?>
    <div class="row mb-3">
      <div class="col-md-4"><div class="stats-card"><i class="bi bi-cart display-6"></i><h4><?php echo $total_pesanan; ?> Pesanan</h4><p class="mb-0">Total pesanan</p></div></div>
      <div class="col-md-4"><div class="stats-card"><i class="bi bi-currency-dollar display-6"></i><h4>Rp <?php echo number_format($total_pendapatan,0,',','.'); ?></h4><p class="mb-0">Total pendapatan</p></div></div>
    </div>

    <?php if($success): ?><div class="alert alert-success alert-dismissible fade show" role="alert"><i class="bi bi-check-circle-fill"></i> <?php echo htmlspecialchars($success); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
    <?php if($error): ?><div class="alert alert-danger alert-dismissible fade show" role="alert"><i class="bi bi-exclamation-triangle-fill"></i> <?php echo htmlspecialchars($error); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>

    <div class="table-responsive">
      <table class="table table-striped table-hover">
        <thead class="table-dark">
          <tr><th>No</th><th>Pelanggan</th><th>Menu</th><th>Jumlah</th><th>Harga Satuan</th><th>Total Harga</th><th>Tanggal Pesan</th><?php if ($username === 'admin') echo '<th width="150" class="text-center">Aksi</th>'; ?></tr>
        </thead>
        <tbody>
          <?php
            $no = 1;
            $q = "SELECT p.id_pesan,pl.id_pelanggan,pl.nama AS nama_pelanggan,m.id_menu,m.nama_menu,p.jumlah,m.harga,(p.jumlah*m.harga) AS total,p.tanggal_pesan FROM pesanan p JOIN pelanggan pl ON p.id_pelanggan=pl.id_pelanggan JOIN menu m ON p.id_menu=m.id_menu ORDER BY p.tanggal_pesan DESC";
            $data = mysqli_query($koneksi, $q);
            if (mysqli_num_rows($data) > 0) {
              while ($d = mysqli_fetch_assoc($data)) {
                echo '<tr>
                        <td>'.$no.'</td>
                        <td>'.htmlspecialchars($d['nama_pelanggan']).'</td>
                        <td>'.htmlspecialchars($d['nama_menu']).'</td>
                        <td>'.$d['jumlah'].'</td>
                        <td>Rp '.number_format($d['harga'],0,',','.').'</td>
                        <td>Rp '.number_format($d['total'],0,',','.').'</td>
                        <td>'.$d['tanggal_pesan'].'</td>';
                if ($username === 'admin') {
                  echo '<td class="text-center">
                          <button class="btn btn-warning btn-sm editBtn btn-action" data-id="'.$d['id_pesan'].'" data-pelanggan-id="'.$d['id_pelanggan'].'" data-menu-id="'.$d['id_menu'].'" data-jumlah="'.$d['jumlah'].'" data-tanggal="'.$d['tanggal_pesan'].'"><i class="bi bi-pencil"></i> Edit</button>
                          <button class="btn btn-danger btn-sm btn-action hapusBtn" data-id="'.$d['id_pesan'].'" data-pelanggan="'.htmlspecialchars($d['nama_pelanggan']).'"><i class="bi bi-trash"></i> Hapus</button>
                        </td>';
                }
                echo '</tr>';
                $no++;
              }
            } else {
              $cols = $username === 'admin' ? 8 : 7;
              echo '<tr><td colspan="'.$cols.'" class="text-center py-4"><i class="bi bi-cart display-4 text-muted"></i><br><span class="text-muted">Belum ada data pesanan</span></td></tr>';
            }
          ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php if ($username === 'admin') : ?>
<!-- Tambah Modal -->
<div class="modal fade" id="tambahModal" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content">
  <div class="modal-header bg-primary text-white"><h5 class="modal-title"><i class="bi bi-cart-plus"></i> Tambah Pesanan Baru</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
  <div class="modal-body">
    <form method="POST" action="">
      <div class="row">
        <div class="col-md-6"><div class="mb-3"><label class="form-label">Nama pelanggan <span class="text-danger">*</span></label>
          <input class="form-control" list="listPelanggan" id="pelanggan_input" name="pelanggan_input" placeholder="Ketik nama pelanggan..." required>
          <datalist id="listPelanggan">
            <?php $qp = mysqli_query($koneksi,"SELECT id_pelanggan,nama,no_hp FROM pelanggan ORDER BY nama"); while($p = mysqli_fetch_assoc($qp)){ echo '<option value="'.htmlspecialchars($p['nama']).'">'.$p['nama'].' - '.$p['no_hp'].'</option>'; } ?>
          </datalist></div></div>
        <div class="col-md-6"><div class="mb-3"><label class="form-label">Menu <span class="text-danger">*</span></label>
          <select class="form-select" id="menu" name="menu" required><option value="">Pilih Menu</option>
            <?php $qm = mysqli_query($koneksi,"SELECT * FROM menu ORDER BY nama_menu"); while($m = mysqli_fetch_assoc($qm)){ echo '<option value="'.$m['id_menu'].'" data-harga="'.$m['harga'].'">'.$m['nama_menu'].'</option>'; } ?>
          </select></div></div>
      </div>
      <div class="row">
        <div class="col-md-6"><div class="mb-3"><label class="form-label">Jumlah <span class="text-danger">*</span></label><input type="number" class="form-control" id="jumlah" name="jumlah" min="1" value="1" required></div></div>
        <div class="col-md-6"><div class="mb-3"><label class="form-label">Tanggal Pesanan <span class="text-danger">*</span></label><input type="datetime-local" class="form-control" id="tanggal_pesanan" name="tanggal_pesanan" required></div></div>
      </div>
      <div class="row">
        <div class="col-md-6"><div class="price-display"><label class="form-label">Harga Satuan</label><div id="harga_satuan_display" class="fw-bold text-primary">Rp 0</div><input type="hidden" id="harga_satuan" name="harga_satuan"></div></div>
        <div class="col-md-6"><div class="price-display"><label class="form-label">Total Harga</label><div id="total_harga_display" class="fw-bold text-success">Rp 0</div><input type="hidden" id="total_harga" name="total_harga"></div></div>
      </div>
      <div class="d-grid gap-2 mt-4"><button type="submit" class="btn btn-primary btn-lg" name="simpan_pesanan"><i class="bi bi-check-circle"></i> Simpan Pesanan</button></div>
    </form>
  </div>
</div></div></div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content">
  <div class="modal-header bg-warning"><h5 class="modal-title"><i class="bi bi-pencil"></i> Edit Pesanan</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
  <div class="modal-body">
    <form method="POST" action="">
      <input type="hidden" name="id_pesan_edit" id="id_pesan_edit">
      <div class="row">
        <div class="col-md-6"><div class="mb-3"><label class="form-label">Pelanggan <span class="text-danger">*</span></label>
          <select class="form-select" id="pelanggan_edit" name="pelanggan_edit" required><option value="">Pilih Pelanggan</option>
            <?php $qp2 = mysqli_query($koneksi,"SELECT * FROM pelanggan ORDER BY nama"); while($p2 = mysqli_fetch_assoc($qp2)){ echo '<option value="'.$p2['id_pelanggan'].'">'.$p2['nama'].' - '.$p2['no_hp'].'</option>'; } ?>
          </select></div></div>
        <div class="col-md-6"><div class="mb-3"><label class="form-label">Menu <span class="text-danger">*</span></label>
          <select class="form-select" id="menu_edit" name="menu_edit" required><option value="">Pilih Menu</option>
            <?php $qm2 = mysqli_query($koneksi,"SELECT * FROM menu ORDER BY nama_menu"); while($m2 = mysqli_fetch_assoc($qm2)){ echo '<option value="'.$m2['id_menu'].'">'.$m2['nama_menu'].'</option>'; } ?>
          </select></div></div>
      </div>
      <div class="row">
        <div class="col-md-6"><div class="mb-3"><label class="form-label">Jumlah <span class="text-danger">*</span></label><input type="number" class="form-control" id="jumlah_edit" name="jumlah_edit" min="1" required></div></div>
        <div class="col-md-6"><div class="mb-3"><label class="form-label">Tanggal Pesanan <span class="text-danger">*</span></label><input type="datetime-local" class="form-control" id="tanggal_pesanan_edit" name="tanggal_pesanan_edit" required></div></div>
      </div>
      <div class="d-grid gap-2 mt-4"><button type="submit" class="btn btn-success btn-lg" name="update_pesanan"><i class="bi bi-check-circle"></i> Update Pesanan</button></div>
    </form>
  </div>
</div></div></div>

<!-- Hapus Modal -->
<div class="modal fade" id="hapusModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
  <div class="modal-header bg-danger text-white"><h5 class="modal-title"><i class="bi bi-exclamation-triangle"></i> Konfirmasi Hapus</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
  <div class="modal-body"><p>Apakah Anda yakin ingin menghapus pesanan dari: <strong id="pelangganHapus"></strong>?</p><p class="text-danger"><small><i class="bi bi-info-circle"></i> Data yang dihapus tidak dapat dikembalikan!</small></p></div>
  <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><a href="#" id="btnHapusConfirm" class="btn btn-danger"><i class="bi bi-trash"></i> Ya, Hapus</a></div>
</div></div></div>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(function(){
  // Hitung harga tambah
  const hitungTotalHarga = () => {
    const menu = $('#menu')[0], jumlah = $('#jumlah')[0], hs = $('#harga_satuan_display'), th = $('#total_harga_display');
    if (!menu || !jumlah) return;
    const harga = menu.options[menu.selectedIndex].getAttribute('data-harga'), j = Number(jumlah.value);
    if (harga && j > 0) { const total = harga * j; const f = a => 'Rp ' + Number(a).toLocaleString('id-ID'); hs.text(f(harga)); th.text(f(total)); } else { hs.text('Rp 0'); th.text('Rp 0'); }
  };
  $('#menu').on('change', hitungTotalHarga);
  $('#jumlah').on('input', hitungTotalHarga);

  // set default datetime tambah
  const tanggalInput = $('#tanggal_pesanan')[0];
  if (tanggalInput && !tanggalInput.value) { const n=new Date(); const pad = s=>String(s).padStart(2,'0'); tanggalInput.value = `${n.getFullYear()}-${pad(n.getMonth()+1)}-${pad(n.getDate())}T${pad(n.getHours())}:${pad(n.getMinutes())}`; }
  hitungTotalHarga();

  // Edit button
  $('.editBtn').click(function(){
    const btn = $(this);
    $('#id_pesan_edit').val(btn.data('id'));
    $('#pelanggan_edit').val(btn.data('pelanggan-id'));
    $('#menu_edit').val(btn.data('menu-id'));
    $('#jumlah_edit').val(btn.data('jumlah'));
    const tanggal = btn.data('tanggal');
    if (tanggal) { const d = new Date(tanggal); const pad=s=>String(s).padStart(2,'0'); $('#tanggal_pesanan_edit').val(`${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`); }
    $('#editModal').modal('show');
  });

  // Hapus button
  $('.hapusBtn').click(function(){ const id=$(this).data('id'), pel=$(this).data('pelanggan'); $('#pelangganHapus').text(pel); $('#btnHapusConfirm').attr('href','?hapus='+id); $('#hapusModal').modal('show'); });

  // auto close alerts
  setTimeout(()=>{$('.alert').alert('close');},5000);

  // reset tambah modal when closed
  $('#tambahModal').on('hidden.bs.modal', function(){
    $(this).find('form')[0].reset();
    const n=new Date(), pad=s=>String(s).padStart(2,'0');
    const t = $('#tanggal_pesanan')[0]; if (t) t.value = `${n.getFullYear()}-${pad(n.getMonth()+1)}-${pad(n.getDate())}T${pad(n.getHours())}:${pad(n.getMinutes())}`;
    $('#jumlah').val('1'); hitungTotalHarga();
  });
});
</script>
</body>
</html>