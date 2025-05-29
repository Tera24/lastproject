<?php
//npx @tailwindcss/cli -i ./tailwind/style.css -o ./tailwind/tailwind.css --watch

include '../koneksi.php';

if (!isset($_COOKIE['level_user'])) {
    echo "<script>alert('Harap login terlebih dahulu');window.location.href='../login.php';</script>";
    exit;
}   

$level_user = $_COOKIE['level_user'];
if ($level_user == 'siswa') {
    $username = isset($_COOKIE['NIS']) ? $_COOKIE['NIS'] : ''; // For students, use NIS
} else {
    $username = isset($_COOKIE['username']) ? $_COOKIE['username'] : ''; // For operators, use username
}
$nama_pengguna = "Pengguna";

if ($level_user == 'siswa') {
    $query = mysqli_query($koneksi, "SELECT nama_siswa FROM tb_siswa WHERE NIS = '$username'");
    if ($data = mysqli_fetch_assoc($query)) {
        $nama_pengguna = $data['nama_siswa'];
    }
} elseif ($level_user == 'operator') {
    $query = mysqli_query($koneksi, "SELECT nama_lengkap FROM tb_pegawai WHERE username = '$username'");
    if ($data = mysqli_fetch_assoc($query)) {
        $nama_pengguna = $data['nama_lengkap'];
    }
}

// Validasi akses halaman
$halaman_operator = ['siswa', 'pegawai', 'jurusan', 'kategori_kegiatan', 'tambah_siswa', 'ubah_siswa', 'tambah_pegawai', 'ubah_pegawai', 'tambah_jurusan', 'ubah_jurusan', 'tambah_kategori', 'tambah_kegiatan', 'ubah_kategori',];
$halaman_siswa = [''];

if(isset($_GET['page']) && (($level_user == 'siswa' && in_array($_GET['page'], $halaman_operator)) ||
    ($level_user == 'operator' && in_array($_GET['page'], $halaman_siswa)))
){
    echo "<script>alert('Anda tidak memiliki akses ke halaman ini!'); window.location.href='halaman_utama.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System SKKPd</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'soft-blue': '#7BB5FF',
                        'light-blue': '#E6F0FF',
                        'soft-red': '#FF9F9F',
                        'light-red': '#FFE6E6',
                        'soft-yellow': '#FFD88A',
                        'light-yellow': '#FFF8E6',
                    }
                }
            }
        }
    </script>
    <!-- Include Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body class="bg-gray-50">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="w-64 bg-white shadow-lg">
            <div class="p-4 border-b">
                <h4 class="text-xl font-semibold text-gray-700 text-center">SMK TI BALI GLOBAL DENPASAR</h4>
            </div>
            <nav class="p-4 space-y-2">
                <?php if (isset($_COOKIE['level_user']) && $_COOKIE['level_user'] == 'operator') : ?>
                    <a href="halaman_utama.php?page=dashboard" class="flex items-center px-4 py-3 text-gray-700 hover:bg-light-red hover:text-soft-red rounded-lg transition-all <?= (isset($_GET['page']) && $_GET['page'] == 'dashboard') ? 'bg-light-red text-soft-red font-medium' : ''; ?>">
                        <i data-lucide="layout-dashboard" class="h-5 w-5 mr-3"></i>
                        Dashboard
                    </a>
                    <a href="halaman_utama.php?page=siswa" class="flex items-center px-4 py-3 text-gray-700 hover:bg-light-blue hover:text-soft-blue rounded-lg transition-all <?= (isset($_GET['page']) && $_GET['page'] == 'siswa') ? 'bg-light-blue text-soft-blue font-medium' : ''; ?>">
                        <i data-lucide="users" class="h-5 w-5 mr-3"></i>
                        Siswa
                    </a>
                    
                    <a href="halaman_utama.php?page=jurusan" class="flex items-center px-4 py-3 text-gray-700 hover:bg-light-yellow hover:text-soft-yellow rounded-lg transition-all <?= (isset($_GET['page']) && $_GET['page'] == 'jurusan') ? 'bg-light-yellow text-yellow-600 font-medium' : ''; ?>">
                        <i data-lucide="book-open" class="h-5 w-5 mr-3"></i>
                        Jurusan
                    </a>
                    
                    <a href="halaman_utama.php?page=pegawai" class="flex items-center px-4 py-3 text-gray-700 hover:bg-light-blue hover:text-soft-blue rounded-lg transition-all <?= (isset($_GET['page']) && $_GET['page'] == 'pegawai') ? 'bg-light-blue text-soft-blue font-medium' : ''; ?>">
                        <i data-lucide="user" class="h-5 w-5 mr-3"></i>
                        Pegawai
                    </a>
                    
                    <a href="halaman_utama.php?page=kategori_kegiatan" class="flex items-center px-4 py-3 text-gray-700 hover:bg-light-yellow hover:text-soft-yellow rounded-lg transition-all <?= (isset($_GET['page']) && $_GET['page'] == 'kategori_kegiatan') ? 'bg-light-yellow text-yellow-600 font-medium' : ''; ?>">
                        <i data-lucide="folder" class="h-5 w-5 mr-3"></i>
                        Kategori Kegiatan
                    </a>
                    
                    <a href="halaman_utama.php?page=sertifikat" class="flex items-center px-4 py-3 text-gray-700 hover:bg-light-red hover:text-soft-red rounded-lg transition-all <?= (isset($_GET['page']) && $_GET['page'] == 'sertifikat') ? 'bg-light-red text-soft-red font-medium' : ''; ?>">
                        <i data-lucide="badge-check" class="h-5 w-5 mr-3"></i>
                        Sertifikat
                    </a>
                <?php elseif (isset($_COOKIE['level_user']) && $_COOKIE['level_user'] == 'siswa') : ?>
                    <a href="halaman_utama.php?page=sertifikat" class="flex items-center px-4 py-3 text-gray-700 hover:bg-light-red hover:text-soft-red rounded-lg transition-all <?= (isset($_GET['page']) && $_GET['page'] == 'sertifikat') ? 'bg-light-red text-soft-red font-medium' : ''; ?>">
                        <i data-lucide="award" class="h-5 w-5 mr-3"></i>
                        Sertifikat
                    </a>
                <?php endif; ?> 
                
                <div class="pt-4 border-t mt-4">
    <div class="relative" x-data="{ open: false }">
        <button @click="open = !open" class="flex items-center px-4 py-3 w-full text-gray-700 hover:bg-gray-100 rounded-lg transition-all">
            <i data-lucide="user-circle" class="h-5 w-5 mr-3"></i>
            <span class="flex-1 text-left"><?= isset($_COOKIE['nama_lengkap']) ? $_COOKIE['nama_lengkap'] : 'User'; ?></span>
            <i data-lucide="chevron-down" class="h-5 w-5"></i>
        </button>
        <div x-show="open" @click.away="open = false" class="absolute left-0 right-0 mt-1 bg-white rounded-md shadow-lg z-10 py-1">
            <?php if (isset($_COOKIE['level_user']) && $_COOKIE['level_user'] == 'operator'): ?>
                <a href="halaman_utama.php?page=ubah_profile" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 transition-colors">Edit Profile</a>
            <?php else: ?>
                <a href="halaman_utama.php?page=profile" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 transition-colors">Profile</a>
            <?php endif; ?>
            <a href="../logout.php" class="block px-4 py-2 text-soft-red hover:bg-light-red transition-colors">Logout</a>
        </div>
    </div>
</div>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 p-8 overflow-auto">
            <?php
            
            if (isset($_GET['page'])) {
                switch ($_GET['page']) {
                    case 'siswa':
                        include "siswa.php";
                        break;
                    case 'dashboard' :
                        include "view_dashboard.php";
                        break;
                    case 'jurusan':
                        include "jurusan.php";
                        break;
                    case 'tambah_siswa':
                        include "../tambah/tambah_siswa.php";
                        break;
                    case 'ubah_siswa':
                        include "../ubah/ubah_siswa.php";
                        break;
                    case 'tambah_jurusan':
                        include "../tambah/tambah_jurusan.php";
                        break;
                    case 'ubah_jurusan':
                        include "../ubah/ubah_jurusan.php";
                        break;
                    case 'pegawai':
                        include 'pegawai.php';
                        break;
                    case 'tambah_pegawai':
                        include "../tambah/tambah_pegawai.php";
                        break;
                    case 'ubah_pegawai':
                        include "../ubah/ubah_pegawai.php";
                        break;
                    case 'kategori_kegiatan':
                        include "kategori_kegiatan.php";
                        break;
                    case 'tambah_kegiatan':
                        include "../tambah/tambah_kegiatan.php";
                        break;
                    case 'tambah_kategori':
                        include "../tambah/tambah_kategori.php";
                        break;
                    case 'ubah_kegiatan':
                        include "../ubah/ubah_kegiatan.php";
                        break;
                    case 'ubah_kategori' :
                        include "../ubah/ubah_kategori.php";
                        break;
                    case 'sertifikat':
                        include "sertifikat.php";
                        break;
                    case 'tambah_sertifikat':
                        include "../tambah/tambah_sertifikat.php";
                        break;
                    case 'ubah_sertifikat':
                        include "../ubah/ubah_sertifikat.php";
                        break;
                    case 'kategori':
                        include "../tambah/kategori.php";
                        break;
                    case 'ubah_profile':
                        include "../ubah/ubah_profile.php";
                        break;
                    case 'profile':
                        include "profile.php";
                        break;
                    case 'valid':
                        include "validasi_sertifikat.php";
                        break;
                    default:
                        echo '<div class="bg-white shadow-md rounded-lg p-8 text-center">';
                        echo '<p class="text-xl text-gray-700">Halaman tidak ditemukan.</p>';
                        echo '</div>';
                        break;
                }
            } else {
                echo '<div class="bg-white shadow-md rounded-lg p-8 text-center">';
                echo '<h1 class="text-3xl font-bold text-soft-blue mb-4">Selamat Datang</h1>';
                echo '<p class="text-xl text-gray-700">Silahkan pilih menu di sidebar untuk melanjutkan.</p>';
                echo '</div>';
            }
            ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Initialize Lucide icons -->
    <script>
        lucide.createIcons();
    </script>
</body>
</html>