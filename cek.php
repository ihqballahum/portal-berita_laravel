<?php
// Halaman diagnosa. Hapus file ini jika website sudah dipakai online.
error_reporting(E_ALL);
ini_set('display_errors', '1');
require __DIR__ . '/config.php';

function baris($label, $ok, $info = '')
{
    echo '<tr><td>' . htmlspecialchars($label) . '</td><td style="color:' . ($ok ? 'green' : 'red') . '"><b>' . ($ok ? 'OK' : 'GAGAL') . '</b></td><td>' . htmlspecialchars($info) . '</td></tr>';
}
?>
<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>Cek Instalasi</title>
<style>body{font-family:Arial;margin:30px}td,th{border:1px solid #ccc;padding:6px 10px}table{border-collapse:collapse}</style></head>
<body>
<h2>Cek Instalasi Portal Berita</h2>
<table>
<tr><th>Pemeriksaan</th><th>Hasil</th><th>Keterangan</th></tr>
<?php
baris('Versi PHP (minimal 7.4)', version_compare(PHP_VERSION, '7.4.0', '>='), PHP_VERSION);
baris('Ekstensi pdo_mysql', extension_loaded('pdo_mysql'));
baris('Ekstensi mbstring', extension_loaded('mbstring'));
baris('Ekstensi gd/getimagesize (upload gambar)', function_exists('getimagesize'));

foreach (array('uploads/berita', 'uploads/berita_foto', 'uploads/galeri', 'storage/sessions') as $d) {
    $p = __DIR__ . '/' . $d;
    if ( ! is_dir($p)) { @mkdir($p, 0777, true); }
    baris('Folder bisa ditulis: ' . $d, is_dir($p) && is_writable($p));
}

try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
    baris('Koneksi database "' . DB_NAME . '"', true);
    foreach (array('users', 'kategori', 'berita', 'komentar', 'berita_foto', 'galeri_album', 'galeri_foto') as $t) {
        try {
            $n = $pdo->query("SELECT COUNT(*) FROM `$t`")->fetchColumn();
            baris('Tabel ' . $t, true, $n . ' baris');
        } catch (Exception $e) {
            baris('Tabel ' . $t, false, 'tidak ditemukan - import portal_berita.sql');
        }
    }
} catch (Exception $e) {
    baris('Koneksi database "' . DB_NAME . '"', false, $e->getMessage());
}
?>
</table>
<p>Jika semua <b style="color:green">OK</b>, buka <a href="index.php">website</a> atau <a href="admin/login.php">halaman login</a>.</p>
</body></html>
