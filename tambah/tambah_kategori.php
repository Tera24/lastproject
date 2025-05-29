<?php
// Process form submission
if(isset($_POST['submit'])) {
    $kategori = mysqli_real_escape_string($koneksi, $_POST['kategori']);
    $sub_kategori = mysqli_real_escape_string($koneksi, $_POST['sub_kategori']);
    
    $query = mysqli_query($koneksi, "INSERT INTO tb_kategori (kategori, sub_kategori) VALUES ('$kategori', '$sub_kategori')");
    
    if(!$query) {
        echo "<script>alert('Gagal Menambahkan Kategori');window.location.href='halaman_utama.php?page=kategori_kegiatan'</script>";
    } else {
        echo "<script>alert('Kategori Berhasil Ditambahkan');window.location.href='halaman_utama.php?page=kategori_kegiatan'</script>";
    }
}
?>

<div class="container mx-auto px-4 py-6">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md p-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Tambah Kategori</h2>
        
        <form method="POST" action="">
            <div class="mb-6">
                <label for="kategori" class="block text-gray-700 font-medium mb-2">Jenis Kategori:</label>
                <select name="kategori" id="kategori" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Pilih Jenis Kategori --</option>
                    <option value="Wajib">Wajib</option>
                    <option value="Optional">Optional</option>
                </select>
            </div>
            
            <div class="mb-6">
                <label for="sub_kategori" class="block text-gray-700 font-medium mb-2">Nama Kategori:</label>
                <input type="text" name="sub_kategori" id="sub_kategori" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div class="flex items-center justify-end space-x-4">
                <a href="halaman_utama.php?page=kategori_kegiatan" 
                   class="px-6 py-2 bg-gray-500 text-white font-medium rounded-lg hover:bg-gray-600 transition-colors">
                    Kembali
                </a>
                <button type="submit" name="submit" 
                        class="px-6 py-2 bg-blue-500 text-white font-medium rounded-lg hover:bg-blue-600 transition-colors">
                    Simpan Kategori
                </button>
            </div>
        </form>
    </div>
</div>