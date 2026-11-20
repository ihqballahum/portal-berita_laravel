<?php
require __DIR__ . '/../includes/bootstrap.php';
wajib_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $aksi = isset($_POST['aksi']) ? $_POST['aksi'] : '';
    $id   = (int) (isset($_POST['id']) ? $_POST['id'] : 0);

    if ($aksi === 'simpan') {
        $jdl = trim(isset($_POST['judul']) ? $_POST['judul'] : '');
        $ket = trim(isset($_POST['keterangan']) ? $_POST['keterangan'] : '');
        $lama = $id ? q('SELECT * FROM berita_foto WHERE id = ?', array($id))->fetch() : null;
        $foto = upload_image('foto', 'berita_foto', $err);

        if ($jdl === '') {
            flash('danger', 'Judul wajib diisi.');
        } elseif ($err !== '') {
            flash('danger', $err);
        } elseif ( ! $id && ! $foto) {
            flash('danger', 'Foto wajib diunggah untuk berita foto baru.');
        } elseif ($id && $lama) {
            q('UPDATE berita_foto SET judul = ?, keterangan = ? WHERE id = ?', array($jdl, $ket, $id));
            if ($foto) {
                hapus_file('berita_foto', $lama['foto']);
                q('UPDATE berita_foto SET foto = ? WHERE id = ?', array($foto, $id));
            }
            flash('success', 'Berita foto berhasil diperbarui.');
        } else {
            q('INSERT INTO berita_foto (judul, foto, keterangan, tanggal) VALUES (?,?,?,NOW())', array($jdl, $foto, $ket));
            flash('success', 'Berita foto berhasil ditambahkan.');
        }
    } elseif ($aksi === 'hapus') {
        $lama = q('SELECT * FROM berita_foto WHERE id = ?', array($id))->fetch();
        if ($lama) {
            hapus_file('berita_foto', $lama['foto']);
            q('DELETE FROM berita_foto WHERE id = ?', array($id));
            flash('success', 'Berita foto berhasil dihapus.');
        }
    }
    redirect('admin/berita_foto.php');
}

$edit = null;
if (isset($_GET['edit'])) {
    $edit = q('SELECT * FROM berita_foto WHERE id = ?', array((int) $_GET['edit']))->fetch();
}
$rows = q('SELECT * FROM berita_foto ORDER BY tanggal DESC')->fetchAll();

$judul = 'Berita Foto'; $sub = 'Tampil sebagai slider di beranda'; $aktif = 'bf';
include __DIR__ . '/../includes/admin_header.php';
?>
<div class="row">
	<div class="col-md-4">
		<div class="box box-primary">
			<div class="box-header with-border"><h3 class="box-title"><?= $edit ? 'Edit Berita Foto' : 'Tambah Berita Foto' ?></h3></div>
			<form method="post" enctype="multipart/form-data">
				<?= csrf_field() ?>
				<input type="hidden" name="aksi" value="simpan">
				<input type="hidden" name="id" value="<?= $edit ? (int) $edit['id'] : 0 ?>">
				<div class="box-body">
					<div class="form-group"><label>Judul</label><input type="text" name="judul" class="form-control" value="<?= $edit ? e($edit['judul']) : '' ?>" required></div>
					<div class="form-group"><label>Keterangan</label><textarea name="keterangan" class="form-control" rows="3"><?= $edit ? e($edit['keterangan']) : '' ?></textarea></div>
					<div class="form-group">
						<label>Foto</label>
						<?php if ($edit && $edit['foto']): ?><div><img src="<?= e(foto_url('berita_foto', $edit['foto'])) ?>" style="max-width:100%;margin-bottom:8px;"></div><?php endif; ?>
						<input type="file" name="foto" accept="image/*">
					</div>
				</div>
				<div class="box-footer">
					<button class="btn btn-primary"><i class="fa fa-save"></i> Simpan</button>
					<?php if ($edit): ?><a href="<?= e(url('admin/berita_foto.php')) ?>" class="btn btn-default">Batal</a><?php endif; ?>
				</div>
			</form>
		</div>
	</div>
	<div class="col-md-8">
		<div class="box box-primary">
			<div class="box-header with-border"><h3 class="box-title">Data Berita Foto</h3></div>
			<div class="box-body no-padding">
				<table class="table table-striped">
					<thead><tr><th width="50">No.</th><th>Foto</th><th>Judul</th><th>Tanggal</th><th width="90">Pilihan</th></tr></thead>
					<tbody>
					<?php $no = 1; foreach ($rows as $f): ?>
						<tr>
							<td><?= $no++ ?></td>
							<td><img class="thumb" src="<?= e(foto_url('berita_foto', $f['foto'])) ?>" alt=""></td>
							<td><b><?= e($f['judul']) ?></b><br><small class="text-muted"><?= e($f['keterangan']) ?></small></td>
							<td><?= e(tgl($f['tanggal'])) ?></td>
							<td>
								<a href="<?= e(url('admin/berita_foto.php?edit=' . $f['id'])) ?>" class="btn btn-info btn-xs" title="Edit"><i class="fa fa-edit"></i></a>
								<form method="post" class="form-inline-del" onsubmit="return confirm('Hapus berita foto ini?')">
									<?= csrf_field() ?>
									<input type="hidden" name="aksi" value="hapus">
									<input type="hidden" name="id" value="<?= (int) $f['id'] ?>">
									<button class="btn btn-danger btn-xs" title="Hapus"><i class="fa fa-trash"></i></button>
								</form>
							</td>
						</tr>
					<?php endforeach; ?>
					<?php if (empty($rows)): ?><tr><td colspan="5" class="text-center text-muted">Belum ada data.</td></tr><?php endif; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<?php include __DIR__ . '/../includes/admin_footer.php'; ?>
