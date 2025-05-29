<?php
include "../koneksi.php";

// Cek login
if (!isset($_COOKIE['NIS']) || $_COOKIE['level_user'] !== "siswa") {
    header("Location: login.php");
    exit();
}

// Ambil data siswa
$NIS = $_COOKIE['NIS'];
$email = "";

// Ambil data siswa
$query = "SELECT * FROM tb_siswa WHERE NIS='$NIS'";
$hasil = mysqli_query($koneksi, $query);

if ($data_siswa = mysqli_fetch_assoc($hasil)) {
    $email = $data_siswa['email'];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Update email
    if (isset($_POST['update_email'])) {
        $email_baru = $_POST['email'];
        
        $query = "UPDATE tb_siswa SET email = '$email_baru' WHERE NIS='$NIS'";
        
        if (mysqli_query($koneksi, $query)) {
            echo "<script>alert('Email berhasil diperbarui');</script>";
        } else {
            echo "<script>alert('Gagal memperbarui email');</script>";
        }
    }
    
    // Update password
    if (isset($_POST['update_password'])) {
        $password_lama = $_POST['current_password'];
        $password_baru = $_POST['new_password'];
        $konfirmasi_password = $_POST['confirm_password'];
        
        // Cek password saat ini
        $query = "SELECT password FROM tb_pengguna WHERE NIS='$NIS'";
        $hasil = mysqli_query($koneksi, $query);
        $data = mysqli_fetch_assoc($hasil);
        
        if (password_verify($password_lama, $data['password'])) {
            if ($password_baru == $konfirmasi_password) {
                if (strlen($password_baru) >= 6) {
                    $password_hash = password_hash($password_baru, PASSWORD_DEFAULT);
                    
                    $query = "UPDATE tb_pengguna SET password = '$password_hash' WHERE NIS='$NIS'";
                    
                    if (mysqli_query($koneksi, $query)) {
                        echo "<script>alert('Password berhasil diperbarui');</script>";
                    } else {
                        echo "<script>alert('Gagal memperbarui password');</script>";
                    }
                } else {
                    echo "<script>alert('Password minimal 6 karakter');</script>";
                }
            } else {
                echo "<script>alert('Password baru dan konfirmasi password tidak cocok');</script>";
            }
        } else {
            echo "<script>alert('Password saat ini tidak valid');</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Pengaturan Profil Siswa</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto py-4 px-4">
        <h1 class="text-2xl font-bold text-gray-800 mb-4">Pengaturan Profil Siswa</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Email update card -->
            <div class="bg-white p-4 rounded-lg shadow-md">
                <h2 class="text-lg font-semibold text-gray-700 mb-3">Update Email</h2>
                <form method="post" action="">
                    <div class="mb-3">
                        <label for="email" class="block text-gray-700 text-sm font-medium mb-1">Email:</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" 
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </div>
                    <button type="submit" name="update_email" 
                        class="w-full bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-3 text-sm rounded-md transition duration-200">
                        Perbarui Email
                    </button>
                </form>
            </div>
            
            <!-- Password update card -->
            <div class="bg-white p-4 rounded-lg shadow-md">
                <h2 class="text-lg font-semibold text-gray-700 mb-3">Update Password</h2>
                <form method="post" action="">
                    <div class="mb-2">
                        <label for="current_password" class="block text-gray-700 text-sm font-medium mb-1">Password Saat Ini:</label>
                        <input type="password" id="current_password" name="current_password" 
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </div>
                    <div class="mb-2">
                        <label for="new_password" class="block text-gray-700 text-sm font-medium mb-1">Password Baru:</label>
                        <input type="password" id="new_password" name="new_password" 
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="block text-gray-700 text-sm font-medium mb-1">Konfirmasi Password:</label>
                        <input type="password" id="confirm_password" name="confirm_password" 
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </div>
                    <button type="submit" name="update_password" 
                        class="w-full bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-3 text-sm rounded-md transition duration-200">
                        Perbarui Password
                    </button>
                </form>
            </div>
        </div>
        
        <div class="mt-4">
            <a href="../tampilan/halaman_utama.php?page=profile" class="text-blue-500 hover:text-blue-700 text-sm">
                &larr; Kembali
            </a>
        </div>
    </div>
</body>
</html>