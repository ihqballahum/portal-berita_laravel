<?php
require __DIR__ . '/includes/bootstrap.php';

// Mendukung dua cara: berita.php?slug=... atau berita.php?id=...
$slug = isset($_GET['slug']) ? $_GET['slug'] : '';
$id   = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($slug !== '') {
    $kondisi = 'b.slug = ?';
    $param   = array($slug);
} else {
    $kondisi = 'b.id = ?';
    $param   = array($id);
}

$b = q("SELECT b.*, k.nama AS kategori, k.slug AS kslug, u.nama AS penulis
        FROM berita b JOIN kategori k ON k.id = b.kategori_id LEFT JOIN users u ON u.id = b.penulis_id
        WHERE $kondisi AND b.status = 'publish'", $param)->fetch();

if ( ! $b) {
    http_response_code(404);
    $judul_halaman = 'Tidak ditemukan';
    include __DIR__ . '/includes/public_header.php';
    echo '<div class="kartu"><h3>Berita tidak ditemukan</h3><a href="' . e(url('index.php')) . '">Kembali ke beranda</a></div>';
    include __DIR__ . '/includes/public_footer.php';
    exit;
}

// Kirim komentar (masuk status "menunggu persetujuan")
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $nama  = trim(isset($_POST['nama']) ? $_POST['nama'] : '');
    $email = trim(isset($_POST['email']) ? $_POST['email'] : '');
    $isi   = trim(isset($_POST['isi']) ? $_POST['isi'] : '');
    $jebak = isset($_POST['website']) ? $_POST['website'] : '';   // kolom tersembunyi anti-spam

    if ($jebak !== '') {
        // bot: abaikan diam-diam
    } elseif ($nama === '' || $isi === '' || mb_strlen($isi) > 1000) {
        flash('danger', 'Nama dan komentar wajib diisi (maksimal 1000 karakter).');
    } elseif ($email !== '' && ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
        flash('danger', 'Format email tidak valid.');
    } else {
        q("INSERT INTO komentar (berita_id, nama, email, isi, status, tanggal) VALUES (?,?,?,?, 'pending', NOW())",
          array($b['id'], mb_substr($nama, 0, 100), $email !== '' ? $email : null, $isi));
        flash('success', 'Terima kasih! Komentar Anda akan tampil setelah disetujui admin.');
    }
    // Kembali ke halaman yang sama (pakai id agar selalu valid)
    redirect('berita.php?id=' . (int) $b['id'] . '#komentar');
}

q('UPDATE berita SET dilihat = dilihat + 1 WHERE id = ?', array($b['id']));
$komentar = q("SELECT * FROM komentar WHERE berita_id = ? AND status = 'approved' ORDER BY tanggal ASC", array($b['id']))->fetchAll();

$judul_halaman = $b['judul'];
include __DIR__ . '/includes/public_header.php';
?>
<div class="row">
	<div class="col-md-8">
		<div class="kartu">
			<h2 style="margin-top:0;"><?= e($b['judul']) ?></h2>
			<div class="meta">
				<a href="<?= e(url('kategori.php?slug=' . urlencode($b['kslug']))) ?>"><?= e($b['kategori']) ?></a>
				&middot; <?= e(tgl($b['tanggal'], true)) ?>
				<?php if ($b['penulis']): ?>&middot; <?= e($b['penulis']) ?><?php endif; ?>
				&middot; <?= (int) $b['dilihat'] + 1 ?>x dilihat
			</div>
			<?php if ($b['foto']): ?><img src="<?= e(foto_url('berita', $b['foto'])) ?>" style="width:100%;max-height:360px;object-fit:cover;margin-bottom:14px;" alt=""><?php endif; ?>
			<div class="isi"><?= $b['isi'] ?></div>
		</div>

		<div class="kartu" id="komentar">
			<h3 style="margin-top:0;">Komentar (<?= count($komentar) ?>)</h3>
			<?php foreach ($komentar as $c): ?>
				<div style="border-bottom:1px solid #eee;padding:8px 0;">
					<b><?= e($c['nama']) ?></b> <small class="text-muted"><?= e(tgl($c['tanggal'], true)) ?></small>
					<p style="margin:4px 0 0;"><?= nl2br(e($c['isi'])) ?></p>
				</div>
			<?php endforeach; ?>
			<?php if (empty($komentar)): ?><p class="text-muted">Belum ada komentar.</p><?php endif; ?>

			<h4 style="margin-top:20px;">Tulis komentar</h4>
			<form method="post" action="">
				<?= csrf_field() ?>
				<div style="display:none;"><input type="text" name="website" tabindex="-1" autocomplete="off"></div>
				<div class="row">
					<div class="col-sm-6 form-group"><input type="text" name="nama" class="form-control" placeholder="Nama" maxlength="100" required></div>
					<div class="col-sm-6 form-group"><input type="email" name="email" class="form-control" placeholder="Email (opsional)"></div>
				</div>
				<div class="form-group"><textarea name="isi" class="form-control" rows="4" placeholder="Komentar Anda" maxlength="1000" required></textarea></div>
				<button class="btn btn-primary">Kirim Komentar</button>
			</form>
		</div>
	</div>
	<div class="col-md-4"><?php include __DIR__ . '/includes/sidebar_publik.php'; ?></div>
</div>
<?php include __DIR__ . '/includes/public_footer.php'; ?>