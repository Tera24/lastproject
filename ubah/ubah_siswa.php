<?php
$NIS = $_GET['NIS'];

$data_siswa = mysqli_fetch_assoc(mysqli_query($koneksi, query: "SELECT * FROM tb_siswa WHERE NIS='$NIS'"));
$data_jurusan = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT jurusan FROM tb_jurusan WHERE id_jurusan = '{$data_siswa['id_jurusan']}'"));

// Get current password (hashed) for display purposes
$data_password = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT password FROM tb_pengguna WHERE NIS='$NIS'"));

if (isset($_POST['tombol_update'])) {
    $nama_jurusan = $_POST['jurusan'];
    
    $id_jurusan = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT id_jurusan FROM tb_jurusan WHERE jurusan = '$nama_jurusan'"));

    if ($id_jurusan['id_jurusan'] == NULL) {
        echo "<script>alert('Jurusan tidak ada di database'); window.location.href = 'halaman_utama.php?page=ubah_siswa&NIS=$NIS';</script>";
    } else {
        $nama_siswa = $_POST['nama_siswa'];
        $no_absen = $_POST['no_absen'];
        $no_telp = $_POST['no_telp'];
        $email = $_POST['email'];
        $kelas = $_POST['kelas'];
        $angkatan = $_POST['angkatan'];
        $password = $_POST['password'];
        
        mysqli_begin_transaction($koneksi);
        
        try {
            $hasil_siswa = mysqli_query($koneksi, "UPDATE tb_siswa SET 
                no_absen = '$no_absen', 
                nama_siswa = '$nama_siswa', 
                no_telp = '$no_telp', 
                email = '$email', 
                id_jurusan = '{$id_jurusan['id_jurusan']}', 
                kelas = '$kelas', 
                angkatan = '$angkatan' 
                WHERE NIS = '$NIS'");
                
            if (!$hasil_siswa) {
                throw new Exception("Gagal update data siswa");
            }
            
            // Only update password if a new one was provided
            if (!empty($password)) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $hasil_password = mysqli_query($koneksi, "UPDATE tb_pengguna SET 
                    password = '$hashed_password'
                    WHERE NIS = '$NIS'");
                    
                if (!$hasil_password) {
                    throw new Exception("Gagal update password");
                }
            }
            mysqli_commit($koneksi);
            echo "<script>alert('Berhasil update data siswa'); window.location.href = 'halaman_utama.php?page=siswa';</script>";
            
        } catch (Exception $e) {
            mysqli_rollback($koneksi);
            echo "<script>alert('" . $e->getMessage() . "'); window.location.href = 'halaman_utama.php?page=ubah_siswa&NIS=$NIS';</script>";
        }
    }
}
?>

<div class="mx-auto max-w-5xl px-4 py-4">
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-4 py-3 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
            <div>
                <h2 class="text-lg font-semibold text-gray-800">Update Data Siswa</h2>
                <p class="text-xs text-gray-500">NIS: <?= $data_siswa['NIS'] ?></p>
            </div>
        </div>

        <div class="p-4">
            <form action="" method="post">
                <div class="grid grid-cols-2 gap-x-6 gap-y-3">
                    <div>
                        <div class="mb-2">
                            <label for="NIS" class="block text-xs font-medium text-gray-700">NIS</label>
                            <input type="number" name="NIS" id="NIS" value="<?= $data_siswa['NIS'] ?>" readonly
                                   class="h-8 bg-gray-100 mt-1 block w-full px-2 py-1 border border-gray-300 rounded-md shadow-sm text-sm text-gray-500">
                        </div>

                        <div class="mb-2">
                            <label for="no_absen" class="block text-xs font-medium text-gray-700">No Absen</label>
                            <input type="number" name="no_absen" id="no_absen" value="<?= $data_siswa['no_absen'] ?>" required
                                   class="h-8 mt-1 block w-full px-2 py-1 border border-gray-300 rounded-md shadow-sm text-sm">
                        </div>

                        <div class="mb-2">
                            <label for="nama_siswa" class="block text-xs font-medium text-gray-700">Nama Siswa</label>
                            <input type="text" name="nama_siswa" id="nama_siswa" value="<?= $data_siswa['nama_siswa'] ?>" required
                                   class="h-8 mt-1 block w-full px-2 py-1 border border-gray-300 rounded-md shadow-sm text-sm">
                        </div>

                        <div class="mb-2">
                            <label for="no_telp" class="block text-xs font-medium text-gray-700">No Telp</label>
                            <input type="text" name="no_telp" id="no_telp" value="<?= $data_siswa['no_telp'] ?>" required
                                   class="h-8 mt-1 block w-full px-2 py-1 border border-gray-300 rounded-md shadow-sm text-sm">
                        </div>
                    </div>
                    
                    <div>
                        <div class="mb-2">
                            <label for="email" class="block text-xs font-medium text-gray-700">Email</label>
                            <input type="email" name="email" id="email" value="<?= $data_siswa['email'] ?>" required
                                   class="h-8 mt-1 block w-full px-2 py-1 border border-gray-300 rounded-md shadow-sm text-sm">
                        </div>

                        <div class="mb-2">
                        <div class="">
                        <label for="jurusan" class="block text-xs font-medium text-gray-700">Jurusan</label>
                        <select class="h-8 mt-1 block w-full px-2 py-1 border border-gray-300 rounded-md shadow-sm text-sm" id="jurusan" name="jurusan" required>
                            <option value="" disabled selected class="text-gray-500">Pilih Jurusan</option>
                            <?php
                            $list = mysqli_query($koneksi, "SELECT jurusan FROM tb_jurusan");
                            while ($data = mysqli_fetch_assoc($list)) {
                                echo "<option value='" . $data['jurusan'] . "'>" . $data['jurusan'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                        <div class="mb-2">
                            <label for="kelas" class="block text-xs font-medium text-gray-700">Kelas</label>
                            <input type="text" name="kelas" id="kelas" value="<?= $data_siswa['kelas'] ?>" required
                                   class="h-8 mt-1 block w-full px-2 py-1 border border-gray-300 rounded-md shadow-sm text-sm">
                        </div>

                        <div class="mb-2">
                            <label for="angkatan" class="block text-xs font-medium text-gray-700">Angkatan</label>
                            <input type="number" name="angkatan" id="angkatan" value="<?= $data_siswa['angkatan'] ?>" required
                                   class="h-8 mt-1 block w-full px-2 py-1 border border-gray-300 rounded-md shadow-sm text-sm">
                        </div>
                        <div class="mb-2">
                            <label for="password" class="block text-xs font-medium text-gray-700">New Password</label>
                            <input type="password" name="password" id="password" 
                                   class="h-8 mt-1 block w-full px-2 py-1 border border-gray-300 rounded-md shadow-sm text-sm">
                            <p class="text-xs text-gray-500 mt-1">Biarkan kosong jika tidak ingin mengubah password</p>
                        </div>
                    </div>
                </div>  
                <div class="mt-4 flex justify-end space-x-2">
                    <a href="halaman_utama.php?page=siswa" 
                       class="px-3 py-1.5 bg-gray-500 text-white text-sm rounded hover:bg-gray-600 transition-colors">
                        Batal
                    </a>
                    <button type="submit" 
                            name="tombol_update" 
                            class="px-3 py-1.5 bg-green-600 text-white text-sm rounded hover:bg-green-700 transition-colors shadow-sm">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>