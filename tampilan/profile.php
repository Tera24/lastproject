<?php
$nis = isset($_COOKIE['NIS']) ? $_COOKIE['NIS'] : '';

if (empty($nis)) {
    echo "<script>alert('Data siswa tidak ditemukan'); window.location.href='halaman_utama.php';</script>";
    exit;
}

$query_siswa = mysqli_query($koneksi, "SELECT s.*, j.jurusan FROM tb_siswa s LEFT JOIN tb_jurusan j ON s.id_jurusan = j.id_jurusan WHERE s.NIS = '$nis'");

if (!$query_siswa || mysqli_num_rows($query_siswa) == 0) {
    echo "<script>alert('Data siswa tidak ditemukan'); window.location.href='halaman_utama.php';</script>";
    exit;
}

$data_user = mysqli_fetch_assoc($query_siswa);

$query_points = mysqli_query($koneksi, "SELECT SUM(k.angka_kredit) as total_poin FROM tb_sertif s JOIN tb_kegiatan k ON s.id_kegiatan = k.id_kegiatan WHERE s.NIS = '$nis' AND s.status = 'Valid'");

$total_poin = 0;
if ($query_points && mysqli_num_rows($query_points) > 0) {
    $data_points = mysqli_fetch_assoc($query_points);
    $total_poin = $data_points['total_poin'] ?: 0;
}
?>

<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <!-- Header -->
        <div class="bg-blue-600 px-6 py-4">
            <h3 class="text-xl font-semibold text-white">Profil Pengguna</h3>
        </div>
        
        <!-- Body -->
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">NIS</label>
                    <p class="text-lg font-medium"><?= htmlspecialchars($data_user['NIS']); ?></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Nama Siswa</label>
                    <p class="text-lg font-medium"><?= htmlspecialchars($data_user['nama_siswa']); ?></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Nomor Telepon</label>
                    <p class="text-lg font-medium"><?= htmlspecialchars($data_user['no_telp']); ?></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Email</label>
                    <p class="text-lg font-medium"><?= htmlspecialchars($data_user['email']); ?></p>
                </div>
            </div>

            <!-- Point Information with Certificate Button -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mt-6">
                <div class="flex flex-wrap items-center justify-between">
                    <div class="flex flex-col sm:flex-row sm:items-center">
                        <div class="mb-3 sm:mb-0 mr-0 sm:mr-6">
                            <h5 class="text-lg font-semibold text-blue-800">Total Point</h5>
                            <p class="text-sm text-blue-600">Akumulasi dari sertifikat yang telah diterima</p>
                        </div>
                    </div>
                    
                    <div class="flex items-baseline">
                        <span class="text-3xl md:text-4xl font-bold text-blue-700"><?= htmlspecialchars($total_poin); ?></span>
                        <span class="ml-1 text-blue-600">poin</span>
                    </div>
                </div>
            </div>
            
            <!-- Buttons Container with Cetak Sertifikat on left and Edit Profile on right -->
            <div class="flex flex-wrap justify-between mt-8">
                <!-- Cetak Sertifikat Button (Left) -->
                <div>
                    <?php if ($total_poin >= 30): ?>
                    <a href="../cetak/sertifikat_skkpd/generate_sertifikat.php" 
                       class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 inline-flex items-center">
                        Cetak Sertifikat
                    </a>
                    <?php endif; ?>
                </div>
                
                <!-- Edit Profile Button (Right) -->
                <div>
                    <a href="halaman_utama.php?page=ubah_profile" 
                       class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md shadow transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Edit Profil
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>