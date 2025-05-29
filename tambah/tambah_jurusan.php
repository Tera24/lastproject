<?php
// Proses tambah data jurusan
if (isset($_POST['tambah_jurusan'])) {
    $jurusan_baru = htmlspecialchars($_POST['jurusan_baru']);
    
    // Cek apakah jurusan sudah ada
    $cek_jurusan = mysqli_query($koneksi, "SELECT * FROM tb_jurusan WHERE jurusan='$jurusan_baru'");
    
    if (mysqli_num_rows($cek_jurusan) > 0) {
        echo "<script>alert('Jurusan sudah ada!');</script>";
    } else {
        // Buat ID jurusan otomatis (format: j1, j2, dst.)
        $cek_id = mysqli_query($koneksi, "SELECT id_jurusan FROM tb_jurusan ORDER BY id_jurusan DESC LIMIT 1");
        $data_id = mysqli_fetch_assoc($cek_id);
                
        if ($data_id) {
            $angka_id = (int) substr($data_id['id_jurusan'], 1) + 1;
        } else {
            $angka_id = 1;
        }
        $id_jurusan_baru = 'j' . $angka_id;
        
        // Insert data ke tabel jurusan
        $hasil = mysqli_query($koneksi, "INSERT INTO tb_jurusan (id_jurusan, jurusan) VALUES ('$id_jurusan_baru', '$jurusan_baru')");
        
        if (!$hasil) {
            echo "<script>alert('Gagal menambahkan jurusan!');</script>";
        } else {
            echo "<script>alert('Jurusan berhasil ditambahkan!'); window.location='halaman_utama.php?page=jurusan';</script>";
        }
    }
}
?>

<body class="bg-gray-100 h-screen">
    <div class="container mx-auto px-4 py-4 h-full">
        <div class="bg-white shadow-lg rounded-lg overflow-hidden h-full max-w-full flex flex-col">
            <div class="p-4 bg-blue-600 text-white">
                <h1 class="text-xl md:text-2xl font-bold">Data Jurusan</h1>
            </div>
            
            <!-- Tabel Data Jurusan (Fixed Height) -->
            <div class="p-4 flex-grow">
                <div class="table-container">
                    <table class="min-w-full bg-white border-collapse">
                        <thead class="sticky top-0 bg-gray-200">
                            <tr class="text-gray-700">
                                <th class="py-2 px-3 text-left text-xs md:text-sm font-medium">No</th>
                                <th class="py-2 px-3 text-left text-xs md:text-sm font-medium">ID Jurusan</th>
                                <th class="py-2 px-3 text-left text-xs md:text-sm font-medium">Nama Jurusan</th>
                                <th class="py-2 px-3 text-left text-xs md:text-sm font-medium">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = mysqli_query($koneksi, "SELECT * FROM tb_jurusan ORDER BY jurusan ASC");
                            $no = 1;
                            
                            if (mysqli_num_rows($query) > 0) {
                                while ($data = mysqli_fetch_assoc($query)) {
                            ?>
                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-2 px-3 text-xs md:text-sm"><?= $no++; ?></td>
                                <td class="py-2 px-3 text-xs md:text-sm"><?= $data['id_jurusan']; ?></td>
                                <td class="py-2 px-3 text-xs md:text-sm"><?= $data['jurusan']; ?></td>
                                <td class="py-2 px-3 text-xs md:text-sm">
                                    <div class="flex flex-wrap gap-1">
                                        <a href="halaman_utama.php?page=ubah_jurusan&id=<?= $data['id_jurusan']; ?>" class="bg-yellow-500 hover:bg-yellow-600 text-white py-1 px-2 rounded text-xs md:text-sm text-center">Edit</a>
                                    </div>
                                </td>
                            </tr>
                            <?php
                                }
                            } else {
                            ?>
                            <tr>
                                <td colspan="4" class="py-3 px-4 text-center text-sm">Tidak ada data jurusan</td>
                            </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Form Tambah Jurusan (Fixed at Bottom) -->
            <div class="p-4 bg-gray-50 border-t">
                <h2 class="text-lg font-semibold mb-3">Tambah Jurusan</h2>
                <form action="" method="POST" class="flex flex-col sm:flex-row gap-2 items-end">
                    <div class="flex-grow">
                        <label for="jurusan_baru" class="block text-gray-700 text-sm mb-1">Nama Jurusan</label>
                        <input type="text" name="jurusan_baru" id="jurusan_baru" required class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" placeholder="Masukkan nama jurusan (2 Huruf)">
                    </div>
                    <button type="submit" name="tambah_jurusan" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg text-sm whitespace-nowrap">
                        Tambah Jurusan 
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>