<?php
if (isset($_GET['NIS'])) {
    $NIS = $_GET['NIS'];
    $hasil = mysqli_query($koneksi, "DELETE FROM tb_siswa WHERE NIS='$NIS'");

    if (!$hasil) {
        echo "<script>alert('Gagal menghapus data');window.location.href='halaman_utama.php?page=siswa'</script>";
    } else {
        echo "<script>alert('Berhasil menghapus data');window.location.href='halaman_utama.php?page=siswa'</script>";
    }
}

// Search functionality
$search = "";
if (isset($_GET['search'])) {
    $search = $_GET['search'];
}
?>

<div class="mx-auto max-w-7xl px-2 sm:px-4 lg:px-6 py-4 sm:py-6">
    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-4 sm:mb-6">
        <div class="px-3 sm:px-6 py-3 sm:py-5 bg-gray-50 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-3">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Data Siswa</h2>
                <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                    <!-- Search Form -->
                    <form action="halaman_utama.php" method="GET" class="flex w-full">
                        <input type="hidden" name="page" value="siswa">
                        <input 
                            type="text" 
                            name="search" 
                            placeholder="Cari siswa..." 
                            value="<?php echo htmlspecialchars($search); ?>"
                            class="px-3 py-2 border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent w-full text-sm sm:text-base"
                        >
                        <button 
                            type="submit" 
                            class="px-3 py-2 bg-green-600 text-white rounded-r-lg hover:bg-green-700 transition-all duration-200"
                        >
                        <i data-lucide="search" class="h-5 w-5"></i>
                        </button>
                    </form>
                    <!-- Add Student Button -->
                    <a href="halaman_utama.php?page=tambah_siswa" 
                       class="px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all duration-200 flex items-center justify-center gap-2 shadow-sm whitespace-nowrap text-sm sm:text-base w-full sm:w-auto"
                    >
                        + Tambah Siswa
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4 lg:gap-6">
        <?php
        // Get all student data
        $data_siswa = mysqli_query($koneksi, "SELECT * FROM tb_siswa INNER JOIN tb_jurusan USING(id_Jurusan)");
        
        // Counter variables
        $total_students = 0;
        $matched_students = 0;
        
        while ($data = mysqli_fetch_assoc($data_siswa)) {
            $total_students++;
            
            // If search is active, check if student matches search criteria
            $match = false;
            if (!empty($search)) {
                if (
                    stripos($data['nama_siswa'], $search) !== false ||
                    stripos($data['NIS'], $search) !== false ||
                    stripos($data['email'], $search) !== false ||
                    stripos($data['kelas'], $search) !== false ||
                    stripos($data['jurusan'], $search) !== false ||
                    stripos($data['angkatan'], $search) !== false
                ) {
                    $match = true;
                    $matched_students++;
                }
            } else {
                $match = true; // If no search, display all
            }
            
            // Display the student card if it matches or if no search is active
            if ($match) {
        ?>
        <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300 <?php echo (!empty($search) && stripos($data['nama_siswa'], $search) !== false) ? 'ring-2 ring-green-500' : ''; ?>">
            <div class="px-3 sm:px-5 py-2 sm:py-3 bg-gray-50 border-b border-gray-200">
                <h3 class="text-base sm:text-lg font-semibold text-gray-800 truncate"><?php echo $data['nama_siswa']; ?></h3>
            </div>
            
            <div class="px-3 sm:px-5 py-3 sm:py-4">
                <div class="grid grid-cols-2 gap-2 sm:gap-3 text-xs sm:text-sm">
                    <div class="text-gray-500">NIS:</div>
                    <div class="text-gray-700 font-medium"><?php echo $data['NIS']; ?></div>
                    
                    <div class="text-gray-500">No Absen:</div>
                    <div class="text-gray-700 font-medium"><?php echo $data['no_absen']; ?></div>
                    
                    <div class="text-gray-500">No Telp:</div>
                    <div class="text-gray-700 font-medium"><?php echo $data['no_telp']; ?></div>
                    
                    <div class="text-gray-500">Email:</div>
                    <div class="text-gray-700 font-medium truncate"><?php echo $data['email']; ?></div>
                    
                    <div class="text-gray-500">Jurusan:</div>
                    <div class="text-gray-700 font-medium"><?php echo $data['jurusan']; ?></div>
                    
                    <div class="text-gray-500">Kelas:</div>
                    <div class="text-gray-700 font-medium"><?php echo $data['kelas']; ?></div>
                    
                    <div class="text-gray-500">Angkatan:</div>
                    <div class="text-gray-700 font-medium"><?php echo $data['angkatan']; ?></div>
                </div>
            </div>
            
            <div class="px-3 sm:px-5 py-2 sm:py-3 bg-gray-50 border-t border-gray-200 flex justify-end space-x-2">
                <a href="halaman_utama.php?page=ubah_siswa&NIS=<?php echo $data['NIS']; ?>" 
                   class="px-2 sm:px-3 py-1 sm:py-1.5 bg-yellow-500 text-white text-xs font-medium rounded hover:bg-yellow-600 transition-colors shadow-sm">
                    Update
                </a>
                <a onclick="return confirm('Yakin mau hapus?');" 
                   href="halaman_utama.php?page=siswa&NIS=<?php echo $data['NIS']; ?>" 
                   class="px-2 sm:px-3 py-1 sm:py-1.5 bg-red-500 text-white text-xs font-medium rounded hover:bg-red-600 transition-colors shadow-sm">
                    Delete
                </a>
            </div>
        </div>
        <?php
            }
        }
        
        // If no students match the search
        if (!empty($search) && $matched_students == 0) {
        ?>
        <div class="col-span-1 md:col-span-2 lg:col-span-3 bg-white rounded-xl shadow-md p-4 sm:p-6 text-center">
            <p class="text-gray-600 text-sm sm:text-base">Tidak ada siswa yang cocok dengan pencarian "<?php echo htmlspecialchars($search); ?>".</p>
        </div>
        <?php
        }
        ?>
    </div>
    
    <div class="mt-4 sm:mt-6 bg-white rounded-xl shadow-md p-3 sm:p-4 text-center">
        <p class="text-gray-600 text-sm sm:text-base">Total Siswa: <span class="font-medium"><?php echo $total_students; ?></span></p>
        <?php if (!empty($search)) { ?>
            <p class="text-gray-500 mt-1 text-xs sm:text-sm">Menampilkan <?php echo $matched_students; ?> siswa yang cocok dengan pencarian: "<?php echo htmlspecialchars($search); ?>"</p>
            <a href="halaman_utama.php?page=siswa" class="text-green-600 hover:text-green-800 text-xs sm:text-sm inline-block mt-2">Reset pencarian</a>
        <?php } ?>
    </div>  
</div>