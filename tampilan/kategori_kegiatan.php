<?php 
// Delete Category functionality
if(isset($_GET['id_kategori_delete'])){
    $id_kategori = mysqli_real_escape_string($koneksi, $_GET['id_kategori_delete']);
    
    // First check if there are any activities associated with this category
    $check_query = mysqli_query($koneksi, "SELECT COUNT(*) as count FROM tb_kegiatan WHERE id_kategori = '$id_kategori'");
    $check_result = mysqli_fetch_assoc($check_query);
    
    if($check_result['count'] > 0) {
        // If activities exist, show error
        echo "<script>alert('Tidak dapat menghapus kategori ini! Hapus semua kegiatan dalam kategori ini terlebih dahulu.');</script>";
    } else {
        // If no activities, proceed with deletion
        $delete_query = mysqli_query($koneksi, "DELETE FROM tb_kategori WHERE id_kategori = '$id_kategori'");
        
        if($delete_query) {
            echo "<script>alert('Kategori berhasil dihapus!'); window.location='halaman_utama.php?page=kategori_kegiatan';</script>";
        } else {
            echo "<script>alert('Gagal menghapus kategori! " . mysqli_error($koneksi) . "');</script>";
        }
    }
}

// Regular activity deletion
if(isset($_GET['id_kegiatan'])){
    $id_kegiatan = mysqli_real_escape_string($koneksi, $_GET['id_kegiatan']);
    
    // First check if the activity is used in tb_sertif
    $check_sertif_query = mysqli_query($koneksi, "SELECT COUNT(*) as count FROM tb_sertif WHERE id_kegiatan = '$id_kegiatan'");
    $check_sertif_result = mysqli_fetch_assoc($check_sertif_query);
    
    if($check_sertif_result['count'] > 0) {
        // If the activity is used in certificates, show error and prevent deletion
        echo "<script>alert('Data Tidak Bisa Di Hapus Karena Digunakan'); window.location='halaman_utama.php?page=kategori_kegiatan';</script>";
    } else {
        // If not used in certificates, proceed with deletion
        $delete_query = mysqli_query($koneksi, "DELETE FROM tb_kegiatan WHERE id_kegiatan = '$id_kegiatan'");
        
        if($delete_query) {
            echo "<script>alert('Kegiatan berhasil dihapus!'); window.location='halaman_utama.php?page=kategori_kegiatan';</script>";
        } else {
            echo "<script>alert('Gagal menghapus kegiatan! " . mysqli_error($koneksi) . "');</script>";
        }
    }
}

// Get filter values
$filter_kategori = isset($_GET['kategori']) ? $_GET['kategori'] : '';
$filter_sub_kategori = isset($_GET['sub_kategori']) ? $_GET['sub_kategori'] : '';

// Get all categories for the dropdown - DIPERBAIKI
$query_all_categories = mysqli_query($koneksi, "SELECT DISTINCT kategori FROM tb_kategori ORDER BY kategori");

// QUERY SUB KATEGORI DIPERBAIKI - menambahkan reset pada awal query
mysqli_data_seek($query_all_categories, 0);

// Query sub kategori diperbaiki untuk menampilkan data yang benar
$query_all_sub_categories = mysqli_query($koneksi, "SELECT id_kategori, sub_kategori FROM tb_kategori ORDER BY sub_kategori");
?>

<div class="container mx-auto px-4 py-6">
    <!-- Filter Form -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" action="halaman_utama.php" class="flex flex-wrap items-center gap-4">
            <input type="hidden" name="page" value="kategori_kegiatan">
            
            <div class="flex-1 min-w-[200px]">
                <label for="kategori" class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                <select id="kategori" name="kategori" class="w-full rounded border-gray-300 px-3 py-2">
                    <option value="">Semua Kategori</option>
                    <?php while($cat = mysqli_fetch_assoc($query_all_categories)): ?>
                        <option value="<?= htmlspecialchars($cat['kategori']) ?>" <?= $filter_kategori == $cat['kategori'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['kategori']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="flex-1 min-w-[200px]">
                <label for="sub_kategori" class="block text-sm font-medium text-gray-700 mb-1">Sub Kategori</label>
                <select id="sub_kategori" name="sub_kategori" class="w-full rounded border-gray-300 px-3 py-2">
                    <option value="">Semua Sub Kategori</option>
                    <?php 
                    // Cek apakah query berhasil dan ada data
                    if(mysqli_num_rows($query_all_sub_categories) > 0) {
                        while($sub_cat = mysqli_fetch_assoc($query_all_sub_categories)): 
                    ?>
                        <option value="<?= htmlspecialchars($sub_cat['id_kategori']) ?>" <?= $filter_sub_kategori == $sub_cat['id_kategori'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($sub_cat['sub_kategori']) ?>
                        </option>
                    <?php 
                        endwhile; 
                    }
                    ?>
                </select>
            </div>
            
            <div class="flex items-end">
                <button type="submit" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded transition-colors">
                    Filter
                </button>
                <?php if($filter_kategori || $filter_sub_kategori): ?>
                    <a href="halaman_utama.php?page=kategori_kegiatan" class="ml-2 px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded transition-colors">
                        Reset
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Action Buttons -->
    <div class="flex justify-end space-x-4 mb-6">
        <a href="halaman_utama.php?page=tambah_kategori" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded transition-colors">
            Tambah Kategori
        </a>
        <a href="halaman_utama.php?page=tambah_kegiatan" class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white font-medium rounded transition-colors">
            Tambah Kegiatan
        </a>
    </div>
    
    <!-- Table -->
    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="w-full table-auto">
            <thead>
                <tr class="bg-gray-100 text-gray-700 uppercase text-sm leading-normal">
                    <th class="py-3 px-6 text-center w-16">No</th>
                    <th class="py-3 px-6 text-left">Jenis Kegiatan</th>
                    <th class="py-3 px-6 text-center w-24">Point</th>
                    <th class="py-3 px-6 text-center w-48">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 text-sm">
                <?php
                    // Build query with filters
                    $query_string = "SELECT tb_kategori.*, tb_kegiatan.* 
                                    FROM tb_kategori 
                                    INNER JOIN tb_kegiatan ON tb_kategori.id_kategori = tb_kegiatan.id_kategori 
                                    WHERE 1=1";
                    
                    if($filter_kategori) {
                        $query_string .= " AND tb_kategori.kategori = '" . mysqli_real_escape_string($koneksi, $filter_kategori) . "'";
                    }
                    
                    if($filter_sub_kategori) {
                        $query_string .= " AND tb_kategori.id_kategori = '" . mysqli_real_escape_string($koneksi, $filter_sub_kategori) . "'";
                    }
                    
                    $query_string .= " ORDER BY tb_kategori.sub_kategori, tb_kegiatan.jenis_kegiatan";
                    
                    $query = mysqli_query($koneksi, $query_string);
                    $last_kategori_id = null;
                    $activity_no = 1;
                    
                    if(mysqli_num_rows($query) > 0) {
                        while($baris = mysqli_fetch_assoc($query)) {
                            if($last_kategori_id !== $baris['id_kategori']) {
                                // Category header row
                                echo "
                                <tr class='bg-gray-200 border-t-2 border-gray-300'>
                                    <td class='py-3 px-6 text-left font-bold' colspan='2'>
                                        <span class='font-medium text-xs text-gray-500'>" . htmlspecialchars($baris['kategori']) . " &raquo;</span> 
                                        " . htmlspecialchars($baris['sub_kategori']) . "
                                    </td>
                                    <td class='py-3 px-6'></td>
                                    <td class='py-3 px-6 text-center'>
                                        <div class='flex justify-center space-x-2'>
                                            <a href='halaman_utama.php?page=ubah_kategori&id_kategori=" . htmlspecialchars($baris['id_kategori']) . "' 
                                               class='px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-white rounded transition-colors'>
                                                Edit
                                            </a>
                                            <a href='halaman_utama.php?page=kategori_kegiatan&id_kategori_delete=" . htmlspecialchars($baris['id_kategori']) . "' 
                                               class='px-3 py-1 bg-red-500 hover:bg-red-600 text-white rounded transition-colors'
                                               onclick=\"return confirm('Yakin ingin menghapus kategori ini? Semua kegiatan dalam kategori ini harus dihapus terlebih dahulu.');\">
                                                Hapus
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                ";
                                $activity_no = 1;
                            }
                            
                            // Activity row
                            echo "
                            <tr class='border-b border-gray-200 hover:bg-gray-50'>
                                <td class='py-3 px-6 text-center'>{$activity_no}</td>
                                <td class='py-3 px-6 text-left'>" . htmlspecialchars($baris['jenis_kegiatan']) . "</td>
                                <td class='py-3 px-6 text-center font-medium'>" . htmlspecialchars($baris['angka_kredit']) . "</td>
                                <td class='py-3 px-6 text-center'>
                                    <div class='flex justify-center space-x-2'>
                                        <a href='halaman_utama.php?page=ubah_kegiatan&id_kegiatan=" . htmlspecialchars($baris['id_kegiatan']) . "' 
                                           class='px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-white rounded transition-colors'>
                                            Edit
                                        </a>
                                        <a href='halaman_utama.php?page=kategori_kegiatan&id_kegiatan=" . htmlspecialchars($baris['id_kegiatan']) . "' 
                                           class='px-3 py-1 bg-red-500 hover:bg-red-600 text-white rounded transition-colors'
                                           onclick=\"return confirm('Yakin ingin menghapus kegiatan ini?');\">
                                            Hapus
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            ";
                            
                            $last_kategori_id = $baris['id_kategori'];
                            $activity_no++;
                        }
                    } else {
                        echo "
                        <tr>
                            <td colspan='4' class='py-6 px-6 text-center text-gray-500'>
                                Tidak ada data yang ditemukan
                            </td>
                        </tr>
                        ";
                    }
                ?>
            </tbody>
        </table>
    </div>
</div>