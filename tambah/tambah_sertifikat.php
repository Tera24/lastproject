<?php
include '../koneksi.php';

// Pastikan pengguna sudah login dengan cookie
if (!isset($_COOKIE['NIS'])) {
    echo "<script>alert('Harap login terlebih dahulu');window.location.href='../login.php'</script>";
    exit;
}

// Ambil NIS dari cookie
$NIS = $_COOKIE['NIS'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_kategori = $_POST['id_kategori'];
    $tgl_upload = date('Y-m-d');

    // Tentukan folder penyimpanan
    $target_dir = "uploads/";

    // Pastikan folder ada, jika tidak buat foldernya
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $file_name = basename($_FILES["sertif"]["name"]);
    $target_file = $target_dir . $file_name;
    $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $file_size = $_FILES["sertif"]["size"];
    $file_tmp = $_FILES["sertif"]["tmp_name"];
    $file_error = $_FILES["sertif"]["error"];

    // Generate unique filename
    do {
        $randomString = substr(str_shuffle("abcdefghijklmnopqrstuvwxyz0123456789"), 0, 5);
        $unique_filename = $NIS . '_' . $randomString . ".pdf";
        $target_unique_file = $target_dir . $unique_filename;
    } while (file_exists($target_unique_file));

    // Cek apakah file telah diunggah dengan benar
    if ($file_error !== 0) {
        echo "<script>alert('Terjadi kesalahan saat mengunggah file. Kode error: $file_error');window.location.href='tambah_sertifikat.php'</script>";
        exit;
    }

    // Pastikan hanya file PDF yang diperbolehkan
    if ($file_type != "pdf") {
        echo "<script>alert('Hanya File .pdf yang diperbolehkan');window.location.href='tambah_sertifikat.php'</script>";
        exit;
    }

    // Batasi ukuran file (maksimal 2MB)
    if ($file_size > 5 * 1024 * 1024) {
        echo "<script>alert('Ukuran file terlalu besar! Maksimal 2MB.');window.location.href='tambah_sertifikat.php'</script>";
        exit;
    }

    // Pindahkan file yang diunggah ke folder tujuan dengan nama unik
    if (move_uploaded_file($file_tmp, $target_unique_file)) {
        // Simpan informasi ke database menggunakan nama file unik
        $query = "INSERT INTO tb_sertif (tgl_upload, sertif, status, NIS, id_kegiatan) 
                VALUES ('$tgl_upload', '$unique_filename', 'Pending', '$NIS', '$id_kategori')";
        $hasil = mysqli_query($koneksi, $query);

        if ($hasil) {
            echo "<script>alert('Sertifikat berhasil ditambahkan');window.location.href='halaman_utama.php?page=sertifikat'</script>";
        } else {
            echo "<script>alert('Gagal menambahkan sertifikat ke database');window.location.href='tambah_sertifikat.php'</script>";
        }
    } else {
        echo "<script>alert('Gagal mengunggah file ke folder tujuan');window.location.href='tambah_sertifikat.php'</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Sertifikat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen p-5">
    <div class="max-w-3xl mx-auto">
        <!-- Card container -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <!-- Header section -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 p-5">
                <h2 class="text-2xl font-bold text-white flex items-center">
                    <i class="fas fa-certificate mr-3"></i>
                    Tambah Sertifikat
                </h2>
                <p class="text-blue-100">Unggah sertifikat prestasi dan kegiatan Anda</p>
            </div>
            
            <!-- Form section -->
            <form action="" method="POST" enctype="multipart/form-data" class="p-5 space-y-5" id="upload-form">
                <!-- Two column layout for smaller screens -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- NIS Field -->
                    <div class="space-y-2">
                        <label for="nis" class="text-base font-medium text-gray-700 block">Nomor Induk Siswa</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-id-card text-gray-400"></i>
                            </div>
                            <input type="text" id="nis" class="pl-10 w-full py-2.5 bg-gray-100 border border-gray-300 rounded-lg text-base" 
                                value="<?php echo htmlspecialchars($NIS); ?>" readonly>
                        </div>
                    </div>
                    
                    <!-- Jenis Kegiatan Field -->
                    <div class="space-y-2">
                        <label for="id_kategori" class="text-base font-medium text-gray-700 block">Jenis Kegiatan</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-tags text-gray-400"></i>
                            </div>
                            <select name="id_kategori" id="id_kategori" required
                                class="pl-10 w-full py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent appearance-none text-base">
                                <option value="" disabled selected>-- Pilih Jenis Kegiatan --</option>
                                <?php
                                $query_kategori = mysqli_query($koneksi, "SELECT * FROM tb_kegiatan INNER JOIN tb_kategori USING(id_kategori)");
                                while ($kategori = mysqli_fetch_assoc($query_kategori)) {
                                    echo "<option value='{$kategori['id_kegiatan']}'>{$kategori['jenis_kegiatan']} - {$kategori['kategori']}</option>";
                                }
                                ?>
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <i class="fas fa-chevron-down text-gray-400"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Upload Sertifikat Field with Drag & Drop -->
                <div class="space-y-2">
                    <label class="text-base font-medium text-gray-700 block">Upload Sertifikat</label>
                    
                    <div id="drop-area" class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center transition-colors cursor-pointer relative">
                        <!-- Initial State -->
                        <div id="initial-state" class="flex flex-col items-center justify-center">
                            <i class="fas fa-file-pdf text-5xl text-gray-400 mb-3"></i>
                            <div class="text-gray-600 font-medium">Tarik dan lepas file PDF di sini</div>
                            <div class="text-sm text-gray-500 mt-2">atau</div>
                            <button type="button" id="browse-button" class="mt-2 px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition">
                                <i class="fas fa-folder-open mr-2"></i>Pilih File
                            </button>
                            <div class="text-sm text-gray-500 mt-3">Maksimal 5MB, format PDF</div>
                        </div>
                        
                        <!-- File Selected State (initially hidden) -->
                        <div id="file-selected-state" class="hidden flex-col items-center">
                            <i class="fas fa-file-pdf text-5xl text-blue-500 mb-3"></i>
                            <div id="file-name" class="text-blue-600 font-medium break-all max-w-full"></div>
                            <div id="file-size" class="text-sm text-gray-500 mt-1"></div>
                            <button type="button" id="remove-file" class="mt-3 px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition">
                                <i class="fas fa-trash mr-2"></i>Hapus
                            </button>
                        </div>
                        
                        <!-- Loading Animation (initially hidden) -->
                        <div id="loading-state" class="hidden flex-col items-center">
                            <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-blue-700 mb-3"></div>
                            <div class="text-blue-600 font-medium">Memproses file...</div>
                        </div>
                        
                        <!-- Hidden file input -->
                        <input type="file" name="sertif" id="sertif" class="hidden" accept=".pdf" required>
                    </div>
                </div>
                
                <!-- Confirmation text -->
                <div class="text-sm text-gray-600 bg-blue-50 p-4 rounded-lg border border-blue-100">
                    <p class="flex items-start">
                        <i class="fas fa-info-circle text-blue-500 mr-2 mt-1"></i>
                        <span>Pastikan sertifikat yang diunggah adalah asli dan masih dalam keadaan baik. Sertifikat akan direview oleh admin sebelum diverifikasi.</span>
                    </p>
                </div>
                
                <!-- Submit Button -->
                <div class="flex space-x-4 pt-1">
                    <a href="halaman_utama.php?page=sertifikat" 
                        class="py-2.5 px-5 bg-gray-200 text-gray-700 font-medium rounded-lg shadow hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50 transition">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali
                    </a>
                    <button type="submit" id="submit-button" disabled
                        class="flex-grow py-2.5 bg-gradient-to-r from-blue-600 to-indigo-700 text-white font-medium rounded-lg shadow focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition opacity-50 cursor-not-allowed">
                        <i class="fas fa-upload mr-2"></i>Unggah Sertifikat
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- JavaScript for drag and drop functionality -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dropArea = document.getElementById('drop-area');
            const fileInput = document.getElementById('sertif');
            const browseButton = document.getElementById('browse-button');
            const removeButton = document.getElementById('remove-file');
            const submitButton = document.getElementById('submit-button');
            const initialState = document.getElementById('initial-state');
            const fileSelectedState = document.getElementById('file-selected-state');
            const loadingState = document.getElementById('loading-state');
            const fileNameElement = document.getElementById('file-name');
            const fileSizeElement = document.getElementById('file-size');
            
            // Change border color when dragging over
            ['dragenter', 'dragover'].forEach(eventName => {
                dropArea.addEventListener(eventName, highlight, false);
            });
            
            ['dragleave', 'drop'].forEach(eventName => {
                dropArea.addEventListener(eventName, unhighlight, false);
            });
            
            function highlight(e) {
                e.preventDefault();
                e.stopPropagation();
                dropArea.classList.add('border-blue-500', 'bg-blue-50');
            }
            
            function unhighlight(e) {
                e.preventDefault();
                e.stopPropagation();
                dropArea.classList.remove('border-blue-500', 'bg-blue-50');
            }
            
            // Handle the dropped files
            dropArea.addEventListener('drop', handleDrop, false);
            
            function handleDrop(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const dt = e.dataTransfer;
                const files = dt.files;
                
                if (files.length > 1) {
                    alert('Hanya diperbolehkan mengunggah 1 file PDF saja.');
                    return;
                }
                fileInput.files = files;
                handleFiles(files);
            }
            
            // Open file browser when clicking the browse button
            browseButton.addEventListener('click', function() {
                fileInput.click();
            });
            
            // Handle selected files from file input
            fileInput.addEventListener('change', function() {
                if (this.files.length) {
                    handleFiles(this.files);
                }
            });
            
            // Handle remove button click
            removeButton.addEventListener('click', function() {
                fileInput.value = '';
                showInitialState();
                submitButton.disabled = true;
                submitButton.classList.add('opacity-50', 'cursor-not-allowed');
                submitButton.classList.remove('hover:from-blue-700', 'hover:to-indigo-800');
            });
            
            function handleFiles(files) {
                if (files.length === 0) return;
                
                const file = files[0];
                
                // Check if file is PDF
                if (file.type !== 'application/pdf') {
                    alert('Hanya file PDF yang diperbolehkan.');
                    return;
                }
                
                // Check file size (5MB max)
                if (file.size > 5 * 1024 * 1024) {
                    alert('Ukuran file terlalu besar (maksimal 5MB).');
                    return;
                }
                
                // Show loading state
                showLoadingState();
                
                // Simulate processing (you can remove this setTimeout in production)
                setTimeout(() => {
                    displayFileInfo(file);
                    showFileSelectedState();
                    
                    // Enable submit button
                    submitButton.disabled = false;
                    submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
                    submitButton.classList.add('hover:from-blue-700', 'hover:to-indigo-800');
                }, 800);
            }
            
            function displayFileInfo(file) {
                // Format file size
                const fileSize = formatFileSize(file.size);
                
                // Update UI elements
                fileNameElement.textContent = file.name;
                fileSizeElement.textContent = fileSize;
            }
            
            function formatFileSize(bytes) {
                if (bytes < 1024) return bytes + ' bytes';
                else if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
                else return (bytes / 1048576).toFixed(1) + ' MB';
            }
            
            function showInitialState() {
                initialState.classList.remove('hidden');
                initialState.classList.add('flex');
                fileSelectedState.classList.add('hidden');
                fileSelectedState.classList.remove('flex');
                loadingState.classList.add('hidden');
                loadingState.classList.remove('flex');
            }
            
            function showFileSelectedState() {
                initialState.classList.add('hidden');
                initialState.classList.remove('flex');
                fileSelectedState.classList.remove('hidden');
                fileSelectedState.classList.add('flex');
                loadingState.classList.add('hidden');
                loadingState.classList.remove('flex');
            }
            
            function showLoadingState() {
                initialState.classList.add('hidden');
                initialState.classList.remove('flex');
                fileSelectedState.classList.add('hidden');
                fileSelectedState.classList.remove('flex');
                loadingState.classList.remove('hidden');
                loadingState.classList.add('flex');
            }
            
            // Prevent default browser behavior for drag and drop
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                document.body.addEventListener(eventName, preventDefaults, false);
            });
            
            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }
            
            // Make the entire drop area clickable to open file browser
            dropArea.addEventListener('click', function(e) {
                // Don't trigger if clicking on the remove button
                if (e.target !== removeButton && !removeButton.contains(e.target) && 
                    e.target !== browseButton && !browseButton.contains(e.target)) {
                    if (fileInput.value === '') {
                        fileInput.click();
                    }
                }
            });
        });
    </script>
</body>
</html>