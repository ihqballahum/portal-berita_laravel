<?php
require __DIR__ . '/../includes/bootstrap.php';
wajib_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $aksi = isset($_POST['aksi']) ? $_POST['aksi'] : '';
    $id   = (int) (isset($_POST['id']) ? $_POST['id'] : 0);

    if ($aksi === 'simpan') {
        $album = (int) (isset($_POST['album_id']) ? $_POST['album_id'] : 0);
        $jdl   = trim(isset($_POST['judul']) ? $_POST['judul'] : '');
        $lama  = $id ? q('SELECT * FROM galeri_foto WHERE id = ?', array($id))->fetch() : null;
        $ada_album = $album ? q('SELECT id FROM galeri_album WHERE id = ?', array($album))->fetch() : false;
        $foto  = upload_image('foto', 'galeri', $err);

        if ($jdl === '' || ! $ada_album) {
            flash('danger', 'Album dan judul foto wajib diisi.');
        } elseif ($err !== '') {
            flash('danger', $err);
        } elseif ( ! $id && ! $foto) {
            flash('danger', 'File foto wajib diunggah.');
        } elseif ($id && $lama) {
            q('UPDATE galeri_foto SET album_id = ?, judul = ? WHERE id = ?', array($album, $jdl, $id));
            if ($foto) {
                hapus_file('galeri', $lama['foto']);
                q('UPDATE galeri_foto SET foto = ? WHERE id = ?', array($foto, $id));
            }
            flash('success', 'Foto berhasil diperbarui.');
        } else {
            q('INSERT INTO galeri_foto (album_id, judul, foto, tanggal) VALUES (?,?,?,NOW())', array($album, $jdl, $foto));
            flash('success', 'Foto berhasil ditambahkan.');
        }
    } elseif ($aksi === 'hapus') {
        $lama = q('SELECT * FROM galeri_foto WHERE id = ?', array($id))->fetch();
        if ($lama) {
            hapus_file('galeri', $lama['foto']);
            q('DELETE FROM galeri_foto WHERE id = ?', array($id));
            flash('success', 'Foto berhasil dihapus.');
        }
    }
    redirect('admin/galeri_foto.php');
}

$edit = null;
if (isset($_GET['edit'])) {
    $edit = q('SELECT * FROM galeri_foto WHERE id = ?', array((int) $_GET['edit']))->fetch();
}
$albums = q('SELECT * FROM galeri_album ORDER BY nama')->fetchAll();
$rows   = q('SELECT f.*, a.nama AS album FROM galeri_foto f JOIN galeri_album a ON a.id = f.album_id ORDER BY f.tanggal DESC')->fetchAll();

$judul = 'Foto Galeri'; $sub = 'Kelola foto dalam album'; $aktif = 'g_foto'; $pakai = array('datatables');
include __DIR__ . '/../includes/admin_header.php';
?>
<div class="row">
	<div class="col-md-4">
		<div class="box box-primary">
			<div class="box-header with-border"><h3 class="box-title"><?= $edit ? 'Edit Foto' : 'Tambah Foto' ?></h3></div>
			<?php if (empty($albums)): ?>
				<div class="box-body">Buat album terlebih dahulu di menu <a href="<?= e(url('admin/galeri_album.php')) ?>">Album</a>.</div>
			<?php else: ?>
			<form method="post" enctype="multipart/form-data">
				<?= csrf_field() ?>
				<input type="hidden" name="aksi" value="simpan">
				<input type="hidden" name="id" value="<?= $edit ? (int) $edit['id'] : 0 ?>">
				<div class="box-body">
					<div class="form-group">
						<label>Album</label>
						<select name="album_id" class="form-control" required>
							<?php foreach ($albums as $a): ?>
								<option value="<?= (int) $a['id'] ?>" <?= ($edit && (int) $edit['album_id'] === (int) $a['id']) ? 'selected' : '' ?>><?= e($a['nama']) ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="form-group"><label>Judul Foto</label><input type="text" name="judul" class="form-control" value="<?= $edit ? e($edit['judul']) : '' ?>" required></div>
					<div class="form-group">
						<label>File Foto</label>
						<?php if ($edit && $edit['foto']): ?><div><img src="<?= e(foto_url('galeri', $edit['foto'])) ?>" style="max-width:100%;margin-bottom:8px;"></div><?php endif; ?>
						<input type="file" name="foto" accept="image/*">
					</div>
				</div>
				<div class="box-footer">
					<button class="btn btn-primary"><i class="fa fa-save"></i> Simpan</button>
					<?php if ($edit): ?><a href="<?= e(url('admin/galeri_foto.php')) ?>" class="btn btn-default">Batal</a><?php endif; ?>
				</div>
			</form>
			<?php endif; ?>
		</div>
	</div>
	<div class="col-md-8">
		<div class="box box-primary">
			<div class="box-header with-border"><h3 class="box-title">Data Foto</h3></div>
			<div class="box-body">
				<table class="table table-bordered table-striped tabel">
					<thead><tr><th width="50">No.</th><th class="no-sort">Foto</th><th>Judul</th><th>Album</th><th class="no-sort" width="90">Pilihan</th></tr></thead>
					<tbody>
					<?php $no = 1; foreach ($rows as $f): ?>
						<tr>
							<td><?= $no++ ?></td>
							<td><img class="thumb" src="<?= e(foto_url('galeri', $f['foto'])) ?>" alt=""></td>
							<td><?= e($f['judul']) ?></td>
							<td><?= e($f['album']) ?></td>
							<td>
								<a href="<?= e(url('admin/galeri_foto.php?edit=' . $f['id'])) ?>" class="btn btn-info btn-xs" title="Edit"><i class="fa fa-edit"></i></a>
								<form method="post" class="form-inline-del" onsubmit="return confirm('Hapus foto ini?')">
									<?= csrf_field() ?>
									<input type="hidden" name="aksi" value="hapus">
									<input type="hidden" name="id" value="<?= (int) $f['id'] ?>">
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
