<?php
require_once __DIR__ . '/includes/header.php';
require_role(['admin']);

$pdo = db();
$roles = [];
$error = '';

if ($pdo) {
    $roles = $pdo->query("SELECT id, name FROM roles ORDER BY id ASC")->fetchAll();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role_id = (int)($_POST['role_id'] ?? 0);

    if ($name === '' || $email === '' || $password === '' || $role_id === 0) {
        $error = 'Tous les champs sont obligatoires.';
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role_id, status) VALUES (:name, :email, :password, :role_id, 'active')");
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':password' => $hash,
                ':role_id' => $role_id,
            ]);
            $userId = (int)$pdo->lastInsertId();

            // Insert minimal equipe profile
            $roleName = 'Membre';
            $stmt = $pdo->prepare("SELECT name FROM roles WHERE id = :id LIMIT 1");
            $stmt->execute([':id' => $role_id]);
            $roleRow = $stmt->fetch();
            if (!empty($roleRow['name'])) {
                $roleName = $roleRow['name'];
            }
            $stmt = $pdo->prepare("INSERT INTO equipe (user_id, name, role) VALUES (:user_id, :name, :role)");
            $stmt->execute([
                ':user_id' => $userId,
                ':name' => $name,
                ':role' => $roleName,
            ]);

            $pdo->commit();
        } catch (Throwable $e) {
            $pdo->rollBack();
            $error = 'Erreur lors de la création.';
        }
        if ($error === '') {
            header('Location: users.php');
            exit;
        }
    }
}
?>

<h1>Ajouter un utilisateur</h1>
<?php if ($error !== ''): ?>
  <div class="card" style="padding:12px; border-color:#b3341a;"><?php echo e($error); ?></div>
<?php endif; ?>

<form method="post" class="card" style="padding:16px;">
  <label class="meta">Nom</label>
  <input type="text" name="name" style="width:100%; padding:8px; margin:6px 0 10px;" />

  <label class="meta">Email</label>
  <input type="email" name="email" style="width:100%; padding:8px; margin:6px 0 10px;" />

  <label class="meta">Mot de passe</label>
  <input type="password" name="password" style="width:100%; padding:8px; margin:6px 0 10px;" />

  <label class="meta">Rôle</label>
  <select name="role_id" style="width:100%; padding:8px; margin:6px 0 10px;">
    <option value="0">-- Choisir --</option>
    <?php foreach ($roles as $r): ?>
      <option value="<?php echo e($r['id']); ?>"><?php echo e($r['name']); ?></option>
    <?php endforeach; ?>
  </select>

  <button class="btn" type="submit">Ajouter</button>
</form>

<?php include __DIR__ . '/includes/footer.php';
