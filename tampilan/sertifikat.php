
<?php
include '../koneksi.php';

// Basic authentication check - simplified for localhost
$is_operator = isset($_COOKIE['username']);
$nis_user = $_COOKIE['NIS'] ?? null;

// Get filter values
$filter_kategori = isset($_GET['kategori']) ? $_GET['kategori'] : '';
$filter_sub_kategori = isset($_GET['sub_kategori']) ? $_GET['sub_kategori'] : '';
$filter_nis = isset($_GET['nis_filter']) ? $_GET['nis_filter'] : '';
$filter_status = isset($_GET['status']) ? $_GET['status'] : '';

// Get all categories for filters - only needed once
$query_all_categories = mysqli_query($koneksi, "SELECT DISTINCT kategori FROM tb_kategori ORDER BY kategori");
$query_all_sub_categories = mysqli_query($koneksi, "SELECT id_kategori, kategori, sub_kategori FROM tb_kategori ORDER BY kategori, sub_kategori");
$query_status = mysqli_query($koneksi, "SELECT DISTINCT status FROM tb_sertif ORDER BY status");

// For report modal
$query_angkatan = "SELECT DISTINCT angkatan FROM tb_siswa INNER JOIN tb_sertif USING(NIS) ORDER BY angkatan DESC";
$data_angkatan = mysqli_query($koneksi, $query_angkatan);

$query_siswa = "SELECT DISTINCT tb_siswa.NIS, tb_siswa.nama_siswa, tb_siswa.angkatan FROM tb_siswa INNER JOIN tb_sertif USING(NIS) ORDER BY tb_siswa.angkatan DESC, tb_siswa.nama_siswa ASC";
$data_siswa = mysqli_query($koneksi, $query_siswa);
?>

<div class="container mx-auto px-4 py-6 max-w-6xl">
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-2">Daftar Sertifikat</h2>
        <div class="flex flex-wrap justify-between items-center">
            <?php if (!$is_operator) { ?>
                <a href="halaman_utama.php?page=tambah_sertifikat" 
                class="bg-green-500 hover:bg-green-600 text-white font-medium py-2 px-4 rounded-lg shadow-sm transition duration-150 flex items-center mb-3">
                +  Upload Sertifikat
                </a>
            <?php } ?>
            
            <?php if ($is_operator) { ?>
                <button type="button" id="openModalButton"
                class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-lg shadow-sm transition duration-150 flex items-center mb-3">
                <i data-lucide="file-text" class="h-5 w-5 mr-3"></i>
                    Cetak Laporan
                </button>
            <?php } ?>
        </div>
    </div>

    <!-- Filter Section - Show only for operator -->
    <?php if ($is_operator) { ?>
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" action="" class="flex flex-wrap items-center gap-4">
            <input type="hidden" name="page" value="sertifikat">
            
            <div class="flex-1 min-w-[200px]">
                <label for="nis_filter" class="block text-sm font-medium text-gray-700 mb-1">NIS Siswa</label>
                <input type="text" id="nis_filter" name="nis_filter" 
                       placeholder="Masukkan NIS" 
                       class="w-full rounded border-gray-300 px-3 py-2"
                       value="<?php echo htmlspecialchars($filter_nis); ?>">
            </div>
            
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
                    if(mysqli_num_rows($query_all_sub_categories) > 0) {
                        while($sub_cat = mysqli_fetch_assoc($query_all_sub_categories)): 
                            $style = ($filter_kategori && $sub_cat['kategori'] != $filter_kategori) ? 'style="display:none;"' : '';
                            $data_kategori = 'data-kategori="' . htmlspecialchars($sub_cat['kategori']) . '"';
                    ?>
                        <option value="<?= htmlspecialchars($sub_cat['id_kategori']) ?>" 
                                <?= $filter_sub_kategori == $sub_cat['id_kategori'] ? 'selected' : '' ?>
                                <?= $data_kategori ?>
                                <?= $style ?>>
                            <?= htmlspecialchars($sub_cat['sub_kategori']) ?>
                        </option>
                    <?php 
                        endwhile; 
                    }
                    ?>
                </select>
            </div>
            
            <div class="flex-1 min-w-[200px]">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="status" name="status" class="w-full rounded border-gray-300 px-3 py-2">
                    <option value="">Semua Status</option>
                    <?php 
                    if(mysqli_num_rows($query_status) > 0) {
                        while($status = mysqli_fetch_assoc($query_status)): 
                    ?>
                        <option value="<?= htmlspecialchars($status['status']) ?>" <?= $filter_status == $status['status'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($status['status']) ?>
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
                <?php if($filter_kategori || $filter_sub_kategori || $filter_nis || $filter_status): ?>
                    <a href="halaman_utama.php?page=sertifikat" class="ml-2 px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded transition-colors">
                        Reset
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
    <?php } ?>  

    <?php
    // Build query with multiple filters - simplified
    $query = "SELECT tb_sertif.NIS, tb_siswa.nama_siswa, tb_kegiatan.jenis_kegiatan, tb_kategori.kategori, tb_kategori.sub_kategori,
              tb_sertif.id_sertif, tb_sertif.status, tb_sertif.catatan, tb_sertif.sertif 
              FROM tb_sertif 
              INNER JOIN tb_kegiatan ON tb_sertif.id_kegiatan = tb_kegiatan.id_kegiatan
              INNER JOIN tb_kategori ON tb_kegiatan.id_kategori = tb_kategori.id_kategori
              INNER JOIN tb_siswa ON tb_sertif.NIS = tb_siswa.NIS";

    $where_conditions = [];

    // If not operator and has NIS, filter by user's NIS
    if (!$is_operator && $nis_user) {
        $where_conditions[] = "tb_sertif.NIS = '$nis_user'";
    }

    // Add filters
    if (!empty($filter_nis)) {
        $nis_filter = mysqli_real_escape_string($koneksi, $filter_nis);
        $where_conditions[] = "tb_sertif.NIS LIKE '%$nis_filter%'";
    }

    if (!empty($filter_kategori)) {
        $kategori_filter = mysqli_real_escape_string($koneksi, $filter_kategori);
        $where_conditions[] = "tb_kategori.kategori = '$kategori_filter'";
    }

    if (!empty($filter_sub_kategori)) {
        $sub_kategori_filter = mysqli_real_escape_string($koneksi, $filter_sub_kategori);
        $where_conditions[] = "tb_kategori.id_kategori = '$sub_kategori_filter'";
    }

    if (!empty($filter_status)) {
        $status_filter = mysqli_real_escape_string($koneksi, $filter_status);
        $where_conditions[] = "tb_sertif.status = '$status_filter'";
    }

    //fungsi 2 filter
    if (!empty($where_conditions)) {
        $query .= " WHERE " . implode(" AND ", $where_conditions);
    }

    $query .= " ORDER BY tb_kategori.kategori, tb_kategori.sub_kategori, tb_kegiatan.jenis_kegiatan";

    $data_sertif = mysqli_query($koneksi, $query);

    if (!$data_sertif) {
        die("Query gagal: " . mysqli_error($koneksi));
    }
    
    $total_sertifikat = mysqli_num_rows($data_sertif);
    ?>
    
    <!-- Filter summary -->
    <div class="mb-4">
        <p class="text-gray-600">
            Total: <span class="font-semibold"><?php echo $total_sertifikat; ?></span> sertifikat
            <?php if(!empty($filter_kategori)): ?>
                <span class="mx-1">|</span> Kategori: <span class="font-semibold"><?php echo htmlspecialchars($filter_kategori); ?></span>
            <?php endif; ?>
            <?php if(!empty($filter_sub_kategori)): 
                $sub_kat_id = mysqli_real_escape_string($koneksi, $filter_sub_kategori);
                $query_sub_name = mysqli_query($koneksi, "SELECT sub_kategori FROM tb_kategori WHERE id_kategori = '$sub_kat_id'");
                $sub_kat_name = mysqli_fetch_assoc($query_sub_name)['sub_kategori'] ?? 'N/A';
            ?>
                <span class="mx-1">|</span> Sub Kategori: <span class="font-semibold"><?php echo htmlspecialchars($sub_kat_name); ?></span>
            <?php endif; ?>
            <?php if(!empty($filter_nis)): ?>
                <span class="mx-1">|</span> NIS: <span class="font-semibold"><?php echo htmlspecialchars($filter_nis); ?></span>
            <?php endif; ?>
            <?php if(!empty($filter_status)): ?>
                <span class="mx-1">|</span> Status: <span class="font-semibold"><?php echo htmlspecialchars($filter_status); ?></span>
            <?php endif; ?>
        </p>
    </div>
    
    <?php
    if (mysqli_num_rows($data_sertif) > 0) {
    ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php
        $no = 1;
        $last_kategori = null;
        
        while ($data = mysqli_fetch_assoc($data_sertif)) {
            $sertif_url = !empty($data['sertif']) ? 'uploads/' . str_replace(' ', '%20', $data['sertif']) : null;
            
            // Add category header if it's a new category
            $current_kategori = $data['kategori'] . ' - ' . $data['sub_kategori'];
            if ($current_kategori != $last_kategori) {
                // Close the previous grid if not the first category
                if ($last_kategori !== null) {
                    echo '</div><div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-4">';
                }
                    
                // Display category header
                ?>
                <div class="col-span-1 md:col-span-2 lg:col-span-3 bg-gray-200 p-3 rounded-lg shadow-sm mb-2">
                    <h3 class="font-medium text-gray-800">
                        <span class="text-gray-500"><?php echo htmlspecialchars($data['kategori']); ?> &raquo;</span> 
                        <?php echo htmlspecialchars($data['sub_kategori']); ?>
                    </h3>
                </div>
                <?php
                $last_kategori = $current_kategori;
            }
            
            // Determine status color
            switch ($data['status']) {
                case 'Valid':
                    $status_color = 'bg-green-100 text-green-800';
                    break;
                case 'Pending':
                    $status_color = 'bg-yellow-100 text-yellow-800';
                    break;
                case 'Tidak Valid':
                    $status_color = 'bg-red-100 text-red-800';
                    break;
                default:
                    $status_color = 'bg-gray-100 text-gray-800';
            }
            ?>
            <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200 hover:shadow-lg transition-shadow duration-200">
                <div class="p-4">
                    <div class="flex justify-between items-start mb-3">
                        <h3 class="font-semibold text-gray-800"><?php echo htmlspecialchars($data['jenis_kegiatan']); ?></h3>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full <?php echo $status_color; ?>">
                            <?php echo htmlspecialchars($data['status']); ?>
                        </span>
                    </div>
                    
                    <div class="text-sm text-gray-600 mb-2">
                        <span class="font-medium">NIS:</span> <?php echo htmlspecialchars($data['NIS']); ?>
                    </div>
                    
                    <div class="text-sm text-gray-600 mb-2">
                        <span class="font-medium">Nama:</span> <?php echo htmlspecialchars($data['nama_siswa']); ?>
                    </div>
                    
                    <div class="text-sm text-gray-600 mb-3 min-h-[20px]">
                        <?php if (!empty($data['catatan'])) { ?>
                            <span class="font-medium">Catatan:</span> <?php echo htmlspecialchars($data['catatan']); ?>
                        <?php } ?>
                    </div>
                    
                    <div class="flex space-x-2 mt-4">
                        <?php if ($sertif_url) { ?>
                            <?php if ($is_operator) { ?>
                                <a href="halaman_utama.php?page=valid&id_sertif=<?php echo $data['id_sertif']; ?>" 
                                class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-1 px-3 rounded-md text-sm flex items-center transition duration-150">
                                    Lihat
                                </a>
                            <?php } else { ?>
                                <a href="<?php echo $sertif_url; ?>" target="_blank" 
                                class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-1 px-3 rounded-md text-sm flex items-center transition duration-150">
                                    Lihat
                                </a>
                            <?php } ?>
                        <?php } else { ?>
                            <span class="text-red-500 text-sm">Tidak Ada Sertifikat</span>
                        <?php } ?>

                        <?php 
                            $can_edit = !$is_operator && $data['NIS'] == $nis_user && $data['status'] != 'Valid';
                            if ($can_edit) { 
                            ?>
                            <a href="halaman_utama.php?page=ubah_sertifikat&id_sertif=<?php echo $data['id_sertif']; ?>"
                            class="bg-yellow-500 hover:bg-yellow-600 text-white font-medium py-1 px-3 rounded-md text-sm flex items-center transition duration-150">
                                Edit
                            </a>
                        <?php } else if (!$is_operator && $data['status'] == 'Valid') { ?>
                            <button disabled
                                class="bg-gray-400 cursor-not-allowed text-white font-medium py-1 px-3 rounded-md text-sm flex items-center transition duration-150">
                                Edit
                            </button>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php
        }
        ?>
        </div>
    <?php
    } else {
    ?>
        <div class="bg-white rounded-lg shadow-md p-8 text-center">
            <div class="flex flex-col items-center">
                <p class="text-gray-500">Belum ada data sertifikat</p>
            </div>
        </div>
    <?php
    }
    ?>
</div>

<!-- Modal Filter Laporan -->
<div id="modalLaporan" class="fixed inset-0 z-50 hidden overflow-auto bg-black bg-opacity-50 flex justify-center items-center p-4">
    <div class="bg-white rounded-lg shadow-lg max-w-md w-full">
        <div class="p-4 border-b border-gray-200 flex justify-between items-center">
            <h5 class="text-lg font-semibold">Cetak Laporan</h5>
            <button type="button" class="text-gray-400 hover:text-gray-600" id="closeModal">
            </button>
        </div>
        <div class="p-4">
            <form action="../cetak/laporan/laporan.php" method="GET">
                <div class="mb-4">
                    <label for="angkatan" class="block text-sm font-medium text-gray-700 mb-1">Pilih Angkatan</label>
                    <select id="angkatan" name="angkatan" class="w-full p-2 border border-gray-300 rounded-md">
                        <option value="Semua Angkatan">Semua Angkatan</option>
                        <?php while ($row = mysqli_fetch_assoc($data_angkatan)) { ?>
                            <option value="<?= $row['angkatan'] ?>"><?= $row['angkatan'] ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="nis" class="block text-sm font-medium text-gray-700 mb-1">Pilih Siswa</label>
                    <select id="nis" name="nis" class="w-full p-2 border border-gray-300 rounded-md">
                        <option value="Semua Siswa">Semua Siswa</option>
                        <?php while ($row = mysqli_fetch_assoc($data_siswa)) { ?>
                            <option value="<?= $row['NIS'] ?>" data-angkatan="<?= $row['angkatan'] ?>">
                                <?= $row['nama_siswa'] ?> (<?= $row['NIS'] ?>)
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status Sertifikat</label>
                    <select id="status" name="status" class="w-full p-2 border border-gray-300 rounded-md">
                        <option value="Semua Status">Semua Status</option>
                        <option value="Valid">Valid</option>
                        <option value="Pending">Pending</option>
                        <option value="Tidak Valid">Tidak Valid</option>
                    </select>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-md">Cetak</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Modal functionality
    const modal = document.getElementById('modalLaporan');
    const openModalBtn = document.getElementById('openModalButton');
    const closeModalBtn = document.getElementById('closeModal');
    
    if (openModalBtn) {
        openModalBtn.addEventListener('click', function() {
            modal.classList.remove('hidden');
        });
    }
    
    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', function() {
            modal.classList.add('hidden');
        });
    }
    
    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            modal.classList.add('hidden');
        }
    });
    
    // Filter students by selected class year
    const angkatanSelect = document.getElementById('angkatan');
    const nisSelect = document.getElementById('nis');
    
    if (angkatanSelect && nisSelect) {
        angkatanSelect.addEventListener('change', function() {
            let angkatan = this.value;
            let siswaOptions = nisSelect.querySelectorAll('option');

            // Show all students or only students from selected class year
            for (let i = 0; i < siswaOptions.length; i++) {
                let option = siswaOptions[i];
                let optionAngkatan = option.getAttribute('data-angkatan');

                if (angkatan === 'Semua Angkatan' || optionAngkatan === angkatan) {
                    option.style.display = '';
                } else {
                    option.style.display = 'none';
                }
            }
            
            // Reset student selection to "All Students"
            nisSelect.value = 'Semua Siswa';
        });
    }
});
</script>