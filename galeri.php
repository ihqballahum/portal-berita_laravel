<?php
require __DIR__ . '/includes/bootstrap.php';

$slug  = isset($_GET['album']) ? $_GET['album'] : '';
$album = $slug !== '' ? q('SELECT * FROM galeri_album WHERE slug = ?', array($slug))->fetch() : null;

$judul_halaman = $album ? 'Album ' . $album['nama'] : 'Galeri Foto';
include __DIR__ . '/includes/public_header.php';

if ($album):
    $fotos = q('SELECT * FROM galeri_foto WHERE album_id = ? ORDER BY tanggal DESC', array($album['id']))->fetchAll();
?>
	<h3 style="margin-top:0;"><?= e($album['nama']) ?></h3>
	<p><a href="<?= e(url('galeri.php')) ?>">&larr; Semua album</a></p>
	<div class="row">
		<?php foreach ($fotos as $f): ?>
			<div class="col-sm-4 col-md-3 galeri-item">
				<a href="<?= e(foto_url('galeri', $f['foto'])) ?>" target="_blank"><img src="<?= e(foto_url('galeri', $f['foto'])) ?>" class="img-thumbnail" alt=""></a>
				<div class="text-center"><small><?= e($f['judul']) ?></small></div>
			</div>
		<?php endforeach; ?>
		<?php if (empty($fotos)): ?><div class="col-xs-12"><div class="kartu text-muted">Album ini belum berisi foto.</div></div><?php endif; ?>
	</div>
<?php else:
    $albums = q('SELECT a.*, (SELECT foto FROM galeri_foto f WHERE f.album_id = a.id ORDER BY f.id DESC LIMIT 1) AS sampul,
                        (SELECT COUNT(*) FROM galeri_foto f WHERE f.album_id = a.id) AS jml
                 FROM galeri_album a ORDER BY a.nama')->fetchAll();
?>
	<h3 style="margin-top:0;">Galeri Foto</h3>
	<div class="row">
		<?php foreach ($albums as $a): ?>
			<div class="col-sm-4 col-md-3 galeri-item">
				<a href="<?= e(url('galeri.php?album=' . urlencode($a['slug']))) ?>"><img src="<?= e(foto_url('galeri', $a['sampul'])) ?>" class="img-thumbnail" alt=""></a>
				<div class="text-center"><b><?= e($a['nama']) ?></b><br><small class="text-muted"><?= (int) $a['jml'] ?> foto</small></div>
			</div>
		<?php endforeach; ?>
		<?php if (empty($albums)): ?><div class="col-xs-12"><div class="kartu text-muted">Belum ada album.</div></div><?php endif; ?>
	</div>
<?php endif; ?>
<?php include __DIR__ . '/includes/public_footer.php'; ?>
