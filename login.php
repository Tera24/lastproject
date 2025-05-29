<?php include "koneksi.php";
if (isset($_POST['tombol_login'])) {
    $user = mysqli_real_escape_string($koneksi, $_POST['username']);
    $pass = $_POST['password'];
    
    // Ambil data pengguna berdasarkan username atau NIS
    $query = "SELECT id_pengguna, username, NIS, password FROM tb_pengguna WHERE username='$user' OR NIS='$user'";
    $result = mysqli_query($koneksi, $query);
    $data = mysqli_fetch_assoc($result);
    
    if ($data) {
        if (password_verify($pass, $data['password'])) {
            if (!empty($data['username'])) {
                // Login sebagai Operator
                $username = $data['username'];
                $result_operator = mysqli_query($koneksi, "SELECT nama_lengkap FROM tb_pegawai WHERE username='$username'");
                $nama_operator = mysqli_fetch_assoc($result_operator)['nama_lengkap'] ?? 'Operator';
                
                // Hanya menggunakan cookie
                setcookie('username', $username, time() + (60 * 60 * 24 * 7), '/');
                setcookie('level_user', 'operator', time() + (60 * 60 * 24 * 7), '/');
                setcookie('nama_lengkap', $nama_operator, time() + (60 * 60 * 24 * 7), '/');
                
                echo "<script>alert('Berhasil Login sebagai Operator');window.location.href='tampilan/halaman_utama.php?page=dashboard'</script>";
                exit;
            } elseif (!empty($data['NIS'])) {
                // Login sebagai Siswa
                $NIS = $data['NIS'];
                $result_siswa = mysqli_query($koneksi, "SELECT nama_siswa FROM tb_siswa WHERE NIS='$NIS'");
                $nama_siswa = mysqli_fetch_assoc($result_siswa)['nama_siswa'] ?? 'siswa';
                
                // Hanya menggunakan cookie
                setcookie('NIS', $NIS, time() + (60 * 60 * 24 * 7), '/');
                setcookie('level_user', 'siswa', time() + (60 * 60 * 24 * 7), '/');
                setcookie('nama_lengkap', $nama_siswa, time() + (60 * 60 * 24 * 7), '/');
                
                echo "<script>alert('Berhasil Login sebagai Siswa');window.location.href='tampilan/halaman_utama.php?page=sertifikat'</script>";
                exit;
            }
        } else {
            echo "<script>alert('Password salah!');window.location.href='login.php'</script>";
            exit;
        }
    } else {
        echo "<script>alert('Username atau NIS tidak ditemukan!');window.location.href='login.php'</script>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Sertifikasi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Card container -->
        <div class="bg-white rounded-xl shadow-xl overflow-hidden">
            <!-- Header section -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 p-6 text-center">
                <h2 class="text-2xl font-bold text-white">Selamat Datang</h2>
                <p class="text-blue-100 mt-1">Silahkan login untuk melanjutkan</p>
            </div>
            
            <!-- Form section -->
            <form action="" method="post" class="p-6 space-y-6">
                <div class="space-y-2">
                    <label for="username" class="text-sm font-medium text-gray-700 block">Username / NIS</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-user text-gray-400"></i>
                        </div>
                        <input type="text" id="username" name="username" required autocomplete="off"
                            class="pl-10 w-full py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent"
                            placeholder="Masukkan username atau NIS">
                    </div>
                </div>
                
                <div class="space-y-2">
                    <label for="password" class="text-sm font-medium text-gray-700 block">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input type="password" id="password" name="password" required autocomplete="off"
                            class="pl-10 w-full py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent"
                            placeholder="Masukkan password">
                    </div>
                </div>
                
                <button type="submit" name="tombol_login" 
                    class="w-full py-3 mt-6 bg-gradient-to-r from-blue-600 to-indigo-700 text-white font-medium rounded-lg shadow-md hover:from-blue-700 hover:to-indigo-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transform transition hover:-translate-y-0.5">
                    <i class="fas fa-sign-in-alt mr-2"></i>Login
                </button>
            </form>
            
            <!-- Footer section -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 text-center text-sm text-gray-500">
                <p>&copy; 2025 MTB. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>