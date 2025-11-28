<?php
session_start();
include 'koneksi.php';

// Cek apakah sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];
$success = '';
$error = '';

// Proses tambah pesanan
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['simpan_pesanan'])) {
    $pelanggan_input = mysqli_real_escape_string($koneksi, $_POST['pelanggan_input']);
    $menu = mysqli_real_escape_string($koneksi, $_POST['menu']);
    $jumlah = (int)$_POST['jumlah'];
    $tanggal_pesanan = mysqli_real_escape_string($koneksi, $_POST['tanggal_pesanan']);
    
    $id_pelanggan_final = '';

    // Cek pelanggan
    if (!empty($pelanggan_input)) {
        $q_check = mysqli_query($koneksi, "SELECT id_pelanggan FROM pelanggan WHERE nama = '$pelanggan_input'");
        if (mysqli_num_rows($q_check) > 0) {
            $id_pelanggan_final = mysqli_fetch_assoc($q_check)['id_pelanggan'];
        } else {
            if (mysqli_query($koneksi, "INSERT INTO pelanggan (nama) VALUES ('$pelanggan_input')")) {
                $id_pelanggan_final = mysqli_insert_id($koneksi);
                $success = "Pelanggan baru **$pelanggan_input** telah ditambahkan.";
            } else {
                $error = 'Gagal menambahkan pelanggan baru: ' . mysqli_error($koneksi);
            }
        }
    }

    // Validasi input
    if (empty($id_pelanggan_final) || empty($menu) || $jumlah <= 0 || empty($tanggal_pesanan)) {
        $error = empty($id_pelanggan_final) ?  : "Semua field harus diisi!";
    } else {
        $query_harga = mysqli_query($koneksi, "SELECT harga FROM menu WHERE id_menu = '$menu'");
        if (mysqli_num_rows($query_harga) > 0) {
            $harga_satuan = mysqli_fetch_assoc($query_harga)['harga'];
            $total_harga = $jumlah * $harga_satuan;
            if (mysqli_query($koneksi, "INSERT INTO pesanan (id_pelanggan, id_menu, jumlah, tanggal_pesan) VALUES ('$id_pelanggan_final', '$menu', '$jumlah', '$tanggal_pesanan')")) {
                $success .= 'Pesanan berhasil ditambahkan!';
                $_POST = array();
            } else {
                $error = 'Gagal menambahkan pesanan: ' . mysqli_error($koneksi);
            }
        } else {
            $error = 'Menu tidak ditemukan!';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tambah Pesanan - CoffeeTime</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <style>
        body {
            background-image: url('img/bg-coffee.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-color: #f8f9fa;
        }
        .content-box {
            background-color: rgba(255, 255, 255, 0.97);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 0 30px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .card-header {
            background: linear-gradient(135deg, #4a2c2a, #6d4c41);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            font-weight: bold;
        }
        .btn-primary {
            background: linear-gradient(135deg, #4a2c2a, #6d4c41);
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #3a201e, #5d4037);
        }
        .price-display {
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 10px;
            border-left: 4px solid #4a2c2a;
        }
        .form-control:focus {
            border-color: #4a2c2a;
            box-shadow: 0 0 0 0.2rem rgba(74, 44, 42, 0.25);
        }
        /* Styles untuk validasi */
        .error {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: block;
        }
        .is-invalid {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }
        .is-valid {
            border-color: #198754;
            box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25);
        }
        .form-text-custom {
            font-size: 0.875rem;
            color: #6c757d;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .validation-icon {
            font-size: 0.875rem;
        }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="content-box">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h3 class="mb-1"><i class="bi bi-cart-plus"></i> Tambah Pesanan Baru</h3>
                        <p class="text-muted mb-0">Isi form untuk menambahkan pesanan baru</p>
                    </div>
                    <div>
                        <a href="detail_pesanan.php" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali ke Detail Pesanan
                        </a>
                    </div>
                </div>

                <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill"></i> <?php echo htmlspecialchars($success); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill"></i> <?php echo htmlspecialchars($error); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="bi bi-cup-hot"></i> Form Pesanan</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="" id="formPesanan">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Nama pelanggan <span class="text-danger">*</span></label>
                                        <input class="form-control" list="listPelanggan" id="pelanggan_input" name="pelanggan_input" 
                                               placeholder="Ketik nama pelanggan..." value="<?php echo isset($_POST['pelanggan_input']) ? htmlspecialchars($_POST['pelanggan_input']) : ''; ?>">
                                        <datalist id="listPelanggan">
                                            <?php
                                            $query_pelanggan_list = "SELECT nama, no_hp FROM pelanggan ORDER BY nama";
                                            $data_pelanggan_list = mysqli_query($koneksi, $query_pelanggan_list);
                                            while ($pelanggan = mysqli_fetch_array($data_pelanggan_list)) {
                                                echo '<option value="'.htmlspecialchars($pelanggan['nama']).'">'.$pelanggan['nama'].' - '.$pelanggan['no_hp'].'</option>';
                                            }
                                            ?>
                                        </datalist>
                                        <div class="form-text-custom">
                                            <i class="bi bi-info-circle validation-icon"></i>r
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Menu <span class="text-danger">*</span></label>
                                        <select class="form-select" id="menu" name="menu">
                                            <option value="">Pilih Menu</option>
                                            <?php
                                            $query_menu = "SELECT * FROM menu ORDER BY nama_menu";
                                            $data_menu = mysqli_query($koneksi, $query_menu);
                                            while ($menu = mysqli_fetch_array($data_menu)) {
                                                $selected = (isset($_POST['menu']) && $_POST['menu'] == $menu['id_menu']) ? 'selected' : '';
                                                echo '<option value="'.$menu['id_menu'].'" data-harga="'.$menu['harga'].'" '.$selected.'>'.$menu['nama_menu'].'</option>';
                                            }
                                            ?>
                                        </select>
                                        <div class="form-text-custom">
                                            <i class="bi bi-info-circle validation-icon"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Jumlah <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="jumlah" name="jumlah" min="1" 
                                               value="<?php echo isset($_POST['jumlah']) ? htmlspecialchars($_POST['jumlah']) : '1'; ?>">
                                        <div class="form-text-custom">
                                            <i class="bi bi-info-circle validation-icon"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Tanggal Pesanan <span class="text-danger">*</span></label>
                                        <input type="datetime-local" class="form-control" id="tanggal_pesanan" name="tanggal_pesanan" 
                                               value="<?php echo isset($_POST['tanggal_pesanan']) ? htmlspecialchars($_POST['tanggal_pesanan']) : ''; ?>">
                                        <div class="form-text-custom">
                                            <i class="bi bi-info-circle validation-icon"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="price-display">
                                        <label class="form-label">Harga Satuan</label>
                                        <div id="harga_satuan_display" class="fw-bold text-primary">Rp 0</div>
                                        <input type="hidden" id="harga_satuan" name="harga_satuan">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="price-display">
                                        <label class="form-label">Total Harga</label>
                                        <div id="total_harga_display" class="fw-bold text-success">Rp 0</div>
                                        <input type="hidden" id="total_harga" name="total_harga">
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" class="btn btn-primary btn-lg" name="simpan_pesanan">
                                    <i class="bi bi-check-circle"></i> Simpan Pesanan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function(){
    function formatRupiah(angka) {
        return 'Rp ' + Number(angka).toLocaleString('id-ID');
    }
    
    function hitungTotalHarga() {
        const menuSelect = document.getElementById('menu');
        const jumlahInput = document.getElementById('jumlah');
        const hargaSatuanDisplay = document.getElementById('harga_satuan_display');
        const totalHargaDisplay = document.getElementById('total_harga_display');
        
        const selectedOption = menuSelect.options[menuSelect.selectedIndex];
        const harga = selectedOption ? selectedOption.getAttribute('data-harga') : null;
        const jumlah = jumlahInput.value;
        
        if (harga && jumlah > 0) {
            const total = harga * jumlah;
            hargaSatuanDisplay.textContent = formatRupiah(harga);
            totalHargaDisplay.textContent = formatRupiah(total);
        } else {
            hargaSatuanDisplay.textContent = 'Rp 0';
            totalHargaDisplay.textContent = 'Rp 0';
        }
    }
    
    // Event listeners untuk perhitungan harga
    $('#menu').on('change', hitungTotalHarga);
    $('#jumlah').on('input', hitungTotalHarga);
    
    // Set default datetime
    const tanggalInput = document.getElementById('tanggal_pesanan');
    if (!tanggalInput.value) {
        const now = new Date();
        tanggalInput.value = now.toISOString().slice(0, 16);
    }
    
    hitungTotalHarga();

    // Auto close alerts
    setTimeout(function(){
        $('.alert').alert('close');
    }, 5000);

    // ----------------------------------------------------
    // --- Logika jQuery Validation ---
    // ----------------------------------------------------
    $('#formPesanan').validate({
        // Class untuk tampilan error (menggunakan Bootstrap)
        errorClass: 'is-invalid',
        validClass: 'is-valid',
        errorElement: 'div',

        // Penempatan pesan error
        errorPlacement: function (error, element) {
            error.addClass('error');
            // Tempatkan pesan error setelah element
            if (element.attr("name") == "pelanggan_input") {
                error.insertAfter(element.next().next()); // Setelah datalist
            } else {
                error.insertAfter(element.next()); // Setelah element langsung
            }
        },
        
        // Highlight field yang error
        highlight: function(element, errorClass, validClass) {
            $(element).addClass(errorClass).removeClass(validClass);
            // Sembunyikan form-text saat error
            $(element).nextAll('.form-text-custom').first().hide();
        },
        
        // Unhighlight field yang sudah valid
        unhighlight: function(element, errorClass, validClass) {
            $(element).removeClass(errorClass).addClass(validClass);
            // Tampilkan kembali form-text saat valid
            $(element).nextAll('.form-text-custom').first().show();
        },
        
        // Aturan validasi
        rules: {
            pelanggan_input: {
                required: true,
                minlength: 2 // Nama pelanggan minimal 2 karakter
            },
            menu: {
                required: true
            },
            jumlah: {
                required: true,
                digits: true,
                min: 1 // Jumlah harus minimal 1
            },
            tanggal_pesanan: {
                required: true
            }
        },
        
        // Pesan error kustom
        messages: {
            pelanggan_input: {
                required: "<i class='bi bi-exclamation-circle'></i> Nama Pelanggan wajib diisi",
                minlength: "<i class='bi bi-exclamation-circle'></i> Nama Pelanggan minimal 2 karakter"
            },
            menu: {
                required: "<i class='bi bi-exclamation-circle'></i> Pilihan Menu wajib diisi"
            },
            jumlah: {
                required: "<i class='bi bi-exclamation-circle'></i> Jumlah wajib diisi",
                digits: "<i class='bi bi-exclamation-circle'></i> Jumlah harus berupa angka",
                min: "<i class='bi bi-exclamation-circle'></i> Jumlah harus minimal 1"
            },
            tanggal_pesanan: {
                required: "<i class='bi bi-exclamation-circle'></i> Tanggal Pesanan wajib diisi"
            }
        },

        // Submit handler
        submitHandler: function(form) {
            // Tampilkan loading state
            const submitBtn = $(form).find('button[type="submit"]');
            const originalText = submitBtn.html();
            submitBtn.prop('disabled', true);
            submitBtn.html('<i class="bi bi-hourglass-split"></i> Menyimpan...');
            
            // Submit form
            form.submit();
            
            // Reset button setelah 3 detik (fallback)
            setTimeout(function() {
                submitBtn.prop('disabled', false);
                submitBtn.html(originalText);
            }, 3000);
        }
    });

    // Real-time validation pada input
    $('#pelanggan_input').on('blur', function() {
        $(this).valid();
    });
    
    $('#menu').on('change', function() {
        $(this).valid();
    });
    
    $('#jumlah').on('blur', function() {
        $(this).valid();
    });
    
    $('#tanggal_pesanan').on('change', function() {
        $(this).valid();
    });
});
</script>
</body>
</html>