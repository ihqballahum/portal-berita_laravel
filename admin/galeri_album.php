<?php
require __DIR__ . '/../includes/bootstrap.php';
wajib_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $aksi = isset($_POST['aksi']) ? $_POST['aksi'] : '';
    $id   = (int) (isset($_POST['id']) ? $_POST['id'] : 0);

    if ($aksi === 'simpan') {
        $nama = trim(isset($_POST['nama']) ? $_POST['nama'] : '');
        $ket  = trim(isset($_POST['keterangan']) ? $_POST['keterangan'] : '');
        if ($nama === '') {
            flash('danger', 'Nama album wajib diisi.');
        } elseif ($id) {
            q('UPDATE galeri_album SET nama = ?, slug = ?, keterangan = ? WHERE id = ?', array($nama, slug_unik('galeri_album', $nama, $id), $ket, $id));
            flash('success', 'Album berhasil diperbarui.');
        } else {
            q('INSERT INTO galeri_album (nama, slug, keterangan) VALUES (?,?,?)', array($nama, slug_unik('galeri_album', $nama), $ket));
            flash('success', 'Album berhasil ditambahkan.');
        }
    } elseif ($aksi === 'hapus') {
        // hapus file foto di dalam album, barisnya ikut terhapus (ON DELETE CASCADE)
        foreach (q('SELECT foto FROM galeri_foto WHERE album_id = ?', array($id))->fetchAll() as $f) {
            hapus_file('galeri', $f['foto']);
        }
        q('DELETE FROM galeri_album WHERE id = ?', array($id));
        flash('success', 'Album beserta fotonya berhasil dihapus.');
    }
    redirect('admin/galeri_album.php');
}

$edit = null;
if (isset($_GET['edit'])) {
    $edit = q('SELECT * FROM galeri_album WHERE id = ?', array((int) $_GET['edit']))->fetch();
}
$rows = q('SELECT a.*, (SELECT COUNT(*) FROM galeri_foto f WHERE f.album_id = a.id) AS jumlah FROM galeri_album a ORDER BY a.nama')->fetchAll();

$judul = 'Album Galeri'; $sub = 'Kelola album foto'; $aktif = 'g_album';
include __DIR__ . '/../includes/admin_header.php';
?>
<div class="row">
	<div class="col-md-4">
		<div class="box box-primary">
			<div class="box-header with-border"><h3 class="box-title"><?= $edit ? 'Edit Album' : 'Tambah Album' ?></h3></div>
			<form method="post">
				<?= csrf_field() ?>
				<input type="hidden" name="aksi" value="simpan">
				<input type="hidden" name="id" value="<?= $edit ? (int) $edit['id'] : 0 ?>">
				<div class="box-body">
					<div class="form-group"><label>Nama Album</label><input type="text" name="nama" class="form-control" value="<?= $edit ? e($edit['nama']) : '' ?>" required></div>
					<div class="form-group"><label>Keterangan</label><textarea name="keterangan" class="form-control" rows="3"><?= $edit ? e($edit['keterangan']) : '' ?></textarea></div>
				</div>
				<div class="box-footer">
					<button class="btn btn-primary"><i class="fa fa-save"></i> Simpan</button>
					<?php if ($edit): ?><a href="<?= e(url('admin/galeri_album.php')) ?>" class="btn btn-default">Batal</a><?php endif; ?>
				</div>
			</form>
		</div>
	</div>
	<div class="col-md-8">
		<div class="box box-primary">
			<div class="box-header with-border"><h3 class="box-title">Data Album</h3></div>
			<div class="box-body no-padding">
				<table class="table table-striped">
					<thead><tr><th width="50">No.</th><th>Nama</th><th>Keterangan</th><th>Foto</th><th width="90">Pilihan</th></tr></thead>
					<tbody>
					<?php $no = 1; foreach ($rows as $a): ?>
						<tr>
							<td><?= $no++ ?></td>
							<td><?= e($a['nama']) ?></td>
							<td><?= e($a['keterangan']) ?></td>
							<td><?= (int) $a['jumlah'] ?></td>
							<td>
								<a href="<?= e(url('admin/galeri_album.php?edit=' . $a['id'])) ?>" class="btn btn-info btn-xs" title="Edit"><i class="fa fa-edit"></i></a>
								<form method="post" class="form-inline-del" onsubmit="return confirm('Hapus album ini beserta semua fotonya?')">
									<?= csrf_field() ?>
									<input type="hidden" name="aksi" value="hapus">
									<input type="hidden" name="id" value="<?= (int) $a['id'] ?>">
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
