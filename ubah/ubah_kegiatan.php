<?php
$id_kegiatan = $_GET['id_kegiatan'] ?? '';
$data_update = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM tb_kegiatan INNER JOIN tb_kategori ON tb_kegiatan.id_kategori = tb_kategori.id_kategori WHERE id_kegiatan='$id_kegiatan'"));

if (!$id_kegiatan || !$data_update) {
    die("ID kegiatan tidak ditemukan atau data tidak tersedia.");
}

if(isset($_POST['tombol_update'])){
    $kategori = $_POST['kategori'];
    $sub_kategori = $_POST['sub_kategori'];
    $nama_kategori = $_POST['nama_kategori'];
    $angka_kredit = $_POST['angka_kredit'];

    $hasil = mysqli_query($koneksi, "UPDATE tb_kegiatan SET id_kategori = (SELECT id_kategori FROM tb_kategori WHERE kategori='$kategori' AND sub_kategori='$sub_kategori' LIMIT 1), jenis_kegiatan = '$nama_kategori', angka_kredit = '$angka_kredit' WHERE id_kegiatan='$id_kegiatan'");
    
    if(!$hasil){
        echo "<script>alert('Gagal Update Kegiatan');window.location.href='halaman_utama.php?page=ubah_kegiatan&id_kegiatan=$id_kegiatan'</script>";
    }else{
        echo "<script>alert('Kegiatan Berhasil Diupdate');window.location.href='halaman_utama.php?page=kategori_kegiatan'</script>";
    }
}

$kategori_list = mysqli_query($koneksi, "SELECT DISTINCT kategori FROM tb_kategori");
$sub_kategori_list = mysqli_query($koneksi, "SELECT kategori, sub_kategori FROM tb_kategori");
$sub_kategori_data = [];
while ($row = mysqli_fetch_assoc($sub_kategori_list)) {
    $sub_kategori_data[$row['kategori']][] = $row['sub_kategori'];
}
?>

<div class="w-full px-4">
    <div class="flex flex-col md:flex-row">
        <div class="md:w-1/4"></div>
        <div class="w-full md:w-1/2 mt-10">
            <form action="" method="post">
                <div class="mb-4 relative">
                    <select class="w-full px-4 py-3 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500 pt-6" name="kategori" id="kategori" required>
                        <option value="">Pilih Kategori</option>
                        <?php
                        while($kategori = mysqli_fetch_assoc($kategori_list)) {
                            $selected = ($kategori['kategori'] == $data_update['kategori']) ? 'selected' : '';
                            echo "<option value='{$kategori['kategori']}' $selected>{$kategori['kategori']}</option>";
                        }
                        ?>
                    </select>
                    <label for="kategori" class="absolute text-gray-500 top-1 left-4 text-xs">Kategori</label>
                </div>

                <div class="mb-4 relative">
                    <select class="w-full px-4 py-3 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500 pt-6" name="sub_kategori" id="sub_kategori" required>
                        <option value="">Pilih Sub Kategori</option>
                    </select>
                    <label for="sub_kategori" class="absolute text-gray-500 top-1 left-4 text-xs">Sub Kategori</label>
                </div>

                <div class="mb-4 relative">
                    <input type="text" class="w-full px-4 py-3 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500 pt-6" id="floatingNamaKategori" placeholder=" "
                        name="nama_kategori" value="<?= $data_update['jenis_kegiatan'] ?>" required>
                    <label for="floatingNamaKategori" class="absolute text-gray-500 top-1 left-4 text-xs">Nama Kegiatan</label>
                </div>

                <div class="mb-4 relative">
                    <input type="number" class="w-full px-4 py-3 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500 pt-6" id="floatingPoint" placeholder=" "
                        name="angka_kredit" value="<?= $data_update['angka_kredit'] ?? '' ?>" required>
                    <label for="floatingPoint" class="absolute text-gray-500 top-1 left-4 text-xs">Angka Kredit</label>
                </div>

                <div class="flex justify-end">
                    <input type="submit" name="tombol_update" class="px-6 py-2 bg-green-500 hover:bg-green-600 text-white rounded cursor-pointer" value="Simpan">
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let subKategoriData = <?php echo json_encode($sub_kategori_data); ?>;
    let subKategoriSelect = document.getElementById('sub_kategori');
    let kategoriSelect = document.getElementById('kategori');
    let selectedSubKategori = "<?= $data_update['sub_kategori'] ?>";
    
    function updateSubKategori() {
        let selectedKategori = kategoriSelect.value;
        subKategoriSelect.innerHTML = '<option value="">Pilih Sub Kategori</option>';
        if (selectedKategori in subKategoriData) {
            subKategoriData[selectedKategori].forEach(sub => {
                let option = document.createElement('option');
                option.value = sub;
                option.textContent = sub;
                if (sub === selectedSubKategori) {
                    option.selected = true;
                }
                subKategoriSelect.appendChild(option);
            });
        }
    }

    kategoriSelect.addEventListener('change', updateSubKategori);
    window.onload = updateSubKategori;
</script>