<?php
require_once __DIR__ . '/../../config/db.php';

if (session_status() === PHP_SESSION_NONE) {
    @ini_set('session.save_path', sys_get_temp_dir());
    session_start();
}

function e($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

if (!function_exists('admin_user')) {
    function admin_user()
    {
        return $_SESSION['admin_user'] ?? null;
    }
}

if (!function_exists('require_login')) {
    function require_login()
    {
        return true;
    }
}

if (!function_exists('require_role')) {
    function require_role(array $roles)
    {
        return true;
    }
}

$user = admin_user() ?: [];
?><!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Admin | Media360</title>
  <link rel="stylesheet" href="<?php echo e(base_url('assets/styles.css')); ?>" />
  <style>
    .admin-layout { display: grid; grid-template-columns: 240px 1fr; min-height: 100vh; }
    .admin-sidebar { background: #1c1c1c; color: #fff; padding: 16px; }
    .admin-sidebar a { color: #fff; display: block; padding: 8px 0; }
    .admin-top { display: flex; justify-content: space-between; align-items: center; padding: 14px 0; }
    .admin-content { padding: 0 0 24px; }
    @media (max-width: 900px) { .admin-layout { grid-template-columns: 1fr; } }
  </style>
</head>
<body>
<div class="admin-layout">
  <?php include __DIR__ . '/sidebar.php'; ?>
  <main class="admin-content">
    <div class="container">
      <div class="admin-top">
        <strong>Admin Panel</strong>
        <span class="meta"><?php echo e($user['name'] ?? $user['email'] ?? ''); ?></span>
      </div>
