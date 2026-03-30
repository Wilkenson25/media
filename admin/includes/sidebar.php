<?php
$user = function_exists('admin_user') ? (admin_user() ?: []) : [];
?>
<aside class="admin-sidebar">
  <h3>Media360</h3>
  <nav>
    <a href="dashboard.php">Dashboard</a>
    <a href="articles.php">Articles</a>
    <a href="categories.php">Catégories</a>
    <a href="users.php">Utilisateurs</a>
    <a href="profile.php">Profil</a>
    <a href="about_settings.php">À propos</a>
    <a href="messages.php">Messages</a>
    <a href="settings.php">Paramètres</a>
    <a href="logout.php">Déconnexion</a>
  </nav>
  <p class="meta">Rôle: <?php echo e($user['role'] ?? ''); ?></p>
</aside>
