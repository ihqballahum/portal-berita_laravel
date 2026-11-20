<?php
define('ROOT_DIR', realpath(__DIR__ . '/..'));

// Deteksi otomatis folder project terhadap document root (jalan di php -S maupun Apache)
$__root = str_replace('\\', '/', ROOT_DIR);
$__doc  = isset($_SERVER['DOCUMENT_ROOT']) ? realpath($_SERVER['DOCUMENT_ROOT']) : false;
$__doc  = $__doc ? str_replace('\\', '/', $__doc) : '';
$__base = '';
if ($__doc !== '' && strncasecmp($__root, $__doc, strlen($__doc)) === 0) {
    $__base = substr($__root, strlen($__doc));
}
define('BASE_URL', rtrim($__base, '/'));

function url($path = '')
{
    return BASE_URL . '/' . ltrim($path, '/');
}

function e($teks)
{
    return htmlspecialchars((string) $teks, ENT_QUOTES, 'UTF-8');
}

function redirect($path)
{
    header('Location: ' . url($path));
    exit;
}

// ---------- Pesan flash ----------
function flash($tipe, $pesan)
{
    $_SESSION['flash'][] = array($tipe, $pesan);
}

function ambil_flash()
{
    $f = isset($_SESSION['flash']) ? $_SESSION['flash'] : array();
    unset($_SESSION['flash']);
    return $f;
}

// ---------- CSRF ----------
function csrf_token()
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf'];
}

function csrf_field()
{
    return '<input type="hidden" name="csrf" value="' . csrf_token() . '">';
}

function csrf_check()
{
    $kirim = isset($_POST['csrf']) ? $_POST['csrf'] : '';
    $asli  = isset($_SESSION['csrf']) ? $_SESSION['csrf'] : '';
    if ($asli === '' || ! hash_equals($asli, $kirim)) {
        http_response_code(419);
        exit('Token keamanan tidak valid. Muat ulang halaman lalu coba lagi.');
    }
}

// ---------- Login ----------
function user()
{
    return isset($_SESSION['user']) ? $_SESSION['user'] : null;
}

function wajib_login($levels = null)
{
    if ( ! user()) {
        redirect('admin/login.php');
    }
    if ($levels !== null && ! in_array(user()['level'], $levels, true)) {
        http_response_code(403);
        exit('Akses ditolak. Halaman ini hanya untuk: ' . e(implode(', ', $levels)) . '.');
    }
}

// ---------- Teks ----------
function slugify($teks)
{
    $t = strtolower(trim($teks));
    $t = preg_replace('/[^a-z0-9]+/', '-', $t);
    $t = trim($t, '-');
    return $t !== '' ? $t : 'item';
}

/** Slug unik untuk tabel tertentu (nama tabel hanya dari kode, bukan input user). */
function slug_unik($tabel, $teks, $id = 0)
{
    $dasar = slugify($teks);
    $slug  = $dasar;
    $i = 2;
    while (true) {
        $st = q("SELECT id FROM $tabel WHERE slug = ? AND id <> ?", array($slug, (int) $id));
        if ( ! $st->fetch()) {
            return $slug;
        }
        $slug = $dasar . '-' . $i;
        $i++;
    }
}

function tgl($tanggal, $dengan_jam = false)
{
    if ( ! $tanggal) return '-';
    $bln = array('Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des');
    $t = strtotime($tanggal);
    $s = date('d', $t) . ' ' . $bln[(int) date('n', $t) - 1] . ' ' . date('Y', $t);
    return $dengan_jam ? $s . ' ' . date('H:i', $t) : $s;
}

function ringkas($html, $kata = 30)
{
    $teks  = trim(preg_replace('/\s+/', ' ', strip_tags($html)));
    $words = explode(' ', $teks);
    if (count($words) <= $kata) return $teks;
    return implode(' ', array_slice($words, 0, $kata)) . '...';
}

/** Bersihkan HTML dari editor: hapus tag berbahaya, atribut on*, dan javascript: */
function bersihkan_html($html)
{
    $boleh = '<p><br><b><strong><i><em><u><s><ul><ol><li><a><img><h1><h2><h3><h4><h5><h6>'
           . '<blockquote><table><thead><tbody><tr><th><td><span><div><pre><code><hr><figure><figcaption>';
    $html = strip_tags($html, $boleh);
    $html = preg_replace('/\son\w+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html);
    $html = preg_replace('/(href|src)\s*=\s*(["\'])\s*javascript:[^"\']*\2/i', '$1="#"', $html);
    return $html;
}

// ---------- Gambar ----------
function foto_url($folder, $file)
{
    if ($file && is_file(ROOT_DIR . '/uploads/' . $folder . '/' . basename($file))) {
        return url('uploads/' . $folder . '/' . basename($file));
    }
    $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="400" height="260" viewBox="0 0 400 260">'
         . '<rect width="400" height="260" fill="#e3e6ea"/>'
         . '<g fill="#aab2bd"><rect x="150" y="85" width="100" height="75" rx="6"/><circle cx="178" cy="108" r="9" fill="#e3e6ea"/>'
         . '<path d="M158 152l30-30 22 22 14-14 18 22z" fill="#e3e6ea"/></g>'
         . '<text x="200" y="195" font-family="Arial" font-size="16" text-anchor="middle" fill="#8b95a1">Tidak ada foto</text></svg>';
    return 'data:image/svg+xml;utf8,' . rawurlencode($svg);
}

/** Upload gambar (jpg/png/gif, maks 2 MB). Mengembalikan nama file atau null. */
function upload_image($field, $folder, &$err)
{
    $err = '';
    if (empty($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    $f = $_FILES[$field];
    if ($f['error'] !== UPLOAD_ERR_OK) {
        $err = 'Upload gagal (kode ' . $f['error'] . ').';
        return null;
    }
    if ($f['size'] > 2 * 1024 * 1024) {
        $err = 'Ukuran gambar maksimal 2 MB.';
        return null;
    }
    $info = @getimagesize($f['tmp_name']);
    $tipe = array(IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_GIF => 'gif');
    if ( ! $info || ! isset($tipe[$info[2]])) {
        $err = 'File harus berupa gambar JPG, PNG, atau GIF.';
        return null;
    }
    $dir = ROOT_DIR . '/uploads/' . $folder;
    if ( ! is_dir($dir)) { @mkdir($dir, 0777, true); }
    $nama = bin2hex(random_bytes(8)) . '.' . $tipe[$info[2]];
    if ( ! move_uploaded_file($f['tmp_name'], $dir . '/' . $nama)) {
        $err = 'Gagal menyimpan file ke folder uploads.';
        return null;
    }
    return $nama;
}

function hapus_file($folder, $nama)
{
    if ($nama) {
        $p = ROOT_DIR . '/uploads/' . $folder . '/' . basename($nama);
        if (is_file($p)) { @unlink($p); }
    }
}

// ---------- Tampilan website publik ----------
function paging($total, $hal, $per, $base)
{
    $jml = max(1, (int) ceil($total / $per));
    if ($jml <= 1) return '';
    $sep = (strpos($base, '?') === false) ? '?' : '&';
    $h = '<ul class="pagination">';
    for ($i = 1; $i <= $jml; $i++) {
        $h .= '<li' . ($i == $hal ? ' class="active"' : '') . '><a href="' . e($base . $sep . 'hal=' . $i) . '">' . $i . '</a></li>';
    }
    return $h . '</ul>';
}

function daftar_berita($rows)
{
    if (empty($rows)) {
        echo '<div class="kartu text-muted">Belum ada berita.</div>';
        return;
    }
    foreach ($rows as $b) {
        $link = url('berita.php?slug=' . urlencode($b['slug']));
        echo '<div class="kartu"><div class="row">';
        echo '<div class="col-sm-4"><a href="' . e($link) . '"><img class="cover" src="' . e(foto_url('berita', $b['foto'])) . '" alt=""></a></div>';
        echo '<div class="col-sm-8">';
        echo '<h3 style="margin-top:0;"><a href="' . e($link) . '">' . e($b['judul']) . '</a></h3>';
        echo '<div class="meta"><a href="' . e(url('kategori.php?slug=' . urlencode($b['kslug']))) . '">' . e($b['kategori']) . '</a> &middot; ' . e(tgl($b['tanggal'])) . '</div>';
        echo '<p>' . e(ringkas($b['isi'], 28)) . '</p>';
        echo '<a href="' . e($link) . '" class="btn btn-primary btn-sm">Baca selengkapnya</a>';
        echo '</div></div></div>';
    }
}
