<?php
require_once __DIR__ . '/includes/header.php';
require_role(['admin']);

$pdo = db();
$users = [];
$roles = [];
$error = '';

if ($pdo) {
    if (isset($_GET['delete'])) {
        $id = (int)$_GET['delete'];
        $pdo->prepare("DELETE FROM users WHERE id = :id")->execute([':id' => $id]);
        header('Location: users.php');
        exit;
    }

    $roles = $pdo->query("SELECT id, name FROM roles ORDER BY id ASC")->fetchAll();
    $users = $pdo->query("SELECT u.id, u.name, u.email, u.status, r.name AS role FROM users u LEFT JOIN roles r ON r.id = u.role_id ORDER BY u.id DESC")->fetchAll();
}
?>

<h1>Utilisateurs</h1>
<?php if ($error !== ''): ?>
  <div class="card" style="padding:12px; border-color:#b3341a;"><?php echo e($error); ?></div>
<?php endif; ?>

<a class="btn" href="add_user.php">Ajouter un utilisateur</a>

<table class="card" style="width:100%; border-collapse:collapse;">
  <thead>
    <tr>
      <th style="text-align:left; padding:8px;">Nom</th>
      <th style="text-align:left; padding:8px;">Email</th>
      <th style="text-align:left; padding:8px;">Rôle</th>
      <th style="text-align:left; padding:8px;">Statut</th>
      <th style="text-align:left; padding:8px;">Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($users as $u): ?>
      <tr>
        <td style="padding:8px;"><?php echo e($u['name']); ?></td>
        <td style="padding:8px;"><?php echo e($u['email']); ?></td>
        <td style="padding:8px;"><?php echo e($u['role']); ?></td>
        <td style="padding:8px;"><?php echo e($u['status'] ?? 'active'); ?></td>
        <td style="padding:8px;">
          <a href="edit_user.php?id=<?php echo e($u['id']); ?>">Modifier</a>
          | <a href="users.php?delete=<?php echo e($u['id']); ?>" onclick="return confirm('Supprimer cet utilisateur ?')">Supprimer</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php include __DIR__ . '/includes/footer.php';
