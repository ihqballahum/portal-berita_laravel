<?php
$sb_kat = q('SELECT k.nama, k.slug, (SELECT COUNT(*) FROM berita b WHERE b.kategori_id = k.id AND b.status = \'publish\') AS jml FROM kategori k ORDER BY k.nama')->fetchAll();
$sb_pop = q('SELECT judul, slug FROM berita WHERE status = \'publish\' ORDER BY dilihat DESC LIMIT 5')->fetchAll();
?>
<div class="kartu">
	<h3 style="margin-top:0;">Kategori</h3>
	<ul class="list-unstyled">
		<?php foreach ($sb_kat as $k): ?>
			<li style="margin-bottom:4px;"><a href="<?= e(url('kategori.php?slug=' . urlencode($k['slug']))) ?>"><?= e($k['nama']) ?></a> <span class="badge"><?= (int) $k['jml'] ?></span></li>
		<?php endforeach; ?>
	</ul>
</div>
<div class="kartu">
	<h3 style="margin-top:0;">Terpopuler</h3>
	<ul class="list-unstyled">
		<?php foreach ($sb_pop as $p): ?>
			<li style="margin-bottom:6px;"><a href="<?= e(url('berita.php?slug=' . urlencode($p['slug']))) ?>"><?= e($p['judul']) ?></a></li>
		<?php endforeach; ?>
	</ul>
</div>
