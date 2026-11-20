<?php
require __DIR__ . '/../includes/bootstrap.php';
wajib_login(array('admin'));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $aksi = isset($_POST['aksi']) ? $_POST['aksi'] : '';
    $id   = (int) (isset($_POST['id']) ? $_POST['id'] : 0);

    if ($aksi === 'simpan') {
        $nama  = trim(isset($_POST['nama']) ? $_POST['nama'] : '');
        $uname = trim(isset($_POST['username']) ? $_POST['username'] : '');
        $email = trim(isset($_POST['email']) ? $_POST['email'] : '');
        $level = (isset($_POST['level']) && $_POST['level'] === 'admin') ? 'admin' : 'editor';
        $aktif = isset($_POST['aktif']) ? 1 : 0;
        $pass  = (string) (isset($_POST['password']) ? $_POST['password'] : '');

        $dobel = (int) q('SELECT COUNT(*) FROM users WHERE username = ? AND id <> ?', array($uname, $id))->fetchColumn();

        if ($nama === '' || $uname === '') {
            flash('danger', 'Nama dan username wajib diisi.');
        } elseif ($dobel > 0) {
            flash('danger', 'Username sudah dipakai.');
        } elseif ( ! $id && strlen($pass) < 6) {
            flash('danger', 'Password minimal 6 karakter.');
        } elseif ($id && $pass !== '' && strlen($pass) < 6) {
            flash('danger', 'Password baru minimal 6 karakter.');
        } else {
            if ($id === user()['id']) { $aktif = 1; $level = 'admin'; }   // jangan kunci akun sendiri
            if ($id) {
                q('UPDATE users SET nama=?, username=?, email=?, level=?, aktif=? WHERE id=?', array($nama, $uname, $email, $level, $aktif, $id));
                if ($pass !== '') {
                    q('UPDATE users SET password=? WHERE id=?', array(password_hash($pass, PASSWORD_DEFAULT), $id));
                }
                if ($id === user()['id']) { $_SESSION['user']['nama'] = $nama; $_SESSION['user']['username'] = $uname; }
                flash('success', 'User berhasil diperbarui.');
            } else {
                q('INSERT INTO users (nama, username, email, password, level, aktif) VALUES (?,?,?,?,?,?)',
                  array($nama, $uname, $email, password_hash($pass, PASSWORD_DEFAULT), $level, $aktif));
                flash('success', 'User berhasil ditambahkan.');
            }
        }
    } elseif ($aksi === 'hapus') {
        if ($id === user()['id']) {
            flash('danger', 'Anda tidak bisa menghapus akun sendiri.');
        } else {
            q('DELETE FROM users WHERE id = ?', array($id));   // berita tetap ada (penulis menjadi kosong)
            flash('success', 'User berhasil dihapus.');
        }
    }
    redirect('admin/user.php');
}

$edit = null;
if (isset($_GET['edit'])) {
    $edit = q('SELECT * FROM users WHERE id = ?', array((int) $_GET['edit']))->fetch();
}
$rows = q('SELECT * FROM users ORDER BY nama')->fetchAll();

$judul = 'Manajemen User'; $sub = 'Kelola pengguna dashboard'; $aktif = 'user';
include __DIR__ . '/../includes/admin_header.php';
?>
<div class="row">
	<div class="col-md-4">
		<div class="box box-primary">
			<div class="box-header with-border"><h3 class="box-title"><?= $edit ? 'Edit User' : 'Tambah User' ?></h3></div>
			<form method="post">
				<?= csrf_field() ?>
				<input type="hidden" name="aksi" value="simpan">
				<input type="hidden" name="id" value="<?= $edit ? (int) $edit['id'] : 0 ?>">
				<div class="box-body">
					<div class="form-group"><label>Nama Lengkap</label><input type="text" name="nama" class="form-control" value="<?= $edit ? e($edit['nama']) : '' ?>" required></div>
					<div class="form-group"><label>Username</label><input type="text" name="username" class="form-control" value="<?= $edit ? e($edit['username']) : '' ?>" required></div>
					<div class="form-group"><label>Email</label><input type="email" name="email" class="form-control" value="<?= $edit ? e($edit['email']) : '' ?>"></div>
					<div class="form-group">
						<label>Level</label>
						<select name="level" class="form-control">
							<option value="editor" <?= ($edit && $edit['level'] === 'editor') ? 'selected' : '' ?>>Editor (berita, komentar, galeri)</option>
							<option value="admin" <?= ($edit && $edit['level'] === 'admin') ? 'selected' : '' ?>>Admin (akses penuh)</option>
						</select>
					</div>
					<div class="form-group">
						<label>Password <?= $edit ? '(kosongkan jika tidak diganti)' : '' ?></label>
						<input type="password" name="password" class="form-control" autocomplete="new-password" <?= $edit ? '' : 'required' ?>>
					</div>
					<div class="checkbox"><label><input type="checkbox" name="aktif" value="1" <?= ( ! $edit || $edit['aktif']) ? 'checked' : '' ?>> Akun aktif</label></div>
				</div>
				<div class="box-footer">
					<button class="btn btn-primary"><i class="fa fa-save"></i> Simpan</button>
					<?php if ($edit): ?><a href="<?= e(url('admin/user.php')) ?>" class="btn btn-default">Batal</a><?php endif; ?>
				</div>
			</form>
		</div>
	</div>
	<div class="col-md-8">
		<div class="box box-primary">
			<div class="box-header with-border"><h3 class="box-title">Data User</h3></div>
			<div class="box-body no-padding">
				<table class="table table-striped">
					<thead><tr><th width="50">No.</th><th>Nama</th><th>Username</th><th>Level</th><th>Status</th><th width="90">Pilihan</th></tr></thead>
					<tbody>
					<?php $no = 1; foreach ($rows as $u): ?>
						<tr>
							<td><?= $no++ ?></td>
							<td><?= e($u['nama']) ?><br><small class="text-muted"><?= e($u['email']) ?></small></td>
							<td><?= e($u['username']) ?></td>
							<td><span class="label label-<?= $u['level'] === 'admin' ? 'danger' : 'info' ?>"><?= e($u['level']) ?></span></td>
							<td><span class="label label-<?= $u['aktif'] ? 'success' : 'default' ?>"><?= $u['aktif'] ? 'Aktif' : 'Nonaktif' ?></span></td>
							<td>
								<a href="<?= e(url('admin/user.php?edit=' . $u['id'])) ?>" class="btn btn-info btn-xs" title="Edit"><i class="fa fa-edit"></i></a>
								<form method="post" class="form-inline-del" onsubmit="return confirm('Hapus user ini?')">
									<?= csrf_field() ?>
									<input type="hidden" name="aksi" value="hapus">
									<input type="hidden" name="id" value="<?= (int) $u['id'] ?>">
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
