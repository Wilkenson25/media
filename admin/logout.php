<?php
require_once __DIR__ . '/../config/db.php';

if (session_status() === PHP_SESSION_NONE) {
    @ini_set('session.save_path', sys_get_temp_dir());
    session_start();
}

$_SESSION = [];
if (session_status() === PHP_SESSION_ACTIVE) {
    session_destroy();
}
header('Location: ' . base_url('login.php'));
exit;
