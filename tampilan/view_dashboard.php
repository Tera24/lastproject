<?php
// Include the connection file
include '../koneksi.php';

// Query to get total students
$sql_siswa = "SELECT COUNT(*) as total_siswa FROM tb_siswa";
$result_siswa = mysqli_query($koneksi, $sql_siswa);
$total_siswa = 0;
if ($result_siswa) {
    $row = mysqli_fetch_assoc($result_siswa);
    $total_siswa = $row["total_siswa"];
}

// Query to get total categories
$sql_kategori = "SELECT COUNT(*) as total_kategori FROM tb_kategori";
$result_kategori = mysqli_query($koneksi, $sql_kategori);
$total_kategori = 0;
if ($result_kategori) {
    $row = mysqli_fetch_assoc($result_kategori);
    $total_kategori = $row["total_kategori"];
}

// Query to get total departments
$sql_jurusan = "SELECT COUNT(*) as total_jurusan FROM tb_jurusan";
$result_jurusan = mysqli_query($koneksi, $sql_jurusan);
$total_jurusan = 0;
if ($result_jurusan) {
    $row = mysqli_fetch_assoc($result_jurusan);
    $total_jurusan = $row["total_jurusan"];
}

// Query to get total staff
$sql_pegawai = "SELECT COUNT(*) as total_pegawai FROM tb_pegawai";
$result_pegawai = mysqli_query($koneksi, $sql_pegawai);
$total_pegawai = 0;
if ($result_pegawai) {
    $row = mysqli_fetch_assoc($result_pegawai);
    $total_pegawai = $row["total_pegawai"];
}

// Query to get total sertifikat
$sql_sertif = "SELECT COUNT(*) as total_sertif FROM tb_sertif";
$result_sertif = mysqli_query($koneksi, $sql_sertif);
$total_sertif = 0;
if ($result_sertif) {
    $row = mysqli_fetch_assoc($result_sertif);
    $total_sertif = $row["total_sertif"];
}

// Query to get list of certificates - without aliases
$sql_sertif = "SELECT tb_sertif.sertif, tb_siswa.nama_siswa, tb_sertif.NIS 
               FROM tb_sertif
               JOIN tb_siswa ON tb_sertif.NIS = tb_siswa.NIS
               ORDER BY tb_sertif.tgl_upload DESC   ";
$result_sertif = mysqli_query($koneksi, $sql_sertif);

// Query to get list of staff - without aliases
$sql_list_pegawai = "SELECT tb_pegawai.nama_lengkap, tb_pegawai.username 
                    FROM tb_pegawai
                    ORDER BY tb_pegawai.nama_lengkap ASC";
$result_list_pegawai = mysqli_query($koneksi, $sql_list_pegawai);

// Get current date
$current_date = date("d F Y");
?>
<head>
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Custom styles for better responsiveness */
        @media (max-width: 640px) {
            .stats-card {
                margin-bottom: 1rem;
            }
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-4 md:py-8 max-w-7xl">
        <div class="bg-white rounded-lg shadow-md p-4 md:p-6 mb-4 md:mb-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                <h2 class="text-xl md:text-2xl font-bold text-gray-800 mb-2 md:mb-0">
                    Selamat datang, <?= isset($_COOKIE['nama_lengkap']) ? $_COOKIE['nama_lengkap'] : 'User'; ?>
                </h2>
                <div class="text-gray-600"><?php echo $current_date; ?></div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-6">
            <!-- Total Siswa -->
            <div class="bg-white rounded-lg shadow-md p-4 md:p-6 flex items-center justify-between hover:shadow-lg transition-shadow stats-card">
                <div>
                    <h3 class="text-gray-500 text-sm font-medium mb-1">Total Siswa</h3>
                    <div class="text-2xl md:text-3xl font-bold text-gray-800"><?php echo $total_siswa; ?></div>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <i class="fas fa-user-graduate text-blue-600 text-xl md:text-2xl"></i>
                </div>
            </div>

            <!-- Total Kategori -->
            <div class="bg-white rounded-lg shadow-md p-4 md:p-6 flex items-center justify-between hover:shadow-lg transition-shadow stats-card">
                <div>
                    <h3 class="text-gray-500 text-sm font-medium mb-1">Total Sub Kategori</h3>
                    <div class="text-2xl md:text-3xl font-bold text-gray-800"><?php echo $total_kategori; ?></div>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <i class="fas fa-tags text-green-600 text-xl md:text-2xl"></i>
                </div>
            </div>

            <!-- Total Jurusan -->
            <div class="bg-white rounded-lg shadow-md p-4 md:p-6 flex items-center justify-between hover:shadow-lg transition-shadow stats-card">
                <div>
                    <h3 class="text-gray-500 text-sm font-medium mb-1">Total Jurusan</h3>
                    <div class="text-2xl md:text-3xl font-bold text-gray-800"><?php echo $total_jurusan; ?></div>
                </div>
                <div class="bg-purple-100 p-3 rounded-full">
                    <i class="fas fa-book text-purple-600 text-xl md:text-2xl"></i>
                </div>
            </div>

            <!-- Total Pegawai -->
            <div class="bg-white rounded-lg shadow-md p-4 md:p-6 flex items-center justify-between hover:shadow-lg transition-shadow stats-card">
                <div>
                    <h3 class="text-gray-500 text-sm font-medium mb-1">Pegawai</h3>
                    <div class="text-2xl md:text-3xl font-bold text-gray-800"><?php echo $total_pegawai; ?></div>
                </div>
                <div class="bg-orange-100 p-3 rounded-full">
                    <i class="fas fa-user-tie text-orange-600 text-xl md:text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Sertifikat List - Simplified -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-4 md:p-6 bg-white border-b flex justify-between items-center">
                    <div class="flex items-center space-x-2">
                        <h3 class="text-lg font-medium text-gray-800">List Sertifikat</h3>
                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full"><?php echo $total_sertif; ?></span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama File</th>
                                <th scope="col" class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pemilik</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php
                            if ($result_sertif && mysqli_num_rows($result_sertif) > 0) {
                                while($row = mysqli_fetch_assoc($result_sertif)) {
                                    echo '<tr class="hover:bg-gray-50">
                                            <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <div class="flex items-center">
                                                    <span class="text-gray-500 mr-2">📄</span>
                                                    <span class="font-medium">' . $row["sertif"] . '</span>
                                                </div>
                                            </td>
                                            <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm text-gray-500">' . $row["NIS"] . '</td>
                                        </tr>';
                                }
                            } else {
                                echo '<tr><td colspan="2" class="px-4 md:px-6 py-4 text-center text-sm text-gray-500">Tidak ada sertifikat</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pegawai List - Improved Responsive Design -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-4 md:p-6 bg-white border-b flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                    <div class="flex items-center space-x-2">
                        <h3 class="text-lg font-medium text-gray-800">List Pegawai</h3>
                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full"><?php echo $total_pegawai; ?></span>
                    </div>  
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 p-2 max-h-96 overflow-y-auto">
                    <?php
                    if ($result_list_pegawai && mysqli_num_rows($result_list_pegawai) > 0) {
                        while($row = mysqli_fetch_assoc($result_list_pegawai)) {
                            $initial = strtoupper(substr($row["nama_lengkap"], 0, 1));
                            echo '<div class="p-3 flex items-center space-x-3 hover:bg-gray-50 rounded border">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold">' . $initial . '</div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">' . $row["nama_lengkap"] . '</p>
                                    </div>
                                </div>';
                        }
                    } else {
                        echo '<div class="p-4 text-center text-sm text-gray-500">Tidak ada pegawai</div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</body>