<?php
require __DIR__ . '/includes/bootstrap.php';
header('Content-Type: text/plain; charset=utf-8');

echo "DB_NAME di config.php : " . DB_NAME . "\n";
$d = q('SELECT DATABASE() AS d')->fetch();
echo "Database yang terkoneksi: " . $d['d'] . "\n\n";

$rows = q('SELECT id, username, aktif, LENGTH(password) AS panjang, LEFT(password,7) AS awal FROM users')->fetchAll();
echo "Jumlah user: " . count($rows) . "\n";
foreach ($rows as $r) {
    echo "- [" . $r['username'] . "] aktif=" . $r['aktif'] .
         " panjang_hash=" . $r['panjang'] . " awal=" . $r['awal'] . "\n";
}

echo "\nReset password + verifikasi:\n";
$akun = array('admin' => 'test123', 'editor' => 'test456');
foreach ($akun as $un => $pw) {
    q('UPDATE users SET password = ?, aktif = 1 WHERE username = ?',
      array(password_hash($pw, PASSWORD_DEFAULT), $un));
    $u = q('SELECT * FROM users WHERE username = ? AND aktif = 1', array($un))->fetch();
    if (!$u) {
        echo "- " . $un . ": user TIDAK DITEMUKAN\n";
    } else {
        echo "- " . $un . ": password_verify = " .
             (password_verify($pw, $u['password']) ? 'OK' : 'GAGAL') .
             " (password: " . $pw . ")\n";
    }
}