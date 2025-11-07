<?php
// 1. Mulai session dan include koneksi DB
session_start();
require 'db.php';

// ===================================================================
// BAGIAN LOGIKA (PROCESSOR)
// ===================================================================

$error_message = '';
$success_message = '';

// 2. PROSES FORM (CREATE & UPDATE)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $form_action = $_POST['form_action'] ?? '';

    // Ambil semua data form
    $id_program = $_POST['id_program'];
    $id_donatur = $_POST['id_donatur'];
    $id_metode = $_POST['id_metode'];
    $id_status = $_POST['id_status'];
    $jumlah_donasi = $_POST['jumlah_donasi'];
    $tanggal_donasi = $_POST['tanggal_donasi'];
    $pesan_doa = $_POST['pesan_doa'] ?? '';

    try {
        // A. CREATE
        if ($form_action == 'tambah') {
            $sql = "INSERT INTO transaksi_donasi (id_program, id_donatur, id_metode, id_status, jumlah_donasi, tanggal_donasi, pesan_doa) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id_program, $id_donatur, $id_metode, $id_status, $jumlah_donasi, $tanggal_donasi, $pesan_doa]);
            $_SESSION['message'] = 'Data donasi berhasil ditambahkan!';
        } 
        
        // B. UPDATE
        elseif ($form_action == 'edit') {
            $id_transaksi = $_POST['id_transaksi'];
            $sql = "UPDATE transaksi_donasi SET 
                        id_program = ?, id_donatur = ?, id_metode = ?, id_status = ?, 
                        jumlah_donasi = ?, tanggal_donasi = ?, pesan_doa = ? 
                    WHERE id_transaksi = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id_program, $id_donatur, $id_metode, $id_status, $jumlah_donasi, $tanggal_donasi, $pesan_doa, $id_transaksi]);
            $_SESSION['message'] = 'Data donasi berhasil diupdate!';
        }
        
        header("Location: index.php");
        exit;

    } catch (PDOException $e) {
        $error_message = "Operasi gagal: " . $e->getMessage();
    }
}

// 3. TENTUKAN AKSI DARI URL
$action = $_GET['action'] ?? 'tampil'; // Default: 'tampil'
$id = $_GET['id'] ?? null;

// 4. PROSES AKSI HAPUS (DELETE)
if ($action == 'hapus' && $id) {
    try {
        $sql = "DELETE FROM transaksi_donasi WHERE id_transaksi = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $_SESSION['message'] = 'Data donasi berhasil dihapus!';
    } catch (PDOException $e) {
        $_SESSION['message'] = "Gagal menghapus data: " . $e->getMessage();
    }
    header("Location: index.php");
    exit;
}

// 5. AMBIL DATA UNTUK DROPDOWN
$program_list = $pdo->query("SELECT * FROM program_donasi ORDER BY nama_program")->fetchAll();
$donatur_list = $pdo->query("SELECT * FROM donatur ORDER BY nama_donatur")->fetchAll();
$metode_list = $pdo->query("SELECT * FROM metode_pembayaran ORDER BY nama_metode")->fetchAll();
$status_list = $pdo->query("SELECT * FROM status_pembayaran ORDER BY nama_status")->fetchAll();


// Cek pesan sukses dari session
if (isset($_SESSION['message'])) {
    $success_message = $_SESSION['message'];
    unset($_SESSION['message']);
}

// ===================================================================
// BAGIAN TAMPILAN (VIEW)
// ===================================================================
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <?php
        if ($action == 'tambah') $page_title = 'Tambah Donasi';
        elseif ($action == 'edit') $page_title = 'Edit Donasi';
        elseif ($action == 'detail') $page_title = 'Detail Donasi';
        else $page_title = 'Manajemen Donasi';
    ?>
    <title><?php echo $page_title; ?></title>
    
    <link rel="stylesheet" href="style.css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

    <div class="container">
        
        <?php
        // 7. Router Tampilan
        switch ($action):
        
        // ======================================
        // CASE: TAMBAH DATA (tambah.php)
        // ======================================
        case 'tambah':
        ?>
            <h2>Tambah Donasi</h2>
            
            <?php if ($error_message): ?>
                <div class="alert alert-error"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <form action="index.php" method="POST">
                <input type="hidden" name="form_action" value="tambah">
                <div class="form-grid">
                    
                    <div class="form-group">
                        <label for="id_program">Program Donasi</label>
                        <select id="id_program" name="id_program" required>
                            <option value="">-- Pilih Program --</option>
                            <?php foreach ($program_list as $item): ?>
                                <option value="<?php echo $item['id_program']; ?>"><?php echo htmlspecialchars($item['nama_program']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="id_status">Status Pembayaran</label>
                        <select id="id_status" name="id_status" required>
                            <option value="">-- Pilih Status --</option>
                            <?php foreach ($status_list as $item): ?>
                                <option value="<?php echo $item['id_status']; ?>" <?php echo ($item['nama_status'] == 'Berhasil') ? 'selected' : ''; ?>><?php echo htmlspecialchars($item['nama_status']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="id_donatur">Donatur</label>
                        <select id="id_donatur" name="id_donatur" required>
                            <option value="">-- Pilih Donatur --</option>
                            <?php foreach ($donatur_list as $item): ?>
                                <option value="<?php echo $item['id_donatur']; ?>"><?php echo htmlspecialchars($item['nama_donatur']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="tanggal_donasi">Tanggal Donasi</label>
                        <input type="date" id="tanggal_donasi" name="tanggal_donasi" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="jumlah_donasi">Jumlah Donasi (Rp)</label>
                        <input type="number" id="jumlah_donasi" name="jumlah_donasi" placeholder="cth: 100000" required>
                    </div>

                    <div class="form-group">
                        <label for="id_metode">Metode Pembayaran</label>
                        <select id="id_metode" name="id_metode" required>
                            <option value="">-- Pilih Metode --</option>
                            <?php foreach ($metode_list as $item): ?>
                                <option value="<?php echo $item['id_metode']; ?>"><?php echo htmlspecialchars($item['nama_metode']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group full-width">
                        <label for="pesan_doa">Pesan / Doa</label>
                        <textarea id="pesan_doa" name="pesan_doa" placeholder="Tuliskan pesan atau doa (opsional)..."></textarea>
                    </div>

                    <div class="form-actions">
                        <a href="index.php" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan Data</button>
                    </div>
                </div>
            </form>

        <?php
        break;

        // ======================================
        // CASE: EDIT DATA (edit.php)
        // ======================================
        case 'edit':
            $stmt = $pdo->prepare("SELECT * FROM transaksi_donasi WHERE id_transaksi = ?");
            $stmt->execute([$id]);
            $item_edit = $stmt->fetch();
            
            if (!$item_edit) { echo "Data tidak ditemukan!"; break; }
        ?>
            <h2>Edit Donasi</h2>
            
            <?php if ($error_message): ?>
                <div class="alert alert-error"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <form action="index.php" method="POST">
                <input type="hidden" name="form_action" value="edit">
                <input type="hidden" name="id_transaksi" value="<?php echo $item_edit['id_transaksi']; ?>">
                <div class="form-grid">
                    
                    <div class="form-group">
                        <label for="id_program">Program Donasi</label>
                        <select id="id_program" name="id_program" required>
                            <?php foreach ($program_list as $item): ?>
                                <option value="<?php echo $item['id_program']; ?>" <?php echo ($item['id_program'] == $item_edit['id_program']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($item['nama_program']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="id_status">Status Pembayaran</label>
                        <select id="id_status" name="id_status" required>
                            <?php foreach ($status_list as $item): ?>
                                <option value="<?php echo $item['id_status']; ?>" <?php echo ($item['id_status'] == $item_edit['id_status']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($item['nama_status']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="id_donatur">Donatur</label>
                        <select id="id_donatur" name="id_donatur" required>
                            <?php foreach ($donatur_list as $item): ?>
                                <option value="<?php echo $item['id_donatur']; ?>" <?php echo ($item['id_donatur'] == $item_edit['id_donatur']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($item['nama_donatur']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="tanggal_donasi">Tanggal Donasi</label>
                        <input type="date" id="tanggal_donasi" name="tanggal_donasi" value="<?php echo $item_edit['tanggal_donasi']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="jumlah_donasi">Jumlah Donasi (Rp)</label>
                        <input type="number" id="jumlah_donasi" name="jumlah_donasi" value="<?php echo $item_edit['jumlah_donasi']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="id_metode">Metode Pembayaran</label>
                        <select id="id_metode" name="id_metode" required>
                            <?php foreach ($metode_list as $item): ?>
                                <option value="<?php echo $item['id_metode']; ?>" <?php echo ($item['id_metode'] == $item_edit['id_metode']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($item['nama_metode']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group full-width">
                        <label for="pesan_doa">Pesan / Doa</label>
                        <textarea id="pesan_doa" name="pesan_doa"><?php echo htmlspecialchars($item_edit['pesan_doa']); ?></textarea>
                    </div>

                    <div class="form-actions">
                        <a href="index.php" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Update Data</button>
                    </div>
                </div>
            </form>

        <?php
        break;

        // ======================================
        // CASE: DETAIL DATA (detail.php)
        // ======================================
        case 'detail':
            $stmt = $pdo->prepare("
                SELECT t.*, p.nama_program, d.nama_donatur, m.nama_metode, s.nama_status
                FROM transaksi_donasi t
                JOIN program_donasi p ON t.id_program = p.id_program
                JOIN donatur d ON t.id_donatur = d.id_donatur
                JOIN metode_pembayaran m ON t.id_metode = m.id_metode
                JOIN status_pembayaran s ON t.id_status = s.id_status
                WHERE t.id_transaksi = ?
            ");
            $stmt->execute([$id]);
            $item_detail = $stmt->fetch();
            
            if (!$item_detail) { echo "Data tidak ditemukan!"; break; }
        ?>
            <h2>Detail Donasi</h2>

            <form>
                <div class="form-grid">
                    
                    <div class="form-group">
                        <label>Program Donasi</label>
                        <input type="text" value="<?php echo htmlspecialchars($item_detail['nama_program']); ?>" readonly>
                    </div>

                    <div class="form-group">
                        <label>Status Pembayaran</label>
                        <input type="text" value="<?php echo htmlspecialchars($item_detail['nama_status']); ?>" readonly>
                    </div>

                    <div class="form-group">
                        <label>Donatur</label>
                        <input type="text" value="<?php echo htmlspecialchars($item_detail['nama_donatur']); ?>" readonly>
                    </div>

                    <div class="form-group">
                        <label>Tanggal Donasi</label>
                        <input type="text" value="<?php echo date('d F Y', strtotime($item_detail['tanggal_donasi'])); ?>" readonly>
                    </div>

                    <div class="form-group">
                        <label>Jumlah Donasi (Rp)</label>
                        <input type="text" value="<?php echo 'Rp ' . number_format($item_detail['jumlah_donasi'], 0, ',', '.'); ?>" readonly>
                    </div>

                    <div class="form-group">
                        <label>Metode Pembayaran</label>
                        <input type="text" value="<?php echo htmlspecialchars($item_detail['nama_metode']); ?>" readonly>
                    </div>

                    <div class="form-group full-width">
                        <label>Pesan / Doa</label>
                        <textarea readonly><?php echo htmlspecialchars($item_detail['pesan_doa'] ? $item_detail['pesan_doa'] : '-'); ?></textarea>
                    </div>

                    <div class="form-actions" style="justify-content: flex-start;">
                        <a href="index.php" class="btn btn-primary">
                            &larr; Kembali ke data donasi
                        </a>
                    </div>
                </div>
            </form>
            
        <?php
        break;

        // ======================================
        // CASE: TAMPIL DATA (index.php)
        // ======================================
        default:
            // Ambil data untuk tabel utama
            $sql = "SELECT t.id_transaksi, d.nama_donatur, p.nama_program, t.jumlah_donasi, s.nama_status
                    FROM transaksi_donasi t
                    JOIN donatur d ON t.id_donatur = d.id_donatur
                    JOIN program_donasi p ON t.id_program = p.id_program
                    JOIN status_pembayaran s ON t.id_status = s.id_status
                    ORDER BY t.id_transaksi DESC";
            $stmt = $pdo->query($sql);
            $data_transaksi = $stmt->fetchAll();
        ?>
            <h1>Manajemen Donasi</h1>
            
            <?php if ($success_message): ?>
                <div style="padding: 15px; background-color: #dcfce7; color: #166534; border-radius: 8px; margin-bottom: 20px;">
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>

            <div class="header-toolbar">
                <div class="search-bar">
                    <span class="icon-search"><i class="fas fa-search"></i></span>
                    <input type="text" id="searchInput" placeholder="Cari donatur...">
                </div>
                <a href="index.php?action=tambah" class="btn btn-primary">Tambah Donasi Baru</a>
            </div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Donatur</th>
                        <th>Program Donasi</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="donasiTableBody">
                    <?php if ($data_transaksi): ?>
                        <?php foreach ($data_transaksi as $item): ?>
                        <tr>
                            <td><strong><?php echo $item['id_transaksi']; ?></strong></td>
                            <td><?php echo htmlspecialchars($item['nama_donatur']); ?></td>
                            <td><?php echo htmlspecialchars($item['nama_program']); ?></td>
                            <td><?php echo 'Rp ' . number_format($item['jumlah_donasi'], 0, ',', '.'); ?></td>
                            <td>
                                <?php 
                                    $status_class = '';
                                    if ($item['nama_status'] == 'Berhasil') $status_class = 'status-berhasil';
                                    elseif ($item['nama_status'] == 'Pending') $status_class = 'status-pending';
                                    elseif ($item['nama_status'] == 'Gagal') $status_class = 'status-gagal';
                                ?>
                                <span class="status-badge <?php echo $status_class; ?>">
                                    <?php echo htmlspecialchars($item['nama_status']); ?>
                                </span>
                            </td>
                            <td class="action-buttons">
                                <a href="index.php?action=detail&id=<?php echo $item['id_transaksi']; ?>" class="btn-icon view" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="index.php?action=edit&id=<?php echo $item['id_transaksi']; ?>" class="btn-icon edit" title="Edit">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                                <a href="index.php?action=hapus&id=<?php echo $item['id_transaksi']; ?>" 
                                   class="btn-icon delete" title="Hapus" 
                                   onclick="return confirm('Anda yakin ingin menghapus data donasi dari <?php echo htmlspecialchars($item['nama_donatur']); ?>?');">
                                   <i class="fas fa-trash-alt"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 30px;">
                                Belum ada data donasi yang masuk.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

        <?php
        endswitch;
        ?>

    </div> <footer>
        &copy; <?php echo date('Y'); ?> Doni Setiawan Wahyono
    </footer>

    <script>
        // Pastikan skrip ini berjalan setelah halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Ambil elemen input pencarian
            const searchInput = document.getElementById('searchInput');
            
            // 2. Ambil elemen body tabel
            const tableBody = document.getElementById('donasiTableBody');
            
            // 3. Ambil semua baris (tr) di dalam body tabel
            const allRows = tableBody.getElementsByTagName('tr');

            // 4. Tambahkan event listener 'keyup' (berjalan setiap kali tombol keyboard dilepas)
            searchInput.addEventListener('keyup', function() {
                const filter = searchInput.value.toLowerCase(); // Ambil nilai input & ubah ke huruf kecil

                // 5. Loop melalui semua baris tabel
                for (let i = 0; i < allRows.length; i++) {
                    const row = allRows[i];
                    
                    // Ambil sel (td) ke-2 (indeks 1), yaitu kolom "Donatur"
                    const nameCell = row.getElementsByTagName('td')[1]; 
                    
                    if (nameCell) {
                        const nameText = nameCell.textContent || nameCell.innerText;
                        
                        // 6. Cek apakah nama donatur mengandung teks yang dicari
                        if (nameText.toLowerCase().indexOf(filter) > -1) {
                            // Jika ya, tampilkan barisnya
                            row.style.display = ""; 
                        } else {
                            // Jika tidak, sembunyikan barisnya
                            row.style.display = "none"; 
                        }
                    }
                }
            });
        });
    </script>
    </body>
</html>