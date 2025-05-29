<?php
include '../koneksi.php';

// Check user access
$level_user = $_COOKIE['level_user'] ?? null;
$is_operator = ($level_user == 'operator');
$nis_user = $_COOKIE['NIS'] ?? null;
$id_sertif = $_GET['id_sertif'] ?? null;

// Redirect if not logged in or invalid certificate ID
if (!$level_user) {
    echo "<script>window.location.href='halaman_utama.php?page=sertifikat';</script>";
}

if (!$id_sertif) {
    echo "<script>window.location.href='halaman_utama.php?page=sertifikat';</script>";
}

// Get certificate data
$query = "SELECT s.*, k.jenis_kegiatan FROM tb_sertif s 
          JOIN tb_kegiatan k ON s.id_kegiatan = k.id_kegiatan 
          WHERE s.id_sertif = '$id_sertif'";
$result = mysqli_query($koneksi, $query);
$data = mysqli_fetch_assoc($result);

// Redirect if data not found or unauthorized access
if (!$data || (!$is_operator && $nis_user !== $data['NIS'])) {
    echo "<script>window.location.href='halaman_utama.php?page=sertifikat';</script>";
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($is_operator) {
        // Operator update (status and notes)
        $status = $_POST['status'];
        $catatan = $_POST['catatan'];
        $query = "UPDATE tb_sertif SET status='$status', catatan='$catatan' WHERE id_sertif='$id_sertif'";
    } else {
        // Student update (certificate type and file)
        $jenis_kegiatan = $_POST['jenis_kegiatan'];
        $query = "UPDATE tb_sertif SET id_kegiatan='$jenis_kegiatan'";
        
        // Handle file upload if provided
        if (!empty($_FILES['sertif']['name'])) {
            $file_extension = strtolower(pathinfo($_FILES['sertif']['name'], PATHINFO_EXTENSION));
            
            if ($file_extension != "pdf") {
                $upload_error = "Hanya file PDF yang diperbolehkan!";
            } else {
                // Create a unique filename to prevent overwrites and access issues
                $unique_filename = time() . '_' . $data['NIS'] . '_' . basename($_FILES['sertif']['name']);
                $target_dir = "uploads/";
                
                // Ensure uploads directory exists
                if (!file_exists($target_dir)) {
                    mkdir($target_dir, 0755, true);
                }
                
                $target_file = $target_dir . $unique_filename;
                
                if (move_uploaded_file($_FILES['sertif']['tmp_name'], $target_file)) {
                    $query .= ", sertif='$unique_filename'";
                } else {
                    $upload_error = "Gagal mengunggah file!";
                }
            }
        }
        
        $query .= " WHERE id_sertif='$id_sertif'";
    }
    
    // Execute update query if no errors
    if (!isset($upload_error) && mysqli_query($koneksi, $query)) {
        echo "<script>alert('Berhasil Update Data');window.location.href='halaman_utama.php?page=sertifikat';</script>";
    }
}

// Get kegiatan options for dropdown
$kegiatan_query = mysqli_query($koneksi, "SELECT * FROM tb_kegiatan");

// Get the certificate file path - Fix path issues
$certificate_file = null;
if (!empty($data['sertif'])) {
    $file_path = 'uploads/' . $data['sertif'];
    // Check if file exists
    if (file_exists($file_path)) {
        $certificate_file = $file_path;
    }
}
?>

<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $is_operator ? 'Validasi' : 'Edit' ?> Sertifikat</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-full overflow-hidden">
    <div class="h-screen flex flex-col">
        <div class="flex-1 flex flex-col md:flex-row overflow-hidden">
            <!-- Form panel - will be on the left side on desktop -->
            <div class="md:w-1/3 p-4 overflow-y-auto">
                <div class="bg-white rounded-lg shadow-md h-full">
                    <div class="bg-blue-600 text-white p-4">
                        <h2 class="text-xl font-semibold"><?= $is_operator ? 'Validasi' : 'Edit' ?> Sertifikat</h2>
                    </div>
                    
                    <?php if (isset($upload_error)): ?>
                        <div class="p-4 mb-4 bg-red-100 text-red-700 rounded">
                            <?= $upload_error ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="p-6">
                        <form method="POST" enctype="multipart/form-data" class="space-y-4">
                            <!-- NIS Field (Read-only) -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">NIS:</label>
                                <input type="text" value="<?= htmlspecialchars($data['NIS']) ?>" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50" readonly>
                            </div>
                            
                            <!-- Jenis Kegiatan Field -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Jenis Kegiatan:</label>
                                <?php if ($is_operator): ?>
                                    <input type="text" value="<?= htmlspecialchars($data['jenis_kegiatan']) ?>" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50" readonly>
                                <?php else: ?>
                                    <select name="jenis_kegiatan" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                        <?php while ($kegiatan = mysqli_fetch_assoc($kegiatan_query)): ?>
                                            <option value="<?= $kegiatan['id_kegiatan'] ?>" 
                                                    <?= ($data['id_kegiatan'] == $kegiatan['id_kegiatan']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($kegiatan['jenis_kegiatan']) ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Student-specific fields -->
                            <?php if (!$is_operator): ?>
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700">Upload Sertifikat Baru (PDF):</label>
                                    <div id="drop-area" class="border-2 border-dashed border-gray-300 rounded-md p-4 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                        </svg>
                                        <div class="mt-2">
                                            <label for="file-upload" class="cursor-pointer text-blue-600 hover:text-blue-500">
                                                <span id="file-chosen">Upload file PDF</span>
                                                <input id="file-upload" name="sertif" type="file" class="sr-only" accept=".pdf">
                                            </label>
                                            <p class="text-xs text-gray-500 mt-1">PDF hingga 10MB</p>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Operator-specific fields -->
                            <?php if ($is_operator): ?>
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700">Status:</label>
                                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                        <option value="Pending" <?= ($data['status'] == 'Pending') ? 'selected' : '' ?>>Pending</option>
                                        <option value="Tidak Valid" <?= ($data['status'] == 'Tidak Valid') ? 'selected' : '' ?>>Tidak Valid</option>
                                        <option value="Valid" <?= ($data['status'] == 'Valid') ? 'selected' : '' ?>>Valid</option>
                                    </select>
                                </div>
                                
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700">Catatan:</label>
                                    <textarea name="catatan" rows="4" 
                                              class="w-full px-3 py-2 border border-gray-300 rounded-md"><?= htmlspecialchars($data['catatan']) ?></textarea>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Submit Button -->
                            <div class="pt-4">
                                <button type="submit" class="w-full py-2 px-4 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                    Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- PDF Display in center - will take remaining space -->
            <div class="md:w-2/3 p-4 flex-1 flex flex-col">
                <?php if ($certificate_file): ?>
                    <div class="bg-white rounded-lg shadow-md h-full flex flex-col">
                        <div class="bg-gray-100 p-4 border-b">
                            <h3 class="text-lg font-semibold">File Sertifikat</h3>
                        </div>
                        
                        <div class="flex-1 bg-gray-800 relative">
                            <!-- PDF Viewer using object tag instead of iframe for better compatibility -->
                            <object 
                                data="<?= $certificate_file ?>" 
                                type="application/pdf"
                                class="absolute inset-0 w-full h-full">
                                <div class="w-full h-full flex items-center justify-center bg-gray-200">
                                    <div class="text-center p-4">
                                        <p class="text-red-600 font-medium">PDF tidak dapat ditampilkan.</p>
                                        <a href="<?= $certificate_file ?>" class="text-blue-600 underline" target="_blank">Klik disini untuk membuka file</a>
                                    </div>
                                </div>
                            </object>
                        </div>
                        
                        <div class="bg-gray-100 p-4 border-t flex justify-between items-center">
                            <span class="text-sm text-gray-600 truncate max-w-xs"><?= basename($data['sertif']) ?></span>
                            <a href="<?= $certificate_file ?>" target="_blank" 
                               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                Buka di Tab Baru
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="bg-white rounded-lg shadow-md flex h-full items-center justify-center">
                        <div class="text-center p-8">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3 class="mt-4 text-lg font-medium text-gray-900">Tidak ada file sertifikat</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                <?= $is_operator ? 'Siswa belum mengunggah file sertifikat.' : 'Silakan unggah file sertifikat Anda.' ?>
                            </p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <?php if (!$is_operator): ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const dropArea = document.getElementById('drop-area');
        const fileInput = document.getElementById('file-upload');
        const fileChosen = document.getElementById('file-chosen');
        
        // Prevent default drag behaviors
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, e => {
                e.preventDefault();
                e.stopPropagation();
            });
        });
        
        // Highlight drop area when dragging
        ['dragenter', 'dragover'].forEach(eventName => {
            dropArea.addEventListener(eventName, () => {
                dropArea.classList.add('bg-gray-100', 'border-blue-500');
            });
        });
        
        ['dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, () => {
                dropArea.classList.remove('bg-gray-100', 'border-blue-500');
            });
        });
        
        // Handle dropped files
        dropArea.addEventListener('drop', e => {
            fileInput.files = e.dataTransfer.files;
            updateFileName();
        });
        
        // Handle selected files
        fileInput.addEventListener('change', updateFileName);
        
        function updateFileName() {
            const file = fileInput.files[0];
            if (file) {
                if (file.type !== 'application/pdf') {
                    alert('Hanya file PDF yang diperbolehkan!');
                    fileInput.value = '';
                    fileChosen.textContent = 'Upload file PDF';
                } else {
                    fileChosen.textContent = file.name.length > 20 
                        ? file.name.substring(0, 17) + '...' 
                        : file.name;
                }
            }
        }
    });
    </script>
    <?php endif; ?>
</body>
</html>