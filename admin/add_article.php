<?php
require_once __DIR__ . '/includes/header.php';
require_role(['admin', 'editor', 'journalist']);

$pdo = db();
$categories = [];
$error = '';

if ($pdo) {
    $categories = $pdo->query("SELECT id, name FROM categories ORDER BY name ASC")->fetchAll();
}

$user = admin_user();
$isJournalist = ($user['role'] ?? '') === 'journalist';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo) {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $excerpt = trim($_POST['excerpt'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0);
    $status = $isJournalist ? 'draft' : ($_POST['status'] ?? 'draft');

    if ($title === '' || $content === '') {
        $error = 'Titre et contenu obligatoires.';
    } else {
        $imagePath = '';
        if (!empty($_FILES['image']['name'])) {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $filename = 'article_' . time() . '.' . $ext;
            $dest = __DIR__ . '/../uploads/' . $filename;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
                $imagePath = base_url('uploads/' . $filename);
            }
        }

        $publishedAt = ($status === 'published') ? date('Y-m-d H:i:s') : null;

        $stmt = $pdo->prepare("INSERT INTO articles (title, slug, content, excerpt, image_url, category_id, status, author_name, published_at) VALUES (:title, :slug, :content, :excerpt, :image_url, :category_id, :status, :author_name, :published_at)");
        $slug = preg_replace('/[^a-z0-9]+/i', '-', strtolower($title));
        $slug = trim($slug, '-');
        $stmt->execute([
            ':title' => $title,
            ':slug' => $slug,
            ':content' => $content,
            ':excerpt' => $excerpt,
            ':image_url' => $imagePath,
            ':category_id' => $category_id,
            ':status' => $status,
            ':author_name' => $user['name'] ?? $user['email'] ?? 'Rédaction',
            ':published_at' => $publishedAt,
        ]);
        header('Location: articles.php');
        exit;
    }
}
?>

<h1>Ajouter un article</h1>
<?php if ($error !== ''): ?>
  <div class="card" style="padding:12px; border-color:#b3341a;"><?php echo e($error); ?></div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data" class="card" style="padding:16px;">
  <label class="meta">Titre</label>
  <input type="text" name="title" style="width:100%; padding:8px; margin:6px 0 10px;" />

  <label class="meta">Résumé</label>
  <textarea name="excerpt" rows="3" style="width:100%; padding:8px; margin:6px 0 10px;"></textarea>

  <label class="meta">Contenu</label>
  <textarea name="content" rows="10" style="width:100%; padding:8px; margin:6px 0 10px;"></textarea>

  <label class="meta">Image</label>
  <input type="file" name="image" style="margin:6px 0 10px;" />

  <label class="meta">Catégorie</label>
  <select name="category_id" style="width:100%; padding:8px; margin:6px 0 10px;">
    <option value="0">-- Choisir --</option>
    <?php foreach ($categories as $cat): ?>
      <option value="<?php echo e($cat['id']); ?>"><?php echo e($cat['name']); ?></option>
    <?php endforeach; ?>
  </select>

  <?php if (!$isJournalist): ?>
    <label class="meta">Statut</label>
    <select name="status" style="width:100%; padding:8px; margin:6px 0 10px;">
      <option value="draft">Brouillon</option>
      <option value="published">Publié</option>
    </select>
  <?php else: ?>
    <p class="meta">Rôle journaliste: publication en brouillon uniquement.</p>
  <?php endif; ?>

  <button class="btn" type="submit">Enregistrer</button>
</form>

<?php include __DIR__ . '/includes/footer.php';
