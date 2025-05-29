<?php
include "../koneksi.php";

if (isset($_GET['id'])) {
    $id_jurusan = mysqli_real_escape_string($koneksi, $_GET['id']);
    $hasil_jurusan = mysqli_query($koneksi, "DELETE FROM tb_jurusan WHERE id_jurusan='$id_jurusan'");

    if (!$hasil_jurusan) {
        echo "<script>alert('Gagal menghapus data');window.location.href='halaman_utama.php?page=jurusan';</script>";
    } else {
        echo "<script>alert('Berhasil menghapus data');window.location.href='halaman_utama.php?page=jurusan';</script>";
    }
}

// Get total count of departments
$count_query = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tb_jurusan");
$count_data = mysqli_fetch_assoc($count_query);
$total_jurusan = $count_data['total'];
?>

<div class="container mx-auto px-4 py-6 max-w-4xl">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">Daftar Jurusan</h2>
        </div>
        <a href="halaman_utama.php?page=tambah_jurusan" 
           class="bg-green-500 hover:bg-green-600 text-white font-medium py-2 px-4 rounded-lg shadow-sm transition duration-150 flex items-center">
           <i data-lucide="plus-circle" class="h-5 w-5 mr-1"></i>
           Tambah Jurusan
        </a>
    </div>

    <!-- Stats Card -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-100 text-blue-500 mr-4">
                <i data-lucide="book-dashed" class="h-6 w-6"></i>
            </div>
            <div>
                <p class="text-gray-500 text-sm">Jumlah Jurusan</p>
                <p class="text-2xl font-bold text-gray-800"><?= $total_jurusan ?></p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php
        $no = 1;
        $data_jurusan = mysqli_query($koneksi, "SELECT * FROM tb_jurusan");
        while ($data = mysqli_fetch_assoc($data_jurusan)) {
        ?>
        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-200">
            <div class="p-4 border-b border-gray-100">
                <div class="flex justify-between items-center">
                    <span class="bg-gray-100 text-gray-600 text-xs font-medium px-2 py-1 rounded-full"><?= $no++; ?></span>
                    <span class="text-gray-500 text-sm">Jurusan</span>
                </div>
                <h3 class="font-bold text-lg mt-2 text-gray-800"><?= $data['jurusan']; ?></h3>
            </div>
            <div class="px-4 py-3 bg-gray-50">
                <div class="flex space-x-2">
                    <a href="halaman_utama.php?page=ubah_jurusan&id=<?= $data['id_jurusan'] ?>"
                       class="bg-yellow-500 hover:bg-yellow-600 text-white font-medium py-1 px-3 rounded-md text-sm flex items-center transition duration-150 flex-1 justify-center">
                       <i data-lucide="pencil" class="h-4 w-4 mr-1"></i>
                       Update
                    </a>
                    <?php
                    $id_cek = $data['id_jurusan'];
                    $cek_data = mysqli_query($koneksi, "SELECT id_jurusan FROM tb_siswa WHERE id_jurusan='$id_cek'");

                    if (mysqli_num_rows($cek_data) == 0) {
                    ?>
                        <a onclick="return confirm('Yakin mau hapus?');" 
                           href="halaman_utama.php?page=jurusan&id=<?= $data['id_jurusan'] ?>" 
                           class="bg-red-500 hover:bg-red-600 text-white font-medium py-1 px-3 rounded-md text-sm flex items-center transition duration-150 flex-1 justify-center">
                           <i data-lucide="trash-2" class="h-4 w-4 mr-1"></i>
                           Delete
                        </a>
                    <?php } else { ?>
                        <div class="flex-1"></div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
    
    <?php if (mysqli_num_rows($data_jurusan) == 0) { ?>
    <div class="bg-gray-50 rounded-lg p-8 text-center">
        <i data-lucide="file-text" class="h-12 w-12 mx-auto text-gray-400"></i>
        <h3 class="mt-2 text-gray-700 font-medium">Belum ada data jurusan</h3>
        <p class="text-gray-500 mt-1 text-sm">Silahkan tambahkan jurusan baru</p>
    </div>
    <?php } ?>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();   
  });
</script>