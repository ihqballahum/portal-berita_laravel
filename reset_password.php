<?php
require __DIR__ . '/includes/bootstrap.php';
header('Content-Type: text/plain; charset=utf-8');

echo "Database yang dipakai website: " . DB_NAME . "\n\n";

$akun = array('admin' => 'test123', 'editor' => 'test456');

foreach ($akun as $un => $pw) {
    q('UPDATE users SET password = ?, aktif = 1 WHERE username = ?',
      array(password_hash($pw, PASSWORD_DEFAULT), $un));

    $u = q('SELECT password FROM users WHERE username = ?', array($un))->fetch();

    if (!$u) {
        echo $un . " -> user TIDAK ADA di tabel users\n";
    } elseif (password_verify($pw, $u['password'])) {
        echo $un . " -> OK, login dengan password: " . $pw . "\n";
    } else {
        echo $un . " -> GAGAL verifikasi\n";
    }
}