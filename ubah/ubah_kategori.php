<?php
// Make sure we have an ID to edit
if(!isset($_GET['id_kategori'])) {
    echo "<script>alert('ID Kategori tidak ditemukan');window.location.href='halaman_utama.php?page=kategori_kegiatan'</script>";
    exit;
}

$id_kategori = $_GET['id_kategori'];

// Fetch current category data
$query = mysqli_query($koneksi, "SELECT * FROM tb_kategori WHERE id_kategori='$id_kategori'");
if(mysqli_num_rows($query) == 0) {
    echo "<script>alert('Kategori tidak ditemukan');window.location.href='halaman_utama.php?page=kategori_kegiatan'</script>";
    exit;
}

$kategori = mysqli_fetch_assoc($query);

// Process form submission
if(isset($_POST['submit'])) {
    $kategori_type = mysqli_real_escape_string($koneksi, $_POST['kategori']);
    $sub_kategori = mysqli_real_escape_string($koneksi, $_POST['sub_kategori']);
    
    $update_query = mysqli_query($koneksi, "UPDATE tb_kategori SET kategori='$kategori_type', sub_kategori='$sub_kategori' WHERE id_kategori='$id_kategori'");
    
    if(!$update_query) {
        echo "<script>alert('Gagal Mengubah Kategori');window.location.href='halaman_utama.php?page=kategori_kegiatan'</script>";
    } else {
        echo "<script>alert('Kategori Berhasil Diubah');window.location.href='halaman_utama.php?page=kategori_kegiatan'</script>";
    }
}
?>

<div class="w-full px-4">
    <div class="flex flex-col md:flex-row">
        <div class="md:w-1/12"></div>
        <div class="w-full md:w-9/12 mt-10">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-2xl font-bold mb-6">Ubah Kategori</h2>
                
                <form method="POST" action="">
                    <div class="mb-4">
                        <label for="kategori" class="block text-gray-700 font-bold mb-2">Jenis Kategori:</label>
                        <select name="kategori" id="kategori" required
                               class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500">
                            <option value="Wajib" <?= ($kategori['kategori'] == 'Wajib') ? 'selected' : '' ?>>Wajib</option>
                            <option value="Optional" <?= ($kategori['kategori'] == 'Optional') ? 'selected' : '' ?>>Optional</option>
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label for="sub_kategori" class="block text-gray-700 font-bold mb-2">Nama Kategori:</label>
                        <input type="text" name="sub_kategori" id="sub_kategori" required
                               value="<?= htmlspecialchars($kategori['sub_kategori']) ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500">
                    </div>
                    
                    <div class="flex items-center justify-between mt-6">
                        <a href="halaman_utama.php?page=kategori_kegiatan" 
                           class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                            Kembali
                        </a>
                        <button type="submit" name="submit" 
                                class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>