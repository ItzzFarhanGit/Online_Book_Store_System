<?php
/* ============================================================
   Database Connection
   Online Book Zone - Bookstore Project
   ============================================================ */

$host    = "localhost";
$dbuser  = "root";
$dbpass  = "";
$dbname  = "bookstore";

$conn = new mysqli($host, $dbuser, $dbpass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

/* ------------------------------------------------------------
   Image URL helper
   Fixes: images breaking when the project is hosted in a
   subfolder (e.g. XAMPP htdocs/bookstore/) or when called
   from a nested folder like /admin/. Always builds a path
   relative to the current script instead of an absolute
   "/Images/..." path.
   ------------------------------------------------------------ */
$__bz_script_dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
define('IMG_BASE', (basename($__bz_script_dir) === 'admin') ? '../Images/' : 'Images/');

function bz_img($path) {
    $path = trim((string) $path);
    if ($path === '') return '';
    if (preg_match('#^https?://#i', $path)) return $path;
    // normalize away any previously-stored "Images/" or "/Images/" prefix
    $path = preg_replace('#^/?Images/#i', '', $path);
    return IMG_BASE . $path;
}
?>
