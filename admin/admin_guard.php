<?php
/* ============================================================
   admin/admin_guard.php
   Include at the top of every admin page to enforce admin login
   ============================================================ */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin_login.php");
    exit();
}
?>
