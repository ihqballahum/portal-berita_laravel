<?php
require __DIR__ . '/includes/bootstrap.php';

$cari = isset($_GET['q']) ? trim($_GET['q']) : '';
$hal  = max(1, (int) (isset($_GET['hal']) ? $_GET['hal'] : 1));
$per  = 6;

$where  = "b.status = 'publish'";
$params = array();
if ($cari !== '') {
    $where   .= ' AND (b.judul LIKE ? OR b.isi LIKE ?)';
    $params[] = '%' . $cari . '%';
    $params[] = '%' . $cari . '%';
}
$total = (int) q("SELECT COUNT(*) FROM berita b WHERE $where", $params)->fetchColumn();
$rows  = q("SELECT b.*, k.nama AS kategori, k.slug AS kslug FROM berita b JOIN kategori k ON k.id = b.kategori_id
            WHERE $where ORDER BY b.tanggal DESC LIMIT $per OFFSET " . (($hal - 1) * $per), $params)->fetchAll();

$slider = ($cari === '' && $hal === 1) ? q('SELECT * FROM berita_foto ORDER BY tanggal DESC LIMIT 5')->fetchAll() : array();

// Cari otomatis nama file halaman galeri (tab "Berita Foto"). Kalau sudah tahu pasti, isi langsung: $link_galeri = 'namafile.php';
$link_galeri = 'index.php';
foreach (array('galeri.php', 'berita_foto.php', 'foto_berita.php', 'gallery.php', 'foto.php', 'galeri_foto.php') as $f) {
    if (is_file(__DIR__ . '/' . $f)) { $link_galeri = $f; break; }
}

$judul_halaman = $cari !== '' ? 'Hasil pencarian: ' . $cari : 'Beranda';
include __DIR__ . '/includes/public_header.php';
?>

<style>
	/* ===== Carousel: button di atas teks ===== */
	#slider .carousel-caption {
		left: 0; right: 0; bottom: 0;
		padding: 40px 20px 25px;
		background: linear-gradient(to top, rgba(0,0,0,0.75), rgba(0,0,0,0));
		text-shadow: none;
	}
	#slider .carousel-caption h4 { margin: 0 0 5px; font-size: 20px; font-weight: 600; }
	#slider .carousel-caption p  { margin: 0; font-size: 14px; }
	#slider .btn-caption {
		display: inline-block;
		margin-bottom: 12px;
		padding: 6px 22px;
		font-size: 13px;
		font-weight: 600;
		color: #fff;
		background: #3a8cbd;
		border: 2px solid #fff;
		border-radius: 25px;
		transition: all .3s;
	}
	#slider .btn-caption:hover { background: #fff; color: #3a8cbd; text-decoration: none; }

	/* ===== Kartu berita dengan background foto ===== */
	.berita-card {
		position: relative;
		height: 240px;
		margin-bottom: 20px;
		border-radius: 12px;
		overflow: hidden;
		background-color: #3a8cbd;
		background-size: cover;
		background-position: center;
		box-shadow: 0 4px 12px rgba(0,0,0,.15);
		transition: transform .3s, box-shadow .3s;
	}
	.berita-card:hover { transform: translateY(-5px); box-shadow: 0 8px 20px rgba(0,0,0,.25); }
	.berita-card .overlay {
		position: absolute; top: 0; left: 0; right: 0; bottom: 0;
		background: linear-gradient(to top, rgba(0,0,0,.85) 10%, rgba(0,0,0,.1) 70%);
		padding: 18px;
		display: flex; flex-direction: column; justify-content: flex-end;
	}
	.berita-card .label-kat {
		align-self: flex-start;
		background: #3a8cbd; color: #fff;
		font-size: 11px; font-weight: 600;
		padding: 3px 10px; border-radius: 20px; margin-bottom: 8px;
	}
	.berita-card h4 { margin: 0 0 6px; font-size: 17px; font-weight: 600; line-height: 1.3; color: #fff; }
	.berita-card .tanggal { font-size: 12px; color: #ddd; }
	.berita-card a.full-link { position: absolute; top: 0; left: 0; right: 0; bottom: 0; z-index: 5; }
</style>

<div class="row">
	<div class="col-md-8">
		<?php if ($slider): ?>
		<div id="slider" class="carousel slide kartu" data-ride="carousel" style="padding:0;">
			<div class="carousel-inner">
				<?php foreach ($slider as $i => $s): ?>
					<div class="item <?= $i === 0 ? 'active' : '' ?>">
						<img src="<?= e(foto_url('berita_foto', $s['foto'])) ?>" alt="">
						<div class="carousel-caption">
							<!-- BUTTON di atas teks -->
							<a href="<?= e(url($link_galeri)) ?>" class="btn-caption">Lihat Galeri &rarr;</a>
							<h4><?= e($s['judul']) ?></h4>
							<p><?= e($s['keterangan']) ?></p>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
			<a class="left carousel-control" href="#slider" data-slide="prev"><span class="glyphicon glyphicon-chevron-left"></span></a>
			<a class="right carousel-control" href="#slider" data-slide="next"><span class="glyphicon glyphicon-chevron-right"></span></a>
		</div>
		<?php endif; ?>

		<h3 style="margin-top:20px;"><?= $cari !== '' ? 'Hasil pencarian: ' . e($cari) : 'Berita Terbaru' ?></h3>

		<?php if ($rows): ?>
		<div class="row">
			<?php foreach ($rows as $b): ?>
			<div class="col-sm-6">
				<div class="berita-card"
					<?php if (!empty($b['foto'])): ?>style="background-image:url('<?= e(foto_url('berita', $b['foto'])) ?>');"<?php endif; ?>>
					<div class="overlay">
						<span class="label-kat"><?= e($b['kategori']) ?></span>
						<h4><?= e($b['judul']) ?></h4>
						<span class="tanggal"><?= e(date('d M Y', strtotime($b['tanggal']))) ?></span>
					</div>
					<!-- Seluruh kartu bisa diklik (ganti berita.php sesuai halaman detail kamu) -->
					<a class="full-link" href="<?= e(url('berita.php?id=' . $b['id'])) ?>"></a>
				</div>
			</div>
			<?php endforeach; ?>
		</div>
		<?php else: ?>
			<p>Belum ada berita.</p>
		<?php endif; ?>

		<?= paging($total, $hal, $per, url('index.php') . ($cari !== '' ? '?q=' . urlencode($cari) : '')) ?>
	</div>
	<div class="col-md-4"><?php include __DIR__ . '/includes/sidebar_publik.php'; ?></div>
</div>
<?php include __DIR__ . '/includes/public_footer.php'; ?>
