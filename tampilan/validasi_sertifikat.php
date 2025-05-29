<?php
include '../koneksi.php';

// Menggunakan cookie untuk autentikasi
if (!isset($_COOKIE['username'])) {
    echo "<script>alert('Akses tidak diizinkan!'); window.location.href='../login.php';</script>";
    exit;
}

// Pastikan ada ID sertifikat yang dikirim
if (!isset($_GET['id_sertif'])) {
    echo "<script>alert('ID Sertifikat tidak ditemukan!'); window.location.href='halaman_utama.php?page=sertifikat';</script>";
    exit;
}

$id_sertif = $_GET['id_sertif'];

// Query untuk mendapatkan data sertifikat beserta data siswa dan kegiatan
$query = "SELECT ts.*, tk.jenis_kegiatan, tsi.nama_siswa, tsi.kelas, tsi.angkatan, tsi.no_telp, tsi.email 
          FROM tb_sertif ts 
          INNER JOIN tb_kegiatan tk ON ts.id_kegiatan = tk.id_kegiatan
          INNER JOIN tb_siswa tsi ON ts.NIS = tsi.NIS
          WHERE ts.id_sertif = ?";

$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "i", $id_sertif);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    echo "<script>alert('Data sertifikat tidak ditemukan!'); window.location.href='halaman_utama.php?page=sertifikat';</script>";
    exit;
}

$data = mysqli_fetch_assoc($result);
$sertif_url = !empty($data['sertif']) ? 'uploads/' . str_replace(' ', '%20', $data['sertif']) : null;

// Fungsi untuk menentukan warna status
function getStatusColor($status) {
    switch ($status) {
        case 'Valid':
            return 'bg-green-100 text-green-800';
        case 'Pending':
            return 'bg-yellow-100 text-yellow-800';
        case 'Tidak Valid':
            return 'bg-red-100 text-red-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
}

// Fungsi untuk memproses form validasi
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $status = $_POST['status'];
    $catatan = $_POST['catatan'];
    
    $update_query = "UPDATE tb_sertif SET status = ?, catatan = ? WHERE id_sertif = ?";
    $update_stmt = mysqli_prepare($koneksi, $update_query);
    mysqli_stmt_bind_param($update_stmt, "ssi", $status, $catatan, $id_sertif);
    
    if (mysqli_stmt_execute($update_stmt)) {
        echo "<script>alert('Status sertifikat berhasil diupdate!'); window.location.href='halaman_utama.php?page=sertifikat';</script>";
        exit;
    } else {
        $error_message = "Gagal mengupdate status: " . mysqli_error($koneksi);
    }
}
?>

<div class="container mx-auto px-4 py-6 max-w-6xl">
    <div class="mb-6 flex justify-between items-center">
        <h2 class="text-xl font-semibold text-gray-800">Validasi Sertifikat</h2>
        <a href="halaman_utama.php?page=sertifikat" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg shadow-sm transition duration-150 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Kembali
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Panel kiri: Dokumen PDF -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
            <div class="p-4 bg-gray-50 border-b border-gray-200">
                <h3 class="font-semibold text-gray-800">Dokumen Sertifikat</h3>
            </div>
            <div class="p-4 flex justify-center items-center" style="min-height: 600px;">
                <?php if ($sertif_url): ?>
                    <iframe src="<?php echo $sertif_url; ?>" width="100%" height="600" class="border border-gray-200 rounded"></iframe>
                <?php else: ?>
                    <div class="text-center p-8">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-300 mx-auto mb-3" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                        </svg>
                        <p class="text-gray-500">File sertifikat tidak tersedia</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Panel kanan: Detail siswa dan form validasi -->
        <div>
            <!-- Detail siswa -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200 mb-6">
                <div class="p-4 bg-gray-50 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h3 class="font-semibold text-gray-800">Detail Siswa</h3>
                        <span class="px-3 py-1 text-sm font-medium rounded-full <?php echo getStatusColor($data['status']); ?>">
                            <?php echo htmlspecialchars($data['status']); ?>
                        </span>
                    </div>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600 mb-2">
                                <span class="font-medium">Nama:</span> <?php echo htmlspecialchars($data['nama_siswa']); ?>
                            </p>
                            <p class="text-sm text-gray-600 mb-2">
                                <span class="font-medium">NIS:</span> <?php echo htmlspecialchars($data['NIS']); ?>
                            </p>
                            <p class="text-sm text-gray-600 mb-2">
                                <span class="font-medium">Kelas:</span> <?php echo htmlspecialchars($data['kelas']); ?>
                            </p>
                            <p class="text-sm text-gray-600 mb-2">
                                <span class="font-medium">Angkatan:</span> <?php echo htmlspecialchars($data['angkatan']); ?>
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 mb-2">
                                <span class="font-medium">Telepon:</span> <?php echo htmlspecialchars($data['no_telp']); ?>
                            </p>
                            <p class="text-sm text-gray-600 mb-2">
                                <span class="font-medium">Email:</span> <?php echo htmlspecialchars($data['email']); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detail kegiatan -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200 mb-6">
                <div class="p-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-800">Kategori Kegiatan</h3>
                </div>
                <div class="p-4">
                    <p class="text-sm text-gray-600 mb-2">
                        <span class="font-medium">Jenis Kegiatan:</span> <?php echo htmlspecialchars($data['jenis_kegiatan']); ?>
                    </p>
                </div>
            </div>

            <!-- Form validasi -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
                <div class="p-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-800">Validasi Sertifikat</h3>
                </div>
                <form method="POST" class="p-4">
                    <div class="mb-4">
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select id="status" name="status" class="w-full p-2 border border-gray-300 rounded-md">
                            <option value="Valid" <?php echo ($data['status'] == 'Valid') ? 'selected' : ''; ?>>Valid</option>
                            <option value="Pending" <?php echo ($data['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                            <option value="Tidak Valid" <?php echo ($data['status'] == 'Tidak Valid') ? 'selected' : ''; ?>>Tidak Valid</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="catatan" class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                        <textarea id="catatan" name="catatan" rows="3" class="w-full p-2 border border-gray-300 rounded-md"><?php echo htmlspecialchars($data['catatan'] ?? ''); ?></textarea>
                        <p class="mt-1 text-xs text-gray-500">Catatan opsional, direkomendasikan jika sertifikat Tidak Valid atau masih Pending.</p>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-md shadow-sm transition duration-150">
                            Simpan Validasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // JavaScript untuk menangani perubahan status
    document.addEventListener('DOMContentLoaded', function() {
        const statusSelect = document.getElementById('status');
        const catatanField = document.getElementById('catatan');
        
        statusSelect.addEventListener('change', function() {
            if (this.value === 'Tidak Valid') {
                // Hanya ubah placeholder tanpa set required
                catatanField.placeholder = 'Berikan alasan mengapa sertifikat ini tidak valid';
            } else if (this.value === 'Pending') {
                // Hanya ubah placeholder tanpa set required
                catatanField.placeholder = 'Berikan informasi yang diperlukan untuk verifikasi';
            } else {
                catatanField.placeholder = '';
            }
        });
        
        // Trigger the change event on page load
        statusSelect.dispatchEvent(new Event('change'));
    });
</script>