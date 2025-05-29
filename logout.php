<?php
// Hapus cookie dengan benar
setcookie('username', '', time() - 3600, '/');
setcookie('level_user', '', time() - 3600, '/');
setcookie('nama_lengkap', '', time() - 3600, '/');
setcookie('NIS', '', time() - 3600, '/');
setcookie('ingat_password', '', time() - 3600, '/');

echo "<script>alert('Berhasil Logout');window.location.href='login.php';</script>";
exit;
?>