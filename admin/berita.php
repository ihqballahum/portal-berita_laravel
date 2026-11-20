<?php
require __DIR__ . '/../includes/bootstrap.php';
wajib_login();

$rows = q('SELECT b.*, k.nama AS kategori FROM berita b JOIN kategori k ON k.id = b.kategori_id ORDER BY b.tanggal DESC')->fetchAll();

$judul = 'Data Berita'; $sub = 'Kelola seluruh berita'; $aktif = 'b_data'; $pakai = array('datatables');
include __DIR__ . '/../includes/admin_header.php';
?>
<a href="<?= e(url('admin/berita_form.php')) ?>" class="btn btn-primary" style="margin-bottom:12px;"><i class="fa fa-plus"></i> Tambah Berita</a>

<div class="box box-primary">
	<div class="box-header with-border"><h3 class="box-title">Data Berita</h3></div>
	<div class="box-body">
		<table class="table table-bordered table-striped tabel">
			<thead>
				<tr><th width="50">No.</th><th>Tanggal Post</th><th>Kategori</th><th>Judul</th><th class="no-sort">Foto</th><th class="no-sort" width="90">Pilihan</th></tr>
			</thead>
			<tbody>
			<?php $no = 1; foreach ($rows as $b): ?>
				<tr>
					<td><?= $no++ ?></td>
					<td><?= e(date('Y-m-d', strtotime($b['tanggal']))) ?></td>
					<td><?= e(strtolower($b['kategori'])) ?></td>
					<td><?= e($b['judul']) ?> <?php if ($b['status'] === 'draft'): ?><span class="label label-default">draft</span><?php endif; ?></td>
					<td><img class="thumb" src="<?= e(foto_url('berita', $b['foto'])) ?>" alt=""></td>
					<td>
						<a href="<?= e(url('admin/berita_form.php?id=' . $b['id'])) ?>" class="btn btn-info btn-xs" title="Edit"><i class="fa fa-edit"></i></a>
						<form method="post" action="<?= e(url('admin/berita_hapus.php')) ?>" class="form-inline-del" onsubmit="return confirm('Hapus berita ini beserta komentarnya?')">
							<?= csrf_field() ?>
							<input type="hidden" name="id" value="<?= (int) $b['id'] ?>">
							<button type="submit" class="btn btn-danger btn-xs" title="Hapus"><i class="fa fa-trash"></i></button>
						</form>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>
<?php include __DIR__ . '/../includes/admin_footer.php'; ?>
