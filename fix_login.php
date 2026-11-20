<?php
require __DIR__ . '/includes/bootstrap.php';
header('Content-Type: text/plain; charset=utf-8');

$akun = array('admin' => 'Admin2026', 'editor' => 'Editor2026');
echo "Database: " . DB_NAME . "\n";
foreach ($akun as $un => $pw) {
    q('UPDATE users SET password = ?, aktif = 1 WHERE username = ?',
      array(password_hash($pw, PASSWORD_DEFAULT), $un));
    $u = q('SELECT * FROM users WHERE username = ? AND aktif = 1', array($un))->fetch();
    echo $un . ": " . ($u && password_verify($pw, $u['password']) ? 'verifikasi OK, password = ' . $pw : 'GAGAL') . "\n";
}