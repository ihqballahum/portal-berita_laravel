<?php
function db()
{
    static $pdo = null;
    if ($pdo === null) {
        try {
            $pdo = new PDO(
                'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
                DB_USER, DB_PASS,
                array(
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                )
            );
        } catch (PDOException $ex) {
            http_response_code(500);
            exit('<h3>Koneksi database gagal</h3><p>' . htmlspecialchars($ex->getMessage()) .
                 '</p><p>Pastikan MySQL di Laragon menyala, database <b>' . htmlspecialchars(DB_NAME) .
                 '</b> sudah di-import, dan cek pengaturan di <code>config.php</code>. ' .
                 'Buka <a href="cek.php">cek.php</a> untuk diagnosa.</p>');
        }
    }
    return $pdo;
}

/** Jalankan query dengan prepared statement. */
function q($sql, $params = array())
{
    $st = db()->prepare($sql);
    $st->execute($params);
    return $st;
}