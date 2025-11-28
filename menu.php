<?php
// PASTIKAN FILE KONEKSI.PHP SUDAH ADA DAN BERISI KODE KONEKSI KE DATABASE
include 'koneksi.php'; 

$message = "";
$alert_class = "";

// 1. LOGIKA UTAMA: Tentukan Aksi yang Sedang Dilakukan (Tambah, Edit, Hapus)

// --- A. LOGIKA PENGHAPUSAN (DELETE) ---
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id_menu = mysqli_real_escape_string($koneksi, $_GET['id']);
    
    // Ambil nama foto sebelum menghapus
    $foto_query = mysqli_query($koneksi, "SELECT foto FROM menu WHERE id_menu='$id_menu'");
    $foto_data = mysqli_fetch_assoc($foto_query);
    $foto_name = $foto_data['foto'] ?? null;
    
    $query_delete = "DELETE FROM menu WHERE id_menu='$id_menu'";
    
    if (mysqli_query($koneksi, $query_delete)) {
        // Hapus file fisik foto
        $target_file = "img/" . $foto_name;
        if ($foto_name && file_exists($target_file)) {
            unlink($target_file); 
        }
        $message = "✅ Menu berhasil dihapus!";
        $alert_class = "alert-danger";
    } else {
        $message = "❌ Error: Gagal menghapus menu. " . mysqli_error($koneksi);
        $alert_class = "alert-danger";
    }
    // Hapus parameter action dan id dari URL setelah aksi selesai
    header("Location: menu.php?msg=" . urlencode($message) . "&cls=" . urlencode($alert_class));
    exit();
}

// --- B. LOGIKA TAMBAH/EDIT (CREATE/UPDATE) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_action'])) {
    $action = $_POST['submit_action']; // 'add' atau 'update'
    $nama_menu = mysqli_real_escape_string($koneksi, $_POST['nama_menu']);
    $harga = mysqli_real_escape_string($koneksi, $_POST['harga']);
    $kategori = mysqli_real_escape_string($koneksi, $_POST['kategori']);
    $id_menu = isset($_POST['id_menu']) ? mysqli_real_escape_string($koneksi, $_POST['id_menu']) : null;
    $foto_lama = isset($_POST['foto_lama']) ? mysqli_real_escape_string($koneksi, $_POST['foto_lama']) : null;
    $foto_name = $foto_lama;

    $upload_success = true;

    // Proses upload foto baru (jika ada)
    if (!empty($_FILES['foto']['name'])) {
        $foto_name = $_FILES['foto']['name'];
        $foto_tmp = $_FILES['foto']['tmp_name'];
        $target_dir = "img/";
        $target_file = $target_dir . basename($foto_name);
        
        // Cek ekstensi file sebelum memindahkan (meskipun sudah divalidasi client-side, server-side tetap perlu)
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
             $upload_success = false;
             $message = "❌ Error: Hanya file JPG, JPEG, atau PNG yang diizinkan.";
             $alert_class = "alert-danger";
        } else if (move_uploaded_file($foto_tmp, $target_file)) {
            // Hapus foto lama jika ini adalah update
            if ($action == 'update' && $foto_lama && file_exists($target_dir . $foto_lama)) {
                unlink($target_dir . $foto_lama);
            }
        } else {
            $upload_success = false;
            $message = "❌ Error: Gagal mengupload foto baru.";
            $alert_class = "alert-danger";
        }
    } else if ($action == 'add' && empty($foto_name)) {
        // Jika tambah menu, tapi foto kosong (dan seharusnya wajib)
        $upload_success = false;
        $message = "❌ Error: Foto harus diupload untuk menu baru.";
        $alert_class = "alert-danger";
    }

    if ($upload_success) {
        if ($action == 'add') {
            $query = "INSERT INTO menu (nama_menu, harga, kategori, foto) VALUES ('$nama_menu', '$harga', '$kategori', '$foto_name')";
            $msg_sukses = "✅ Menu " . $nama_menu . " berhasil ditambahkan!";
            $cls_sukses = "alert-success";
        } else { // action == 'update'
            $query = "UPDATE menu SET nama_menu='$nama_menu', harga='$harga', kategori='$kategori', foto='$foto_name' WHERE id_menu='$id_menu'";
            $msg_sukses = "✅ Menu " . $nama_menu . " berhasil diupdate!";
            $cls_sukses = "alert-warning";
        }
        
        if (mysqli_query($koneksi, $query)) {
            header("Location: menu.php?msg=" . urlencode($msg_sukses) . "&cls=" . urlencode($cls_sukses));
            exit();
        } else {
            $message = "❌ Error: Gagal memproses data. " . mysqli_error($koneksi);
            $alert_class = "alert-danger";
        }
    }
}

// --- C. AMBIL DATA JIKA AKSI TAMBAH/EDIT DIPILIH ---
$show_form = false;
$edit_data = ['id_menu' => '', 'nama_menu' => '', 'harga' => '', 'kategori' => '', 'foto' => ''];
$form_title = "Tambah Menu Baru";
$submit_btn_text = "Simpan Menu Baru";
$submit_action = 'add';

if (isset($_GET['action']) && $_GET['action'] == 'add') {
    $show_form = true;
    $form_title = "➕ Tambah Menu Baru";
    $submit_btn_text = "Simpan Menu Baru";
    $submit_action = 'add';
}

if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $id_edit = mysqli_real_escape_string($koneksi, $_GET['id']);
    $query_edit = mysqli_query($koneksi, "SELECT * FROM menu WHERE id_menu='$id_edit'");
    
    if ($query_edit && mysqli_num_rows($query_edit) > 0) {
        $edit_data = mysqli_fetch_assoc($query_edit);
        $show_form = true;
        $form_title = "✏ Edit Menu: " . htmlspecialchars($edit_data['nama_menu']);
        $submit_btn_text = "Update Menu";
        $submit_action = 'update';
    } else {
        $message = "❌ Error: Data menu untuk diedit tidak ditemukan.";
        $alert_class = "alert-danger";
    }
}

// 2. TAMPILKAN PESAN DARI REDIRECT
if (isset($_GET['msg']) && isset($_GET['cls'])) {
    $message = htmlspecialchars($_GET['msg']);
    $alert_class = htmlspecialchars($_GET['cls']);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Menu - CoffeeTime</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css"> 
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Tambahkan JQuery Validation Plugin -->
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/localization/messages_id.js"></script> <!-- Pesan dalam Bahasa Indonesia -->
    <style>
        /* Gaya dasar */
        body {
            background-image: url('img/bg-menu.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            font-family: 'Poppins', sans-serif;
            overflow-x: hidden;
            transition: background-color 0.5s; /* Transisi untuk warna latar belakang body */
        }
        .menu-section, .form-crud {
            background-color: rgba(255,255,255,0.95);
            border-radius: 10px;
            padding: 30px;
            margin-bottom: 40px;
            box-shadow: 0 0 20px rgba(0,0,0,0.15);
            transition: all 0.5s ease;
        }
        .card-img-top { height: 220px; object-fit: cover; transition: all 0.3s ease; }
        .foto-preview { max-width: 100px; height: auto; margin-top: 10px; border: 1px solid #ccc; padding: 5px; }

        /* ===== STYLE KHUSUS VALIDASI KOMENTAR MERAH ===== */
        .form-control.is-invalid {
            border-color: #dc3545 !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M6 3.5v3M6 8.5h.01'/%3e%3c/svg%3e") !important;
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
            padding-right: 2.25em !important;
        }
        
        .form-control.is-valid {
            border-color: #198754 !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%23198754' d='M2.3 6.73.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e") !important;
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
            padding-right: 2.25em !important;
        }
        
        /* Style untuk komentar error merah */
        .error {
            color: #dc3545 !important;
            font-size: 0.875em;
            font-weight: 500;
            margin-top: 0.25rem;
            display: block;
            background: linear-gradient(135deg, #ffe6e6, #ffcccc);
            padding: 8px 12px;
            border-radius: 5px;
            border-left: 4px solid #dc3545;
            animation: fadeIn 0.3s ease-in;
        }
        
        /* Style untuk komentar success hijau */
        .success {
            color: #198754 !important;
            font-size: 0.875em;
            font-weight: 500;
            margin-top: 0.25rem;
            display: block;
            background: linear-gradient(135deg, #e6f7e6, #ccffcc);
            padding: 8px 12px;
            border-radius: 5px;
            border-left: 4px solid #198754;
            animation: fadeIn 0.3s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-5px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #4a2c2a;
            box-shadow: 0 0 0 0.2rem rgba(74, 44, 42, 0.25);
        }
        
        /* Efek hover pada komentar error */
        .error:hover {
            transform: scale(1.02);
            transition: transform 0.2s ease;
        }

        /* --- CSS untuk Efek Jedag-Jedug yang Lebih Berani --- */
        
        /* Animasi Jedag-Jedug untuk Judul */
        .jedag-jedug-super { 
            animation: jedagJedugSuper 0.5s ease-in-out infinite alternate; /* Mengulang terus saat mode aktif */
        }
        @keyframes jedagJedugSuper { 
            0% { transform: scale(1); color: #0d6efd; text-shadow: 0 0 5px rgba(13, 110, 253, 0.5); } 
            100% { transform: scale(1.05); color: #dc3545; text-shadow: 0 0 10px rgba(220, 53, 69, 1); } 
        }

        /* Animasi Shake & Zoom untuk Card */
        .shake-super { 
            animation: shakeSuper 0.1s ease-in-out infinite alternate; 
            box-shadow: 0 0 30px rgba(255, 193, 7, 0.8) !important; /* Bayangan kuning */
        }
        @keyframes shakeSuper { 
            0% { transform: translateX(0) scale(1.0); } 
            100% { transform: translateX(5px) scale(1.02); } 
        }

        /* Efek Body saat Jedag Mode Aktif */
        .jedag-mode-active {
            animation: backgroundFlash 1s ease infinite;
        }
        @keyframes backgroundFlash {
            0% { background-color: rgba(255, 255, 255, 0.5); }
            50% { background-color: rgba(255, 240, 240, 0.7); } /* Sedikit Merah Muda */
            100% { background-color: rgba(255, 255, 255, 0.5); }
        }

        /* Efek pada Gambar di dalam Card saat Jedag Mode Aktif */
        .menu-card .card-img-top.jedag-image {
             filter: sepia(50%) hue-rotate(180deg);
             transform: scale(1.05);
        }
    </style>
</head>
<body>

<?php include 'navbar.php'; // Asumsi file navbar.php ada ?>

<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 id="mainTitle">☕ Daftar Menu CoffeeTime ☕</h3>
        
        <?php if (!$show_form): ?>
            <a href="menu.php?action=add" class="btn btn-success btn-lg">
                <i class="bi bi-plus-circle"></i> Tambah Menu Baru
            </a>
        <?php endif; ?>
    </div>
    
    <?php if ($show_form): ?>
    <div class="form-crud mb-5 border border-primary">
        <h3 class="text-center mb-4 text-primary"><?= $form_title ?></h3>
        
        <?php 
        // Menampilkan pesan sukses atau error
        if ($message) {
            echo '<div class="alert '.$alert_class.' alert-dismissible fade show" role="alert">
                      '. $message .'
                      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>';
        }
        ?>

        <!-- Tambahkan ID "menuForm" untuk target validasi -->
        <form method="POST" action="menu.php" enctype="multipart/form-data" id="menuForm">
            
            <input type="hidden" name="submit_action" value="<?= $submit_action ?>">
            <?php if ($submit_action == 'update'): ?>
                <input type="hidden" name="id_menu" value="<?= htmlspecialchars($edit_data['id_menu']) ?>">
                <input type="hidden" name="foto_lama" value="<?= htmlspecialchars($edit_data['foto']) ?>">
            <?php endif; ?>

            <!-- NAMA MENU -->
            <div class="mb-3">
                <label for="nama_menu" class="form-label">Nama Menu <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="nama_menu" name="nama_menu" 
                       value="<?= htmlspecialchars($edit_data['nama_menu']) ?>"
                       placeholder="Masukkan nama menu (contoh: Espresso, Cappuccino)">
                <div class="success" id="success_nama_menu" style="display: none;">
                    ✅ Nama menu valid!
                </div>
            </div>

            <!-- HARGA -->
            <div class="mb-3">
                <label for="harga" class="form-label">Harga (Rp) <span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="harga" name="harga" 
                       value="<?= htmlspecialchars($edit_data['harga']) ?>"
                       placeholder="Masukkan harga (contoh: 25000)"
                       min="1000" max="10000000">
                <div class="success" id="success_harga" style="display: none;">
                    ✅ Harga valid!
                </div>
            </div>
            
            <!-- KATEGORI -->
            <div class="mb-3">
                <label for="kategori" class="form-label">Kategori <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="kategori" name="kategori" 
                       value="<?= htmlspecialchars($edit_data['kategori']) ?>"
                       placeholder="Contoh: Minuman, Makanan, Snack">
                <div class="success" id="success_kategori" style="display: none;">
                    ✅ Kategori valid!
                </div>
            </div>
            
            <!-- FOTO -->
            <div class="mb-3">
                <label for="foto" class="form-label">Foto Menu 
                    <?php if ($submit_action == 'update'): ?>
                        (Biarkan kosong jika tidak diubah)
                    <?php else: ?>
                        <span class="text-danger">*</span>
                    <?php endif; ?>
                </label>
                <input type="file" class="form-control" id="foto" name="foto" accept=".jpg, .jpeg, .png">
                
                <?php if ($submit_action == 'update' && $edit_data['foto']): ?>
                    <div class="form-text">Foto saat ini: <?= htmlspecialchars($edit_data['foto']) ?></div>
                    <img src="img/<?= htmlspecialchars($edit_data['foto']) ?>" class="foto-preview">
                <?php endif; ?>
                
                <div class="success" id="success_foto" style="display: none;">
                    ✅ Foto valid!
                </div>
            </div>
            
            <button type="submit" class="btn btn-<?= ($submit_action == 'update' ? 'warning' : 'primary') ?> w-100 mt-3">
                <i class="bi bi-save"></i> <?= $submit_btn_text ?>
            </button>

            <a href="menu.php" class="btn btn-secondary w-100 mt-2">
                <i class="bi bi-x-circle"></i> Batalkan
            </a>
            
        </form>
    </div>
    <hr>
    <?php endif; ?>

    <div class="text-center mb-4">
        <button class="btn btn-info btn-lg" id="demoToggle">
            <i class="bi bi-magic"></i> Klik untuk Efek Spesial!
        </button>
    </div>

    <?php
$kategoriQ = mysqli_query($koneksi, "SELECT DISTINCT kategori FROM menu ORDER BY kategori ASC");

while($kat = mysqli_fetch_array($kategoriQ)){
    echo '<div class="menu-section">';
    echo '<h4 class="mb-3 text-center text-primary section-title">'.ucwords($kat['kategori']).'</h4>';
    echo '<div class="row">';

    $menuQ = mysqli_query($koneksi, "SELECT * FROM menu WHERE kategori='".$kat['kategori']."' ORDER BY nama_menu ASC");

    if (mysqli_num_rows($menuQ) == 0) {
        echo '<p class="text-center text-muted">Tidak ada menu di kategori ini.</p>';
    }

    while($item = mysqli_fetch_array($menuQ)){
        echo '
        
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm menu-card">
                <img src="img/'.$item['foto'].'" class="card-img-top" alt="'.$item['nama_menu'].'">

                <div class="card-body text-center">
                    <h5 class="menu-title">'.$item['nama_menu'].'</h5>
                    <p class="text-muted category-text">'.ucwords($item['kategori']).'</p>
                    <h6 class="text-success price-display">Rp '.number_format($item['harga'],0,',','.').'</h6>

                    <form method="POST" action="pesanan_action.php" class="mt-2 mb-3">
                        <input type="hidden" name="id_pelanggan" value="1">
                        <input type="hidden" name="id_menu" value="'.$item['id_menu'].'">

                    </form>

                    <div class="d-flex justify-content-between mt-2">
                        <a href="menu.php?action=edit&id='.$item['id_menu'].'" class="btn btn-warning btn-sm w-50 me-1">
                            <i class="bi bi-pencil-square"></i> Edit
                        </a>
                        <!-- Ganti confirm() dengan custom modal untuk kepatuhan praktik terbaik, namun karena ini kode PHP, saya biarkan untuk sementara -->
                        <a href="menu.php?action=delete&id='.$item['id_menu'].'" class="btn btn-danger btn-sm w-50 ms-1" onclick="return confirm(\'Apakah Anda yakin ingin menghapus menu '.$item['nama_menu'].'?\');">
                            <i class="bi bi-trash"></i> Hapus
                        </a>
                    </div>
                </div>
            </div>
        </div>';
    }

    echo '</div>';
    echo '</div>';
}
?>
</div>

<script>
$(document).ready(function() {
    
    // --- Logika jQuery Validation ---
    <?php if ($show_form): ?>
    $('#menuForm').validate({
        // Aturan validasi
        rules: {
            nama_menu: {
                required: true,
                minlength: 3,
                maxlength: 100
            },
            harga: {
                required: true,
                number: true,
                min: 1000, // Harga minimal Rp 1.000
                max: 10000000 // Harga maksimal Rp 10.000.000
            },
            kategori: {
                required: true,
                minlength: 3,
                maxlength: 50
            },
            foto: {
                // FOTO HANYA WAJIB SAAT submit_action adalah 'add'
                required: '<?= $submit_action ?>' === 'add', 
                extension: "jpg|jpeg|png"
            }
        },
        // Pesan error kustom
        messages: {
            nama_menu: {
                required: "Nama Menu wajib diisi.",
                minlength: "Nama Menu minimal 3 karakter.",
                maxlength: "Nama Menu maksimal 100 karakter."
            },
            harga: {
                required: "Harga wajib diisi.",
                number: "Harga harus berupa angka.",
                min: "Harga minimal Rp 1.000.",
                max: "Harga maksimal Rp 10.000.000."
            },
            kategori: {
                required: "Kategori wajib diisi.",
                minlength: "Kategori minimal 3 karakter.",
                maxlength: "Kategori maksimal 50 karakter."
            },
            foto: {
                required: "Foto Menu wajib diupload.",
                extension: "Hanya file JPG, JPEG, atau PNG yang diizinkan."
            }
        },
        // Custom error placement for Bootstrap
        errorElement: 'div',
        errorClass: 'error',
        errorPlacement: function (error, element) {
            error.insertAfter(element);
        },
        highlight: function (element, errorClass, validClass) {
            $(element).addClass('is-invalid').removeClass('is-valid');
            // Sembunyikan pesan success
            $(element).next('.success').hide();
        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).removeClass('is-invalid').addClass('is-valid');
            // Tampilkan pesan success
            $(element).next('.success').show();
        },
        submitHandler: function(form) {
            // Tampilkan loading state
            const submitBtn = $(form).find('button[type="submit"]');
            const originalText = submitBtn.html();
            
            submitBtn.html('<span class="spinner-border spinner-border-sm" role="status"></span> Menyimpan...')
                    .prop('disabled', true);
            
            // Submit form
            form.submit();
        }
    });

    // Real-time validation feedback
    $("#nama_menu").on('keyup blur', function() {
        $(this).valid();
    });

    $("#harga").on('keyup blur', function() {
        $(this).valid();
    });

    $("#kategori").on('keyup blur', function() {
        $(this).valid();
    });

    $("#foto").on('change', function() {
        $(this).valid();
    });

    <?php endif; ?>
    // --- Akhir Logika jQuery Validation ---

    let jedagMode = false;
    
    function startSuperJedagJedug() {
        // Toggle class jedag-mode-active pada body untuk efek background
        $('body').toggleClass('jedag-mode-active');
        
        // Toggle class jedag-jedug-super pada mainTitle
        $("#mainTitle").toggleClass('jedag-jedug-super');
        
        // Toggle class shake-super pada menu-card dan jedag-image pada gambar
        $(".menu-card").each(function(index) {
            const $card = $(this);
            const $img = $card.find('.card-img-top');
            
            setTimeout(() => {
                if (jedagMode) {
                    $card.addClass('shake-super');
                    $img.addClass('jedag-image');
                    // Menonaktifkan efek hover jQuery secara eksplisit
                    $card.off('mouseenter mouseleave');
                } else {
                    $card.removeClass('shake-super');
                    $img.removeClass('jedag-image');
                    // Mengaktifkan kembali efek hover jQuery
                    setupHoverEffect();
                }
            }, index * 50); // Delay yang lebih cepat untuk efek "flash"
        });
    }

    // Fungsi untuk mengaktifkan kembali efek hover
    function setupHoverEffect() {
        $(".card").off('mouseenter mouseleave'); // Hapus listener lama
        $(".card").hover(
            function() {
                if (!jedagMode) {
                    $(this).stop().animate({ marginTop: "-10px", boxShadow: "0 10px 25px rgba(0,0,0,0.2)" }, 200);
                }
            },
            function() {
                if (!jedagMode) {
                    $(this).stop().animate({ marginTop: "0px", boxShadow: "0 3px 8px rgba(0,0,0,0.1)" }, 200);
                }
            }
        );
    }
    
    // Setup awal hover effect
    setupHoverEffect();

    $("#demoToggle").on("click", function() {
        jedagMode = !jedagMode;
        startSuperJedagJedug();
        
        if (jedagMode) {
            $(this).html('<i class="bi bi-stars"></i> JEDAG-JEDUG MODE ON! 🎵');
            $(this).removeClass('btn-info').addClass('btn-danger');
        } else {
            $(this).html('<i class="bi bi-magic"></i> Klik untuk Efek Spesial!');
            $(this).removeClass('btn-danger').addClass('btn-info');
        }
    });

});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>