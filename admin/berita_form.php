<?php
require __DIR__ . '/../includes/bootstrap.php';
wajib_login();

$id  = (int) (isset($_GET['id']) ? $_GET['id'] : 0);
$row = null;
if ($id) {
    $row = q('SELECT * FROM berita WHERE id = ?', array($id))->fetch();
    if ( ! $row) {
        flash('danger', 'Berita tidak ditemukan.');
        redirect('admin/berita.php');
    }
}
$kategori = q('SELECT * FROM kategori ORDER BY nama')->fetchAll();
$data = $row ? $row : array('judul' => '', 'kategori_id' => 0, 'isi' => '', 'status' => 'publish', 'foto' => null);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $data['judul']       = trim(isset($_POST['judul']) ? $_POST['judul'] : '');
    $data['kategori_id'] = (int) (isset($_POST['kategori_id']) ? $_POST['kategori_id'] : 0);
    $data['isi']         = bersihkan_html(isset($_POST['isi']) ? $_POST['isi'] : '');
    $data['status']      = (isset($_POST['status']) && $_POST['status'] === 'draft') ? 'draft' : 'publish';

    $ada_kat = $data['kategori_id'] ? q('SELECT id FROM kategori WHERE id = ?', array($data['kategori_id']))->fetch() : false;

    if ($data['judul'] === '' || trim(strip_tags($data['isi'])) === '' || ! $ada_kat) {
        flash('danger', 'Judul, kategori, dan isi berita wajib diisi.');
    } else {
        $foto = upload_image('foto', 'berita', $err);
        if ($err !== '') {
            flash('danger', $err);
        } else {
            $slug = ($row && $row['judul'] === $data['judul']) ? $row['slug'] : slug_unik('berita', $data['judul'], $id);
            if ($id) {
                q('UPDATE berita SET judul=?, slug=?, kategori_id=?, isi=?, status=? WHERE id=?',
                  array($data['judul'], $slug, $data['kategori_id'], $data['isi'], $data['status'], $id));
                if ($foto) {
                    hapus_file('berita', $row['foto']);
                    q('UPDATE berita SET foto=? WHERE id=?', array($foto, $id));
                }
                flash('success', 'Berita berhasil diperbarui.');
            } else {
                q('INSERT INTO berita (judul, slug, kategori_id, isi, foto, penulis_id, status, tanggal) VALUES (?,?,?,?,?,?,?,NOW())',
                  array($data['judul'], $slug, $data['kategori_id'], $data['isi'], $foto, user()['id'], $data['status']));
                flash('success', 'Berita berhasil ditambahkan.');
            }
            redirect('admin/berita.php');
        }
    }
}

$judul = $id ? 'Edit Berita' : 'Tambah Berita'; $sub = '';
$aktif = $id ? 'b_data' : 'b_tambah'; $pakai = array('summernote');
include __DIR__ . '/../includes/admin_header.php';
?>
<div class="box box-primary">
	<form method="post" action="" enctype="multipart/form-data">
		<?= csrf_field() ?>
		<div class="box-body">
			<div class="form-group">
				<label>Judul Berita</label>
				<input type="text" name="judul" class="form-control" value="<?= e($data['judul']) ?>" required>
			</div>
			<div class="row">
				<div class="col-md-6 form-group">
					<label>Kategori</label>
					<select name="kategori_id" class="form-control" required>
						<option value="">-- Pilih kategori --</option>
						<?php foreach ($kategori as $k): ?>
							<option value="<?= (int) $k['id'] ?>" <?= (int) $data['kategori_id'] === (int) $k['id'] ? 'selected' : '' ?>><?= e($k['nama']) ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="col-md-6 form-group">
					<label>Status</label>
					<select name="status" class="form-control">
						<option value="publish" <?= $data['status'] === 'publish' ? 'selected' : '' ?>>Publish (tampil di website)</option>
						<option value="draft" <?= $data['status'] === 'draft' ? 'selected' : '' ?>>Draft (disembunyikan)</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label>Foto (JPG/PNG/GIF, maks 2 MB)</label>
				<?php if ($row && $row['foto']): ?>
					<div><img src="<?= e(foto_url('berita', $row['foto'])) ?>" style="max-height:130px;margin-bottom:8px;"></div>
				<?php endif; ?>
				<input type="file" name="foto" accept="image/*">
				<?php if ($row): ?><p class="help-block">Kosongkan jika tidak ingin mengganti foto.</p><?php endif; ?>
			</div>
			<div class="form-group">
				<label>Isi Berita</label>
				<textarea name="isi" class="editor"><?= e($data['isi']) ?></textarea>
			</div>
		</div>
		<div class="box-footer">
			<button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Simpan</button>
			<a href="<?= e(url('admin/berita.php')) ?>" class="btn btn-default">Batal</a>
		</div>
	</form>
</div>
<?php include __DIR__ . '/../includes/admin_footer.php'; ?>
