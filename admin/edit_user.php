<?php
require_once __DIR__ . '/includes/header.php';
require_role(['admin']);

$pdo = db();
$roles = [];
$error = '';
$id = (int)($_GET['id'] ?? 0);
$user = null;

if ($pdo) {
    $roles = $pdo->query("SELECT id, name FROM roles ORDER BY id ASC")->fetchAll();
    $stmt = $pdo->prepare("SELECT id, name, email, role_id, status FROM users WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $id]);
    $user = $stmt->fetch();
}

if (!$user) {
    echo 'Utilisateur introuvable.';
    include __DIR__ . '/includes/footer.php';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role_id = (int)($_POST['role_id'] ?? 0);
    $status = $_POST['status'] ?? 'active';
    $password = $_POST['password'] ?? '';

    if ($name === '' || $email === '' || $role_id === 0) {
        $error = 'Nom, email et rôle obligatoires.';
    } else {
        if ($password !== '') {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET name = :name, email = :email, role_id = :role_id, status = :status, password = :password WHERE id = :id");
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':role_id' => $role_id,
                ':status' => $status,
                ':password' => $hash,
                ':id' => $id,
            ]);
        } else {
            $stmt = $pdo->prepare("UPDATE users SET name = :name, email = :email, role_id = :role_id, status = :status WHERE id = :id");
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':role_id' => $role_id,
                ':status' => $status,
                ':id' => $id,
            ]);
        }
        header('Location: users.php');
        exit;
    }
}
?>

<h1>Modifier un utilisateur</h1>
<?php if ($error !== ''): ?>
  <div class="card" style="padding:12px; border-color:#b3341a;"><?php echo e($error); ?></div>
<?php endif; ?>

<form method="post" class="card" style="padding:16px;">
  <label class="meta">Nom</label>
  <input type="text" name="name" value="<?php echo e($user['name']); ?>" style="width:100%; padding:8px; margin:6px 0 10px;" />

  <label class="meta">Email</label>
  <input type="email" name="email" value="<?php echo e($user['email']); ?>" style="width:100%; padding:8px; margin:6px 0 10px;" />

  <label class="meta">Nouveau mot de passe (optionnel)</label>
  <input type="password" name="password" style="width:100%; padding:8px; margin:6px 0 10px;" />

  <label class="meta">Rôle</label>
  <select name="role_id" style="width:100%; padding:8px; margin:6px 0 10px;">
    <?php foreach ($roles as $r): ?>
      <option value="<?php echo e($r['id']); ?>" <?php echo ((int)$user['role_id'] === (int)$r['id']) ? 'selected' : ''; ?>><?php echo e($r['name']); ?></option>
    <?php endforeach; ?>
  </select>

  <label class="meta">Statut</label>
  <select name="status" style="width:100%; padding:8px; margin:6px 0 10px;">
    <option value="active" <?php echo ($user['status'] === 'active') ? 'selected' : ''; ?>>Actif</option>
    <option value="inactive" <?php echo ($user['status'] === 'inactive') ? 'selected' : ''; ?>>Inactif</option>
  </select>

  <button class="btn" type="submit">Mettre à jour</button>
</form>

<?php include __DIR__ . '/includes/footer.php';
