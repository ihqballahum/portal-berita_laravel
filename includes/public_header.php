<?php
$judul_halaman = isset($judul_halaman) ? $judul_halaman : SITE_NAME;
$kat_menu = q('SELECT nama, slug FROM kategori ORDER BY nama')->fetchAll();
$cari = isset($_GET['q']) ? trim($_GET['q']) : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($judul_halaman) ?> | <?= e(SITE_NAME) ?></title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.4.1/css/bootstrap.min.css">
<style>
	body { background: #f1f3f6; }
	.navbar-portal { background: #3c8dbc; border: 0; border-radius: 0; margin-bottom: 0; }
	.navbar-portal .navbar-brand, .navbar-portal .navbar-nav > li > a { color: #fff; }
	.navbar-portal .navbar-nav > li > a:hover, .navbar-portal .navbar-nav > .open > a { background: #367fa9 !important; color: #fff; }
	.navbar-portal .navbar-toggle { border-color: #fff; }
	.navbar-portal .navbar-toggle .icon-bar { background: #fff; }
	.kartu { background: #fff; padding: 16px; margin-bottom: 18px; border-top: 3px solid #3c8dbc; }
	.kartu h3 { margin-bottom: 8px; }
	.kartu img.cover { width: 100%; height: 160px; object-fit: cover; }
	.meta { color: #888; font-size: 12px; margin-bottom: 8px; }
	.isi img { max-width: 100%; height: auto; }
	.galeri-item { margin-bottom: 20px; }
	.galeri-item img { width: 100%; height: 170px; object-fit: cover; }
	footer.situs { background: #222d32; color: #b8c7ce; padding: 18px 0; margin-top: 20px; }
	.carousel-inner > .item > img { width: 100%; height: 320px; object-fit: cover; }
</style>
</head>
<body>
<nav class="navbar navbar-portal">
	<div class="container">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#nav"><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span></button>
			<a class="navbar-brand" href="<?= e(url('index.php')) ?>"><b><?= e(SITE_NAME) ?></b></a>
		</div>
		<div class="collapse navbar-collapse" id="nav">
			<ul class="nav navbar-nav">
				<li><a href="<?= e(url('index.php')) ?>">Home</a></li>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">Kategori <span class="caret"></span></a>
					<ul class="dropdown-menu">
						<?php foreach ($kat_menu as $k): ?>
							<li><a href="<?= e(url('kategori.php?slug=' . urlencode($k['slug']))) ?>"><?= e($k['nama']) ?></a></li>
						<?php endforeach; ?>
					</ul>
				</li>
				<li><a href="<?= e(url('galeri.php')) ?>">Galeri</a></li>
			</ul>
			<form class="navbar-form navbar-left" action="<?= e(url('index.php')) ?>" method="get">
				<div class="form-group"><input type="text" name="q" class="form-control" placeholder="Cari berita..." value="<?= e($cari) ?>"></div>
				<button type="submit" class="btn btn-default">Cari</button>
			</form>
			<ul class="nav navbar-nav navbar-right">
				<li><a href="<?= e(url('admin/login.php')) ?>"><?= user() ? 'Dashboard' : 'Login' ?></a></li>
			</ul>
		</div>
	</div>
</nav>
<div class="container" style="margin-top:20px;">
<?php foreach (ambil_flash() as $f): ?>
	<div class="alert alert-<?= e($f[0]) ?>"><?= e($f[1]) ?></div>
<?php endforeach; ?>
