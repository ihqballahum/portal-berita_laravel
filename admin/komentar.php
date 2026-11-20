<?php
require __DIR__ . '/../includes/bootstrap.php';
wajib_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $id   = (int) (isset($_POST['id']) ? $_POST['id'] : 0);
    $aksi = isset($_POST['aksi']) ? $_POST['aksi'] : '';
    if ($aksi === 'setujui') {
        q("UPDATE komentar SET status = 'approved' WHERE id = ?", array($id));
        flash('success', 'Komentar disetujui dan tampil di website.');
    } elseif ($aksi === 'sembunyikan') {
        q("UPDATE komentar SET status = 'pending' WHERE id = ?", array($id));
        flash('success', 'Komentar disembunyikan dari website.');
    } elseif ($aksi === 'hapus') {
        q('DELETE FROM komentar WHERE id = ?', array($id));
        flash('success', 'Komentar berhasil dihapus.');
    }
    redirect('admin/komentar.php' . (isset($_POST['kembali']) && $_POST['kembali'] === 'pending' ? '?status=pending' : ''));
}

$filter = (isset($_GET['status']) && $_GET['status'] === 'pending') ? 'pending' : '';
$sql = 'SELECT c.*, b.judul AS berita FROM komentar c JOIN berita b ON b.id = c.berita_id';
if ($filter) { $sql .= " WHERE c.status = 'pending'"; }
$rows = q($sql . ' ORDER BY c.tanggal DESC')->fetchAll();

$judul = 'Komentar'; $sub = $filter ? 'Menunggu persetujuan' : 'Semua komentar';
$aktif = $filter ? 'k_pending' : 'k_semua'; $pakai = array('datatables');
include __DIR__ . '/../includes/admin_header.php';
?>
<div class="box box-primary">
	<div class="box-body">
		<table class="table table-bordered table-striped tabel">
			<thead><tr><th width="50">No.</th><th>Tanggal</th><th>Berita</th><th>Nama</th><th>Komentar</th><th>Status</th><th class="no-sort" width="120">Pilihan</th></tr></thead>
			<tbody>
			<?php $no = 1; foreach ($rows as $c): ?>
				<tr>
					<td><?= $no++ ?></td>
					<td><?= e(tgl($c['tanggal'], true)) ?></td>
					<td><?= e($c['berita']) ?></td>
					<td><?= e($c['nama']) ?><?php if ($c['email']): ?><br><small class="text-muted"><?= e($c['email']) ?></small><?php endif; ?></td>
					<td><?= nl2br(e($c['isi'])) ?></td>
					<td><span class="label label-<?= $c['status'] === 'approved' ? 'success' : 'warning' ?>"><?= $c['status'] === 'approved' ? 'Tampil' : 'Menunggu' ?></span></td>
					<td>
						<form method="post" class="form-inline-del">
							<?= csrf_field() ?>
							<input type="hidden" name="id" value="<?= (int) $c['id'] ?>">
							<input type="hidden" name="kembali" value="<?= e($filter) ?>">
							<?php if ($c['status'] === 'pending'): ?>
								<button name="aksi" value="setujui" class="btn btn-success btn-xs" title="Setujui"><i class="fa fa-check"></i></button>
							<?php else: ?>
								<button name="aksi" value="sembunyikan" class="btn btn-default btn-xs" title="Sembunyikan"><i class="fa fa-eye-slash"></i></button>
							<?php endif; ?>
							<button name="aksi" value="hapus" class="btn btn-danger btn-xs" title="Hapus" onclick="return confirm('Hapus komentar ini?')"><i class="fa fa-trash"></i></button>
						</form>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>
<?php include __DIR__ . '/../includes/admin_footer.php'; ?>
