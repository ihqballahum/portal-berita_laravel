<?php
require __DIR__ . '/../includes/bootstrap.php';
wajib_login();

$jml = array(
    'Berita'      => array((int) q('SELECT COUNT(*) FROM berita')->fetchColumn(), 'bg-aqua', 'fa-newspaper-o', 'admin/berita.php'),
    'Komentar'    => array((int) q('SELECT COUNT(*) FROM komentar')->fetchColumn(), 'bg-green', 'fa-comments-o', 'admin/komentar.php'),
    'Foto Galeri' => array((int) q('SELECT COUNT(*) FROM galeri_foto')->fetchColumn(), 'bg-yellow', 'fa-image', 'admin/galeri_foto.php'),
    'User'        => array((int) q('SELECT COUNT(*) FROM users')->fetchColumn(), 'bg-red', 'fa-user', 'admin/user.php'),
);
$terbaru = q('SELECT b.*, k.nama AS kategori FROM berita b JOIN kategori k ON k.id = b.kategori_id ORDER BY b.tanggal DESC LIMIT 5')->fetchAll();

$judul = 'Dashboard'; $sub = 'Ringkasan portal berita'; $aktif = 'dash';
include __DIR__ . '/../includes/admin_header.php';
?>
<div class="row">
	<?php foreach ($jml as $nama => $d): ?>
	<div class="col-md-3 col-sm-6">
		<div class="small-box <?= $d[1] ?>">
			<div class="inner"><h3><?= $d[0] ?></h3><p><?= e($nama) ?></p></div>
			<div class="icon"><i class="fa <?= $d[2] ?>"></i></div>
			<a href="<?= e(url($d[3])) ?>" class="small-box-footer">Lihat <i class="fa fa-arrow-circle-right"></i></a>
		</div>
	</div>
	<?php endforeach; ?>
</div>

<div class="box box-primary">
	<div class="box-header with-border"><h3 class="box-title">Berita Terbaru</h3></div>
	<div class="box-body no-padding">
		<table class="table table-striped">
			<thead><tr><th>Tanggal</th><th>Kategori</th><th>Judul</th><th>Status</th></tr></thead>
			<tbody>
			<?php foreach ($terbaru as $b): ?>
				<tr>
					<td><?= e(tgl($b['tanggal'])) ?></td>
					<td><?= e($b['kategori']) ?></td>
					<td><?= e($b['judul']) ?></td>
					<td><span class="label label-<?= $b['status'] === 'publish' ? 'success' : 'default' ?>"><?= e($b['status']) ?></span></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>
<?php include __DIR__ . '/../includes/admin_footer.php'; ?>
