<?php
require __DIR__ . '/../includes/bootstrap.php';

if (user()) { redirect('admin/index.php'); }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $un = trim(isset($_POST['username']) ? $_POST['username'] : '');
    $pw = (string) (isset($_POST['password']) ? $_POST['password'] : '');

    $u = q('SELECT * FROM users WHERE username = ? AND aktif = 1', array($un))->fetch();
    if ($u && password_verify($pw, $u['password'])) {
        session_regenerate_id(true);
        $_SESSION['user'] = array(
            'id' => (int) $u['id'], 'nama' => $u['nama'],
            'username' => $u['username'], 'level' => $u['level']
        );
        redirect('admin/index.php');
    }
    $error = 'Username atau password salah.';
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login | <?= e(SITE_NAME) ?></title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.4.1/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/2.4.18/css/AdminLTE.min.css">
<style>
	/* Background foto + lapisan biru gelap */
	body.login-page {
		background:
			linear-gradient(rgba(15, 35, 75, 0.70), rgba(15, 35, 75, 0.70)),
			url('../assest/bg-login..jpg') center / cover no-repeat fixed !important;
		min-height: 100vh;
	}

	/* Judul di atas kartu login */
	.login-logo a {
		color: #fff !important;
		text-shadow: 0 2px 8px rgba(0, 0, 0, .5);
	}

	/* Kartu login */
	.login-box-body {
		background: rgba(255, 255, 255, 0.95);
		box-shadow: 0 10px 30px rgba(0, 0, 0, .35);
		border-radius: 4px;
	}
</style>
</head>
<body class="hold-transition login-page">
<div class="login-box">
	<div class="login-logo"><a href="<?= e(url('index.php')) ?>"><b><?= e(SITE_NAME) ?></b></a></div>
	<div class="login-box-body">
		<p class="login-box-msg">Masuk ke dashboard</p>
		<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
		<form method="post" action="">
			<?= csrf_field() ?>
			<div class="form-group has-feedback">
				<input type="text" name="username" class="form-control" placeholder="Username" required autofocus>
				<span class="fa fa-user form-control-feedback"></span>
			</div>
			<div class="form-group has-feedback">
				<input type="password" name="password" class="form-control" placeholder="Password" required>
				<span class="fa fa-lock form-control-feedback"></span>
			</div>
			<button type="submit" class="btn btn-primary btn-block btn-flat">MASUK</button>
		</form>
	</div>
</div>
</body>
</html>