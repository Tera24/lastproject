<?php
include "../koneksi.php";

if (isset($_POST['tombol_tambah_pegawai'])) {

    $nama_lengkap = htmlspecialchars($_POST['nama_lengkap']);
    $username = htmlspecialchars($_POST['username']); // Removed strtoupper() function
    $password = htmlspecialchars($_POST['password']);
    $konfirmasi_password = htmlspecialchars($_POST['konfirmasi_pass']);

    if($password !== $konfirmasi_password){
        echo "<script>alert('password dengan konfirmasi password tidak sama');window.location.href='halaman_utama.php?page=tambah_pegawai'</script>";
    } else {
        $hasil = mysqli_query($koneksi, "INSERT INTO tb_pegawai VALUES ('$nama_lengkap', '$username')");
        $enkrip = password_hash($password, PASSWORD_DEFAULT);
        $hasil_pengguna = mysqli_query($koneksi, "INSERT INTO tb_pengguna VALUES (NULL, '$username', NULL, '$enkrip')");

        if(!$hasil_pengguna){
            echo "<script>alert('Gagal memasukkan data');window.location.href='halaman_utama.php?page=tambah_pegawai'</script>";
        } else {
            echo "<script>alert('Berhasil memasukkan data') ;window.location.href='halaman_utama.php?page=pegawai'</script>";
        }
                
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pegawai</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8 flex justify-center">
        <div class="w-full max-w-2xl">
            <!-- Header Section -->
            <div class="mb-6 text-center">
                <h1 class="text-2xl font-bold text-gray-800">Tambah Pegawai Baru</h1>
                <p class="text-gray-600">Isi form berikut untuk menambahkan pegawai baru ke sistem</p>
            </div>

            <!-- Form Card -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-6">
                    <form action="" method="post">
                        <div class="space-y-4">
                            <!-- Nama Lengkap Field -->
                            <div>
                                <label for="nama_lengkap" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <input type="text" id="nama_lengkap" name="nama_lengkap" required
                                        class="pl-10 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 border"
                                        placeholder="Masukkan nama lengkap">
                                </div>
                            </div>

                            <!-- Username Field -->
                            <div>
                                <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <input type="text" id="username" name="username" required
                                        class="pl-10 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 border"
                                        placeholder="Masukkan username">
                                </div>
                            </div>

                            <!-- Password Field -->
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <input type="password" id="password" name="password" required
                                        class="pl-10 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 border"
                                        placeholder="Masukkan password">
                                </div>
                            </div>

                            <!-- Konfirmasi Password Field -->
                            <div>
                                <label for="konfirmasi_pass" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <input type="password" id="konfirmasi_pass" name="konfirmasi_pass" required
                                        class="pl-10 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 border"
                                        placeholder="Konfirmasi password">
                                </div>
                            </div>

                            <!-- Buttons -->
                            <div class="flex items-center justify-center space-x-4 pt-4">
                                <button type="submit" name="tombol_tambah_pegawai"
                                    class="inline-flex justify-center items-center py-3 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-300 w-full sm:w-auto">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                    </svg>
                                    Tambah Pegawai
                                </button>
                                <a href="halaman_utama.php?page=pegawai"
                                    class="inline-flex justify-center items-center py-3 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-300 w-full sm:w-auto">
                                    Batal
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>