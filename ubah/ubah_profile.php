<?php
include "../koneksi.php";

// Check if user is logged in
if ((!isset($_COOKIE['username']) && !isset($_COOKIE['NIS'])) || !isset($_COOKIE['level_user'])) {
    header("Location: login.php");
    exit();
}

// Get user data
$user_type = $_COOKIE['level_user']; // 'operator' or 'siswa'
$email = "";
$username = "";
$NIS = "";
$message = "";

// Determine user data based on type
if ($user_type === "operator") {
    $username = $_COOKIE['username'];
    
    // Get operator data
    $query_operator = "SELECT * FROM tb_pegawai WHERE username='$username'";
    $result_operator = mysqli_query($koneksi, $query_operator);
    
    if ($operator_data = mysqli_fetch_assoc($result_operator)) {
        // Check if email column exists in tb_pegawai
        $email = $operator_data['email'] ?? '';
    }
} else if ($user_type === "siswa") {
    $NIS = $_COOKIE['NIS'];
    
    // Get student data
    $query_student = "SELECT * FROM tb_siswa WHERE NIS='$NIS'";
    $result_student = mysqli_query($koneksi, $query_student);
    
    if ($student_data = mysqli_fetch_assoc($result_student)) {
        $email = $student_data['email'];
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update email (siswa only)
    if (isset($_POST['update_email']) && $user_type === "siswa") {
        $new_email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        
        if (filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
            $update_query = "UPDATE tb_siswa SET email = '$new_email' WHERE NIS='$NIS'";
            
            if (mysqli_query($koneksi, $update_query)) {
                $email = $new_email;
                $message = "Email berhasil diperbarui.";
            } else {
                $message = "Gagal memperbarui email: " . mysqli_error($koneksi);
            }
        } else {
            $message = "Format email tidak valid.";
        }
    }
    
    // Username update functionality has been removed for operators
    
    // Update password (all users)
    if (isset($_POST['update_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        // Check if current password is correct
        if ($user_type === "operator") {
            $password_query = "SELECT password FROM tb_pengguna WHERE username='$username'";
        } else {
            $password_query = "SELECT password FROM tb_pengguna WHERE NIS='$NIS'";
        }
        
        $password_result = mysqli_query($koneksi, $password_query);
        $password_row = mysqli_fetch_assoc($password_result);
        
        if (password_verify($current_password, $password_row['password'])) {
            if ($new_password === $confirm_password) {
                if (strlen($new_password) >= 6) {
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    
                    if ($user_type === "operator") {
                        $update_query = "UPDATE tb_pengguna SET password = '$hashed_password' WHERE username='$username'";
                    } else {
                        $update_query = "UPDATE tb_pengguna SET password = '$hashed_password' WHERE NIS='$NIS'";
                    }
                    
                    if (mysqli_query($koneksi, $update_query)) {
                        $message = "Password berhasil diperbarui.";
                    } else {
                        $message = "Gagal memperbarui password: " . mysqli_error($koneksi);
                    }
                } else {
                    $message = "Password minimal 6 karakter.";
                }
            } else {
                $message = "Password baru dan konfirmasi password tidak cocok.";
            }
        } else {
            $message = "Password saat ini tidak valid.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pengaturan Profil</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto py-4 px-4">
        <h1 class="text-2xl font-bold text-gray-800 mb-4">Pengaturan Profil</h1>
        
        <?php if (!empty($message)): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-3 mb-4 rounded" role="alert">
                <p><?php echo $message; ?></p>
            </div>
        <?php endif; ?>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <?php if ($user_type === "siswa"): ?>
                <!-- Email update card (siswa only) -->
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
            <?php endif; ?>
            
            <?php if ($user_type === "operator"): ?>
                <!-- Display operator username (read-only) -->
                <div class="bg-white p-4 rounded-lg shadow-md">
                    <h2 class="text-lg font-semibold text-gray-700 mb-3">Informasi Akun</h2>
                    <div class="mb-3">
                        <label class="block text-gray-700 text-sm font-medium mb-1">Username:</label>
                        <div class="w-full px-3 py-2 text-sm border border-gray-200 bg-gray-50 rounded-md">
                            <?php echo htmlspecialchars($username); ?>
                        </div>
                        <p class="text-sm text-gray-500 mt-1 italic">Username tidak dapat diubah oleh operator</p>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Password update card (all users) -->
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
    </div>
</body>
</html>