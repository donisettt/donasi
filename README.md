# Sistem Manajemen Donasi Sederhana

Proyek ini adalah aplikasi web PHP sederhana untuk manajemen data donasi, yang dibuat untuk memenuhi tugas perkuliahan.

**Nama:** Doni Setiawan Wahyono
**NIM:** 23552011146
**Mata Kuliah:** Pemrograman Web 1

---

## Deskripsi Proyek

Aplikasi ini adalah sistem **CRUD (Create, Read, Update, Delete)** penuh yang memungkinkan admin untuk mengelola data transaksi donasi. Proyek ini dibangun menggunakan PHP native dengan koneksi **PDO (PHP Data Objects)** ke database MySQL.

Sesuai dengan arahan tugas, proyek ini terdiri dari 3 file utama:
1.  `db.php`: Untuk koneksi database.
2.  `index.php`: Sebagai *controller* dan *view* utama. Semua logika (Tampil, Tambah, Edit, Detail, Hapus) di-handle dalam satu file ini menggunakan parameter URL (`?action=...`).
3.  `style.css`: Untuk styling agar tampilan menjadi modern dan menarik sesuai mockup.

## Fitur Utama

* **Read (Tampil Data):** Menampilkan semua data donasi dalam tabel yang rapi dan modern.
* **Create (Tambah Data):** Formulir untuk menambahkan data donasi baru (tersedia di halaman `?action=tambah`).
* **Update (Edit Data):** Formulir untuk mengedit data donasi yang sudah ada (tersedia di halaman `?action=edit`).
* **Delete (Hapus Data):** Fungsionalitas untuk menghapus data donasi dengan konfirmasi.
* **Tampilan Detail:** Halaman *read-only* untuk melihat rincian lengkap setiap donasi (tersedia di halaman `?action=detail`).
* **Pencarian Real-time:** Fitur *live search* di halaman utama untuk mem-filter donatur secara instan menggunakan JavaScript.

## Teknologi yang Digunakan

* **Backend:** PHP (Native)
* **Database:** MySQL
* **Koneksi Database:** PDO (PHP Data Objects)
* **Frontend:** HTML5, CSS3, JavaScript (untuk live search)
* **Ikon:** Font Awesome

## Skema Database

Sistem ini menggunakan 5 tabel yang saling berelasi di database `db_donasi_lengkap`:

1.  `program_donasi` (Master data program/kampanye donasi)
2.  `donatur` (Master data orang yang berdonasi)
3.  `metode_pembayaran` (Master data metode bayar, cth: 'Transfer Bank')
4.  `status_pembayaran` (Master data status, cth: 'Berhasil', 'Pending')
5.  `transaksi_donasi` (Tabel transaksi utama yang menghubungkan ke-4 tabel master di atas)

### Relasi Tabel (phpMyAdmin)

[**Sisipkan screenshot relasi 5 tabel Anda dari phpMyAdmin Designer di sini**]

*(Cukup upload screenshot relasi Anda ke GitHub dan ganti teks di atas dengan: `![Relasi Tabel](nama_file_screenshot_anda.png)`)*

## Screenshot Tampilan

### Halaman Utama (Read & Search)
![Halaman Utama](Cuplikan%20layar%202025-11-07%20124700.png)

### Halaman Tambah Donasi (Create)
![Tambah Donasi](tambah%20donasi.png)

### Halaman Detail Donasi (Read Only)
![Detail Donasi](detail.png)