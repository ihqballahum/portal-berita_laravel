<?php
require __DIR__ . '/../includes/bootstrap.php';
wajib_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect('admin/berita.php'); }
csrf_check();

$id  = (int) (isset($_POST['id']) ? $_POST['id'] : 0);
$row = q('SELECT * FROM berita WHERE id = ?', array($id))->fetch();

if ($row) {
    hapus_file('berita', $row['foto']);
    q('DELETE FROM berita WHERE id = ?', array($id));   // komentar ikut terhapus (ON DELETE CASCADE)
    flash('success', 'Berita berhasil dihapus.');
} else {
    flash('danger', 'Berita tidak ditemukan.');
}
redirect('admin/berita.php');
