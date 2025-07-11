<?php
session_start();
session_unset();
session_destroy();
echo "Anda telah logout. <a href='index.php'>Kembali ke halaman utama</a>";
?>