<?php
include "../koneksi.php";

// Ambil data dari database
$data_pegawai = mysqli_query($koneksi, "SELECT * FROM tb_pegawai");

// Cek apakah ada data dalam tabel
if (!$data_pegawai) {
    die("Query Error: " . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pegawai</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <!-- Header Section -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Daftar Pegawai</h1>
            <a href="halaman_utama.php?page=tambah_pegawai" 
               class="bg-green-500 hover:bg-green-600 text-white font-medium py-2 px-4 rounded-lg transition duration-300 flex items-center">
              + Tambah Pegawai
            </a>
        </div>

        <!-- Card Layout for Employees -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php
            while ($data = mysqli_fetch_assoc($data_pegawai)) {
                $nama_lengkap = htmlspecialchars($data['nama_lengkap'] ?? 'Tidak ada data');
                $username = htmlspecialchars($data['username'] ?? 'Tidak ada data');
            ?>
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-300">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="bg-blue-100 rounded-full p-3 mr-4">
                        <i data-lucide="user" class="h-5 w-5"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-semibold text-gray-800"><?= $nama_lengkap; ?></h2>
                            <p class="text-gray-600">@<?= $username; ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            }
            ?>
        </div>

        <!-- Empty State -->
        <?php if (mysqli_num_rows($data_pegawai) == 0): ?>
        <div class="text-center py-12">
            <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">Belum ada data pegawai</h3>
            <p class="mt-1 text-gray-500">Silakan tambahkan pegawai baru untuk memulai.</p>
            <div class="mt-6">
                <a href="halaman_utama.php?page=tambah_pegawai" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    Tambah Pegawai
                </a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>