<?php
if (isset($_POST['tombol_tambah'])) {
    $kategori = $_POST['kategori'];
    $sub_kategori = $_POST['sub_kategori'];
    $kegiatan = $_POST['kegiatan'];
    
    $cek_kegiatan = mysqli_query($koneksi, "SELECT jenis_kegiatan FROM tb_kegiatan WHERE jenis_kegiatan = '$kegiatan'");
    
    if (mysqli_num_rows($cek_kegiatan) > 0) {
        echo "<script>alert('Data Sudah Ada Di Database, Silahkan Masukkan Jenis Kegiatan Baru');window.location.href='halaman_utama.php?page=tambah_kegiatan&kategori=" . $kategori . "&sub_kategori=" . $sub_kategori . "'</script>";
    } else {
        $kategori = htmlspecialchars($_POST['kategori']);
        $sub_kategori = htmlspecialchars($_POST['sub_kategori']);
        
        $query = "SELECT id_kategori FROM tb_kategori WHERE sub_kategori = '$sub_kategori'";
        $result = mysqli_query($koneksi, $query);
        $id_kategori = mysqli_fetch_assoc($result)['id_kategori'];
        
        $point = htmlspecialchars($_POST['point']);
        
        $hasil = mysqli_query($koneksi, "INSERT INTO tb_kegiatan VALUES (NULL, '$kegiatan', '$point', '$id_kategori')");
        
        if (!$hasil) {
            echo "<script>alert('Gagal Memasukan Data');window.location.href='halaman_utama.php?page=tambah_kegiatan'</script>";
        } else {
            echo "<script>alert('Data Berhasil Ditambahkan');window.location.href='halaman_utama.php?page=kategori_kegiatan'</script>";
        }
    }
}
?>

<div class="w-full px-4">
    <div class="flex flex-col md:flex-row">
        <div class="md:w-1/4"></div>
        <div class="w-full md:w-1/2 mt-10">
            <select name="kategori" class="w-full px-4 py-3 mb-4 text-lg border rounded focus:outline-none focus:ring-2 focus:ring-blue-500" onchange="pilihKategori(this.value)">
                <option selected>Pilih kategori</option>
                <?php
                $list_kategori = mysqli_query($koneksi, "SELECT DISTINCT kategori FROM tb_kategori");
                while ($data_kategori = mysqli_fetch_assoc($list_kategori)) {
                ?>
                <option value="<?= $data_kategori['kategori'] ?>"
                    <?php if (@$_GET['kategori'] == $data_kategori['kategori']) { echo "selected"; } ?>>
                    <?= $data_kategori['kategori'] ?>
                </option>
                <?php } ?>
            </select>
            
            <script>
                function pilihKategori(value) {
                    window.location.href = 'halaman_utama.php?page=tambah_kegiatan&kategori=' + value;
                }
            </script>
            
            <?php if (@$_GET['kategori']) { ?>
            <select name="sub_kategori" class="w-full px-4 py-3 mb-4 text-lg border rounded focus:outline-none focus:ring-2 focus:ring-blue-500" onchange="pilihSubKategori(this.value)">
                <option selected>Pilih Sub kategori</option>
                <?php
                $kategori = $_GET['kategori'];
                $list_sub_kategori = mysqli_query($koneksi, "SELECT DISTINCT sub_kategori FROM tb_kategori WHERE kategori = '$kategori'");
                while ($sub_kategori = mysqli_fetch_assoc($list_sub_kategori)) {
                ?>
                <option value="<?= $sub_kategori['sub_kategori'] ?>"
                    <?php if (@$_GET['sub_kategori'] == $sub_kategori['sub_kategori']) { echo "selected"; } ?>>
                    <?= $sub_kategori['sub_kategori'] ?>
                </option>
                <?php } ?>
            </select>
            
            <script>
                function pilihSubKategori(value) {
                    const urlParams = new URLSearchParams(window.location.search);
                    const kategori = urlParams.get('kategori');
                    window.location.href = `halaman_utama.php?page=tambah_kegiatan&kategori=${kategori}&sub_kategori=${value}`;
                }
            </script>
            <?php } ?>
            
            <?php if (@$_GET['sub_kategori']) { ?>
            <form action="" method="post">
                <input type="hidden" name="kategori" value="<?= $_GET['kategori'] ?>">
                <input type="hidden" name="sub_kategori" value="<?= $_GET['sub_kategori'] ?>">
                
                <datalist id="kegiatan">
                    <?php
                    $sub_kategori = $_GET['sub_kategori'];
                    $list_kegiatan = mysqli_query($koneksi, "SELECT jenis_kegiatan FROM tb_kegiatan INNER JOIN tb_kategori USING(id_kategori) WHERE sub_kategori='$sub_kategori'");
                    while ($data_kegiatan = mysqli_fetch_assoc($list_kegiatan)) {
                    ?>
                    <option value="<?= $data_kegiatan['jenis_kegiatan'] ?>"></option>
                    <?php } ?>
                </datalist>
                
                <div class="mb-4 relative">
                    <input type="text" list="kegiatan" class="w-full px-4 py-3 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500 pt-6" name="kegiatan" placeholder=" " required>
                    <label class="absolute text-gray-500 top-1 left-4 text-xs">Nama Kegiatan</label>
                </div>
                
                <div class="mb-4 relative">
                    <input type="number" class="w-full px-4 py-3 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500 pt-6" name="point" placeholder=" " required>
                    <label class="absolute text-gray-500 top-1 left-4 text-xs">Angka Kredit / Point</label>
                </div>
                
                <div class="flex justify-end">
                    <input type="submit" name="tombol_tambah" class="px-6 py-2 bg-green-500 hover:bg-green-600 text-white rounded cursor-pointer" value="Simpan">
                </div>
            </form>
            <?php } ?>
        </div>
    </div>
</div>