-- Buat database
CREATE DATABASE IF NOT EXISTS db_cafe;
USE db_cafe;

CREATE TABLE pelanggan (
  id_pelanggan INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100) NOT NULL,
  no_hp VARCHAR(20) NOT NULL,
  email VARCHAR(100),
  alamat TEXT
);

INSERT INTO pelanggan (nama, no_hp, email, alamat) VALUES
('Rina Putri', '08123456789', 'rina@gmail.com', 'Jl. Mawar No. 12'),
('Andi Pratama', '08124567890', 'andi@yahoo.com', 'Jl. Kenanga No. 8'),
('Siti Rahma', '08135557788', 'siti@gmail.com', 'Jl. Melati No. 3');

CREATE TABLE menu (
  id_menu INT AUTO_INCREMENT PRIMARY KEY,
  nama_menu VARCHAR(100) NOT NULL,
  kategori ENUM('Makanan','Minuman') NOT NULL,
  harga DECIMAL(10,2) NOT NULL,
  foto VARCHAR(255)
);

INSERT INTO menu (nama_menu, kategori, harga, foto) VALUES
('Cappuccino', 'Minuman', 25000, 'cappuccino.jpg'),
('Americano', 'Minuman', 20000, 'americano.jpg'),
('Roti Bakar', 'Makanan', 15000, 'roti_bakar.jpg'),
('Mie Goreng', 'Makanan', 22000, 'mie_goreng.jpg');

CREATE TABLE pesanan (
  id_pesan INT AUTO_INCREMENT PRIMARY KEY,
  id_pelanggan INT NOT NULL,
  id_menu INT NOT NULL,
  jumlah INT NOT NULL,
  tanggal_pesan DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_pelanggan) REFERENCES pelanggan(id_pelanggan) ON DELETE CASCADE,
  FOREIGN KEY (id_menu) REFERENCES menu(id_menu) ON DELETE CASCADE
);

INSERT INTO pesanan (id_pelanggan, id_menu, jumlah) VALUES
(1,1,2),
(2,3,1),
(3,2,1);

CREATE TABLE detail_pesanan (
  id_detail INT AUTO_INCREMENT PRIMARY KEY,
  id_pesan INT NOT NULL,
  total_harga DECIMAL(10,2),
  FOREIGN KEY (id_pesan) REFERENCES pesanan(id_pesan) ON DELETE CASCADE
);

INSERT INTO detail_pesanan (id_pesan, total_harga) VALUES
(1,50000),
(2,15000),
(3,20000);

CREATE TABLE anggota (
  id_anggota INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100),
  nim VARCHAR(20),
  tugas VARCHAR(100),
  foto VARCHAR(255)
);

INSERT INTO anggota (nama, nim, tugas, foto) VALUES
('Adel', '23123401', 'Pelanggan', 'adel.jpg'),
('Fajri', '23123402', 'Pesanan', 'fajri.jpg'),
('Aisyah', '23123403', 'Menu', 'aisyah.jpg'),
('Nayshia', '23123404', 'Detail Pesanan', 'nayshia.jpg'),
('Fiqri', '23123405', 'About Us', 'fiqri.jpg');
