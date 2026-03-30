<?php
require_once __DIR__ . '/includes/header.php';
require_role(['admin', 'editor']);

$pdo = db();
$categories = [];
$error = '';
$editCategory = null;

if ($pdo) {
    if (isset($_GET['delete'])) {
        require_role(['admin']);
        $id = (int)$_GET['delete'];
        $pdo->prepare("DELETE FROM categories WHERE id = :id")->execute([':id' => $id]);
        header('Location: categories.php');
        exit;
    }

    if (isset($_GET['edit'])) {
        $id = (int)$_GET['edit'];
        $stmt = $pdo->prepare("SELECT id, name, slug FROM categories WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $editCategory = $stmt->fetch();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = trim($_POST['name'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $editId = (int)($_POST['id'] ?? 0);
        if ($slug === '') {
            $slug = preg_replace('/[^a-z0-9]+/i', '-', strtolower($name));
            $slug = trim($slug, '-');
        }
        if ($name === '') {
            $error = 'Nom obligatoire.';
        } else {
            if ($editId > 0) {
                $stmt = $pdo->prepare("UPDATE categories SET name = :name, slug = :slug WHERE id = :id");
                $stmt->execute([':name' => $name, ':slug' => $slug, ':id' => $editId]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO categories (name, slug) VALUES (:name, :slug)");
                $stmt->execute([':name' => $name, ':slug' => $slug]);
            }
        }
    }

    $categories = $pdo->query("SELECT id, name, slug FROM categories ORDER BY name ASC")->fetchAll();
}
?>

<h1>Catégories</h1>
<?php if ($error !== ''): ?>
  <div class="card" style="padding:12px; border-color:#b3341a;"><?php echo e($error); ?></div>
<?php endif; ?>

<form method="post" class="card" style="padding:16px; margin-bottom:12px;">
  <input type="hidden" name="id" value="<?php echo e($editCategory['id'] ?? 0); ?>" />
  <label class="meta">Nom</label>
  <input type="text" name="name" value="<?php echo e($editCategory['name'] ?? ''); ?>" style="width:100%; padding:8px; margin:6px 0 10px;" />
  <label class="meta">Slug (optionnel)</label>
  <input type="text" name="slug" value="<?php echo e($editCategory['slug'] ?? ''); ?>" style="width:100%; padding:8px; margin:6px 0 10px;" />
  <button class="btn" type="submit"><?php echo $editCategory ? 'Mettre à jour' : 'Ajouter'; ?></button>
</form>

<table class="card" style="width:100%; border-collapse:collapse;">
  <thead>
    <tr>
      <th style="text-align:left; padding:8px;">Nom</th>
      <th style="text-align:left; padding:8px;">Slug</th>
      <th style="text-align:left; padding:8px;">Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($categories as $c): ?>
      <tr>
        <td style="padding:8px;"><?php echo e($c['name']); ?></td>
        <td style="padding:8px;"><?php echo e($c['slug']); ?></td>
        <td style="padding:8px;">
          <?php if ((admin_user()['role'] ?? '') === 'admin'): ?>
            <a href="categories.php?edit=<?php echo e($c['id']); ?>">Modifier</a>
            | <a href="categories.php?delete=<?php echo e($c['id']); ?>" onclick="return confirm('Supprimer cette catégorie ?')">Supprimer</a>
          <?php else: ?>
            <span class="meta">Suppression réservée admin</span>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php include __DIR__ . '/includes/footer.php';
