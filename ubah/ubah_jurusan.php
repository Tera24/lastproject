<?php
$id = $_GET['id'];
$data_update = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM tb_jurusan WHERE id_jurusan='$id'"));

if(isset($_POST['tombol_update'])){
    $jurusan = $_POST['jurusan'];

    $hasil = mysqli_query($koneksi, "UPDATE tb_jurusan SET jurusan = '$jurusan' WHERE id_jurusan='$id'");
    
    if(!$hasil){
        echo "<script>alert('Gagal Update Jurusan');window.location.href='halaman_utama.php?page=ubah_jurusan'</script>";
    }else{
        echo "<script>alert('Jurusan Berhasil Di Update');window.location.href='halaman_utama.php?page=jurusan'</script>";
    }
}
?>

<form action="" method="post">
    <div class="container mx-auto px-4">
        <div class="flex justify-center">
            <div class="w-full md:w-1/2 mt-10">
                <div class="mb-6 relative">
                    <input type="text" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                           id="jurusan" 
                           placeholder="Nama Jurusan"
                           name="jurusan" 
                           value="<?=$data_update['jurusan']?>" 
                           required>
                    <label for="jurusan" class="absolute left-0 -top-6 text-sm text-gray-600">Nama Jurusan</label>
                </div>

                <div class="flex justify-end">
                    <input type="submit" 
                           name="tombol_update" 
                           class="px-6 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50" 
                           value="Kirim">
                </div>
            </div>
        </div>
    </div>
</form>