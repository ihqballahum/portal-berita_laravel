<?php
$u      = user();
$judul  = isset($judul) ? $judul : 'Dashboard';
$sub    = isset($sub) ? $sub : '';
$aktif  = isset($aktif) ? $aktif : '';
$pakai  = isset($pakai) ? $pakai : array();
$on     = function ($k) use ($aktif) { return $aktif === $k ? 'active' : ''; };
$grup   = function ($ks) use ($aktif) { return in_array($aktif, $ks, true) ? 'active menu-open' : ''; };
$buka   = function ($ks) use ($aktif) { return in_array($aktif, $ks, true) ? 'menu-open' : ''; };
$pending = (int) q("SELECT COUNT(*) FROM komentar WHERE status = 'pending'")->fetchColumn();
$avatar  = foto_url('', '');
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($judul) ?> | <?= e(SITE_NAME) ?></title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.4.1/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/2.4.18/css/AdminLTE.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/2.4.18/css/skins/skin-blue.min.css">
<?php if (in_array('datatables', $pakai)): ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/css/dataTables.bootstrap.min.css">
<?php endif; ?>
<?php if (in_array('summernote', $pakai)): ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote.min.css">
<?php endif; ?>
<style>
	.table > tbody > tr > td { vertical-align: middle; }
	.thumb { width: 100px; height: 68px; object-fit: cover; border-radius: 2px; }
	.form-inline-del { display: inline; margin: 0; }
	.content-wrapper { background: #ecf0f5; }
	.box.box-primary { border-top-color: #3c8dbc; }
</style>
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

	<header class="main-header">
		<a href="<?= e(url('admin/index.php')) ?>" class="logo">
			<span class="logo-mini"><b>PB</b></span>
			<span class="logo-lg"><b><?= e(SITE_NAME) ?></b></span>
		</a>
		<nav class="navbar navbar-static-top">
			<a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button"><span class="sr-only">Toggle</span></a>
			<div class="navbar-custom-menu">
				<ul class="nav navbar-nav">
					<li><a href="<?= e(url('index.php')) ?>" target="_blank"><i class="fa fa-globe"></i> Lihat Website</a></li>
					<li><a href="#"><i class="fa fa-user"></i> <?= e($u['nama']) ?> (<?= e($u['level']) ?>)</a></li>
					<li><a href="<?= e(url('admin/logout.php')) ?>"><i class="fa fa-sign-out"></i> Keluar</a></li>
				</ul>
			</div>
		</nav>
	</header>

	<aside class="main-sidebar">
		<section class="sidebar">
			<form class="sidebar-form" onsubmit="return false;">
				<div class="input-group">
					<input type="text" id="cari-menu" class="form-control" placeholder="Search...">
					<span class="input-group-btn"><button type="button" class="btn btn-flat"><i class="fa fa-search"></i></button></span>
				</div>
			</form>

			<ul class="sidebar-menu" data-widget="tree">
				<li class="header">MAIN NAVIGATION</li>

				<li class="menu-item <?= $on('dash') ?>"><a href="<?= e(url('admin/index.php')) ?>"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>

				<li class="menu-item treeview <?= $grup(array('b_data', 'b_tambah')) ?>">
					<a href="#"><i class="fa fa-newspaper-o"></i> <span>Berita</span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
					<ul class="treeview-menu <?= $buka(array('b_data', 'b_tambah')) ?>">
						<li class="<?= $on('b_data') ?>"><a href="<?= e(url('admin/berita.php')) ?>"><i class="fa fa-circle-o"></i> Data Berita</a></li>
						<li class="<?= $on('b_tambah') ?>"><a href="<?= e(url('admin/berita_form.php')) ?>"><i class="fa fa-circle-o"></i> Tambah Berita</a></li>
					</ul>
				</li>

				<li class="menu-item treeview <?= $grup(array('k_semua', 'k_pending')) ?>">
					<a href="#"><i class="fa fa-comments-o"></i> <span>Komentar</span>
						<span class="pull-right-container"><?php if ($pending): ?><span class="label label-warning"><?= $pending ?></span> <?php endif; ?><i class="fa fa-angle-left pull-right"></i></span></a>
					<ul class="treeview-menu <?= $buka(array('k_semua', 'k_pending')) ?>">
						<li class="<?= $on('k_semua') ?>"><a href="<?= e(url('admin/komentar.php')) ?>"><i class="fa fa-circle-o"></i> Semua Komentar</a></li>
						<li class="<?= $on('k_pending') ?>"><a href="<?= e(url('admin/komentar.php?status=pending')) ?>"><i class="fa fa-circle-o"></i> Menunggu Persetujuan</a></li>
					</ul>
				</li>

				<li class="menu-item <?= $on('bf') ?>"><a href="<?= e(url('admin/berita_foto.php')) ?>"><i class="fa fa-picture-o"></i> <span>Berita Foto</span></a></li>

				<li class="menu-item treeview <?= $grup(array('g_album', 'g_foto')) ?>">
					<a href="#"><i class="fa fa-image"></i> <span>Galeri Foto</span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
					<ul class="treeview-menu <?= $buka(array('g_album', 'g_foto')) ?>">
						<li class="<?= $on('g_album') ?>"><a href="<?= e(url('admin/galeri_album.php')) ?>"><i class="fa fa-circle-o"></i> Album</a></li>
						<li class="<?= $on('g_foto') ?>"><a href="<?= e(url('admin/galeri_foto.php')) ?>"><i class="fa fa-circle-o"></i> Foto</a></li>
					</ul>
				</li>

				<li class="menu-item <?= $on('kat') ?>"><a href="<?= e(url('admin/kategori.php')) ?>"><i class="fa fa-th-list"></i> <span>Kategori</span></a></li>

				<?php if ($u['level'] === 'admin'): ?>
				<li class="menu-item <?= $on('user') ?>"><a href="<?= e(url('admin/user.php')) ?>"><i class="fa fa-user"></i> <span>Manajemen User</span></a></li>
				<?php endif; ?>
			</ul>
		</section>
	</aside>

	<div class="content-wrapper">
		<section class="content-header">
			<h1><?= e($judul) ?> <small><?= e($sub) ?></small></h1>
		</section>
		<section class="content">
			<?php foreach (ambil_flash() as $f): ?>
				<div class="alert alert-<?= e($f[0]) ?> alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button><?= e($f[1]) ?></div>
			<?php endforeach; ?>
