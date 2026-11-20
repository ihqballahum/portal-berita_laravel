<?php
// Dimuat oleh semua halaman: sesi, konfigurasi, fungsi bantu, database.
$__sp = __DIR__ . '/../storage/sessions';
if ( ! is_dir($__sp)) { @mkdir($__sp, 0777, true); }
if (is_dir($__sp) && is_writable($__sp)) { session_save_path($__sp); }

if (session_status() === PHP_SESSION_NONE) {
    session_name('portal_sess');
    session_start();
}

require __DIR__ . '/../config.php';
require __DIR__ . '/helpers.php';
require __DIR__ . '/db.php';
