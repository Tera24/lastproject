<?php
if (isset($_POST['tombol_tambah'])) {
    $jurusan = $_POST['jurusan'];
    $idjurusan = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT id_jurusan FROM tb_jurusan WHERE jurusan = '$jurusan'"));

    if ($idjurusan == NULL) {
        echo "<script>alert('Data jurusan tidak ada di database');window.location.href='halaman_utama.php?page=tambah_siswa';</script>";
    } else {
        $nis = $_POST['NIS'];
        $no_absen = $_POST['no_absen'];
        $nama_siswa = $_POST['nama_siswa'];
        $no_telp = $_POST['no_telp'];
        $email = $_POST['email'];
        $id_jurusan = $idjurusan['id_jurusan'];
        $kelas = $_POST['kelas'];
        $angkatan = $_POST['angkatan'];
        
        // Generate password otomatis: "siswa" + NIS
        $raw_password = "siswa" . $nis;
        $password = password_hash($raw_password, PASSWORD_DEFAULT);

        // Cek dulu apakah NIS sudah ada di database
        $check_nis = mysqli_query($koneksi, "SELECT NIS FROM tb_siswa WHERE NIS = '$nis'");
        
        if(mysqli_num_rows($check_nis) > 0) {
            echo "<script>alert('NIS sudah terdaftar. Silakan gunakan NIS lain.');window.location.href='halaman_utama.php?page=tambah_siswa';</script>";
            exit;
        }

        // Tambahkan data ke tb_siswa
        $hasil = mysqli_query($koneksi, "INSERT INTO tb_siswa (NIS, no_absen, nama_siswa, no_telp, email, id_jurusan, kelas, angkatan) 
                VALUES ('$nis', '$no_absen', '$nama_siswa', '$no_telp', '$email', '$id_jurusan', '$kelas', '$angkatan')");

        if (!$hasil) {
            echo "<script>alert('Gagal memasukkan data siswa: " . mysqli_error($koneksi) . "');window.location.href='halaman_utama.php?page=tambah_siswa';</script>";
        } else {
            // Tambahkan data ke tb_pengguna (untuk siswa, username=NULL)
            $hasil_pengguna = mysqli_query($koneksi, "INSERT INTO tb_pengguna (username, NIS, password) 
                    VALUES (NULL, '$nis', '$password')");
            
            if (!$hasil_pengguna) {
                echo "<script>alert('Data siswa berhasil ditambahkan, tetapi gagal menambahkan data pengguna: " . mysqli_error($koneksi) . "');window.location.href='halaman_utama.php?page=siswa';</script>";
            } else {
                echo "<script>alert('Berhasil menambahkan data siswa dan pengguna. Password: " . $raw_password . "');window.location.href='halaman_utama.php?page=siswa';</script>";
            }
        }
    }
}
?>

<form action="" method="post">
    <div class="container mx-auto px-4 py-6">
        <div class="flex flex-wrap -mx-4">      
            <div class="w-full md:w-1/2 px-4 mb-6">
                <div class="mb-6">
                    <input type="number" class="w-full h-12 px-4 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" id="nis" placeholder="NIS" name="NIS" required>
                </div>
                <div class="mb-6 ">
                    <input type="number" class="w-full h-12 px-4 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" id="no_absen" placeholder="No Absen" name="no_absen" required>
                </div>
                <div class="mb-6 ">
                    <input type="text" class="w-full h-12 px-4 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" id="nama_siswa" placeholder="Nama Siswa" name="nama_siswa" required>
                </div>
                <div class="mb-6 ">
                    <input type="text" class="w-full h-12 px-4 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" id="no_telp" placeholder="No Telp" name="no_telp" required>
                </div>
                <div class="mb-6 ">
                    <input type="email" class="w-full h-12 px-4 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" id="email" placeholder="Email" name="email" required>
                </div>
            </div>
            <div class="w-full md:w-1/2 px-4 mb-6">
                <div class="mb-6 ">
                    <div class="">
                        <select class="w-full h-12 pl-4 pr-8 border border-gray-300 rounded-md text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 appearance-none flex items-center bg-white" id="jurusan" name="jurusan" required>
                            <option value="" disabled selected class="text-gray-500">Pilih Jurusan</option>
                            <?php
                            // Ambil data jurusan dari database
                            $list = mysqli_query($koneksi, "SELECT jurusan FROM tb_jurusan");
                            while ($data = mysqli_fetch_assoc($list)) {
                                echo "<option value='" . $data['jurusan'] . "'>" . $data['jurusan'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="mb-6 ">
                    <input type="text" class="w-full h-12 px-4 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" id="kelas" placeholder="Kelas" name="kelas" required>
                </div>
                <div class="mb-6 ">
                    <input type="number" class="w-full h-12 px-4 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" id="angkatan" placeholder="Angkatan" name="angkatan" required>
                </div>
                <div class="mb-6 p-4 bg-blue-100 text-blue-800 rounded-md">
                    <p>Password akan dibuat otomatis dengan format: "siswa" + NIS</p>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2" name="tombol_tambah">Kirim</button>
                </div>
            </div>
        </div>
    </div>
</form>