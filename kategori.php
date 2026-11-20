<?php
require __DIR__ . '/includes/bootstrap.php';

$slug = isset($_GET['slug']) ? $_GET['slug'] : '';
$kat  = q('SELECT * FROM kategori WHERE slug = ?', array($slug))->fetch();
if ( ! $kat) {
    http_response_code(404);
    $judul_halaman = 'Tidak ditemukan';
    include __DIR__ . '/includes/public_header.php';
    echo '<div class="kartu"><h3>Kategori tidak ditemukan</h3><a href="' . e(url('index.php')) . '">Kembali ke beranda</a></div>';
    include __DIR__ . '/includes/public_footer.php';
    exit;
}

$hal   = max(1, (int) (isset($_GET['hal']) ? $_GET['hal'] : 1));
$per   = 6;
$total = (int) q("SELECT COUNT(*) FROM berita WHERE kategori_id = ? AND status = 'publish'", array($kat['id']))->fetchColumn();
$rows  = q("SELECT b.*, k.nama AS kategori, k.slug AS kslug FROM berita b JOIN kategori k ON k.id = b.kategori_id
            WHERE b.kategori_id = ? AND b.status = 'publish' ORDER BY b.tanggal DESC LIMIT $per OFFSET " . (($hal - 1) * $per), array($kat['id']))->fetchAll();

$judul_halaman = 'Kategori ' . $kat['nama'];
include __DIR__ . '/includes/public_header.php';
?>
<div class="row">
	<div class="col-md-8">
		<h3 style="margin-top:0;">Kategori: <?= e($kat['nama']) ?></h3>
		<?php daftar_berita($rows); ?>
		<?= paging($total, $hal, $per, url('kategori.php?slug=' . urlencode($kat['slug']))) ?>
	</div>
	<div class="col-md-4"><?php include __DIR__ . '/includes/sidebar_publik.php'; ?></div>
</div>
<?php include __DIR__ . '/includes/public_footer.php'; ?>
