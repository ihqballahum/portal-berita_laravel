<?php
require __DIR__ . '/../includes/bootstrap.php';
wajib_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $aksi = isset($_POST['aksi']) ? $_POST['aksi'] : '';
    $id   = (int) (isset($_POST['id']) ? $_POST['id'] : 0);

    if ($aksi === 'simpan') {
        $nama = trim(isset($_POST['nama']) ? $_POST['nama'] : '');
        if ($nama === '') {
            flash('danger', 'Nama kategori wajib diisi.');
        } elseif ($id) {
            q('UPDATE kategori SET nama = ?, slug = ? WHERE id = ?', array($nama, slug_unik('kategori', $nama, $id), $id));
            flash('success', 'Kategori berhasil diperbarui.');
        } else {
            q('INSERT INTO kategori (nama, slug) VALUES (?, ?)', array($nama, slug_unik('kategori', $nama)));
            flash('success', 'Kategori berhasil ditambahkan.');
        }
    } elseif ($aksi === 'hapus') {
        $dipakai = (int) q('SELECT COUNT(*) FROM berita WHERE kategori_id = ?', array($id))->fetchColumn();
        if ($dipakai > 0) {
            flash('danger', 'Kategori tidak bisa dihapus karena dipakai oleh ' . $dipakai . ' berita.');
        } else {
            q('DELETE FROM kategori WHERE id = ?', array($id));
            flash('success', 'Kategori berhasil dihapus.');
        }
    }
    redirect('admin/kategori.php');
}

$edit = null;
if (isset($_GET['edit'])) {
    $edit = q('SELECT * FROM kategori WHERE id = ?', array((int) $_GET['edit']))->fetch();
}
$rows = q('SELECT k.*, (SELECT COUNT(*) FROM berita b WHERE b.kategori_id = k.id) AS jumlah FROM kategori k ORDER BY k.nama')->fetchAll();

$judul = 'Kategori'; $sub = 'Kelola kategori berita'; $aktif = 'kat';
include __DIR__ . '/../includes/admin_header.php';
?>
<div class="row">
	<div class="col-md-4">
		<div class="box box-primary">
			<div class="box-header with-border"><h3 class="box-title"><?= $edit ? 'Edit Kategori' : 'Tambah Kategori' ?></h3></div>
			<form method="post">
				<?= csrf_field() ?>
				<input type="hidden" name="aksi" value="simpan">
				<input type="hidden" name="id" value="<?= $edit ? (int) $edit['id'] : 0 ?>">
				<div class="box-body">
					<div class="form-group">
						<label>Nama Kategori</label>
						<input type="text" name="nama" class="form-control" value="<?= $edit ? e($edit['nama']) : '' ?>" required>
					</div>
				</div>
				<div class="box-footer">
					<button class="btn btn-primary"><i class="fa fa-save"></i> Simpan</button>
					<?php if ($edit): ?><a href="<?= e(url('admin/kategori.php')) ?>" class="btn btn-default">Batal</a><?php endif; ?>
				</div>
			</form>
		</div>
	</div>
	<div class="col-md-8">
		<div class="box box-primary">
			<div class="box-header with-border"><h3 class="box-title">Data Kategori</h3></div>
			<div class="box-body no-padding">
				<table class="table table-striped">
					<thead><tr><th width="50">No.</th><th>Nama</th><th>Slug</th><th>Berita</th><th width="90">Pilihan</th></tr></thead>
					<tbody>
					<?php $no = 1; foreach ($rows as $k): ?>
						<tr>
							<td><?= $no++ ?></td>
							<td><?= e($k['nama']) ?></td>
							<td><code><?= e($k['slug']) ?></code></td>
							<td><?= (int) $k['jumlah'] ?></td>
							<td>
								<a href="<?= e(url('admin/kategori.php?edit=' . $k['id'])) ?>" class="btn btn-info btn-xs" title="Edit"><i class="fa fa-edit"></i></a>
								<form method="post" class="form-inline-del" onsubmit="return confirm('Hapus kategori ini?')">
									<?= csrf_field() ?>
									<input type="hidden" name="aksi" value="hapus">
									<input type="hidden" name="id" value="<?= (int) $k['id'] ?>">
									<button class="btn btn-danger btn-xs" title="Hapus"><i class="fa fa-trash"></i></button>
								</form>
							</td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<?php include __DIR__ . '/../includes/admin_footer.php'; ?>
