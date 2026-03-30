<?php
require_once __DIR__ . '/includes/header.php';

$pdo = db();
$articles = [];

if ($pdo) {
    try {
        $stmt = $pdo->query("SELECT a.id, a.title, a.status, a.published_at, u.name AS author_name, c.name AS category_name FROM articles a LEFT JOIN categories c ON c.id = a.category_id LEFT JOIN users u ON u.id = a.user_id ORDER BY a.published_at DESC");
        $articles = $stmt->fetchAll();

        if (isset($_GET['delete'])) {
            require_role(['admin']);
            $id = (int)$_GET['delete'];
            $del = $pdo->prepare("DELETE FROM articles WHERE id = :id");
            $del->execute([':id' => $id]);
            header('Location: articles.php');
            exit;
        }
    } catch (Throwable $e) {
        // ignore
    }
}
?>

<h1>Articles</h1>
<a class="btn" href="add_article.php">Ajouter un article</a>

<table class="card" style="width:100%; margin-top:12px; border-collapse:collapse;">
  <thead>
    <tr>
      <th style="text-align:left; padding:8px;">Titre</th>
      <th style="text-align:left; padding:8px;">Catégorie</th>
      <th style="text-align:left; padding:8px;">Auteur</th>
      <th style="text-align:left; padding:8px;">Statut</th>
      <th style="text-align:left; padding:8px;">Date</th>
      <th style="text-align:left; padding:8px;">Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($articles as $a): ?>
      <tr>
        <td style="padding:8px;"><?php echo e($a['title'] ?? ''); ?></td>
        <td style="padding:8px;"><?php echo e($a['category_name'] ?? ''); ?></td>
        <td style="padding:8px;"><?php echo e($a['author_name'] ?? ''); ?></td>
        <td style="padding:8px;"><?php echo e($a['status'] ?? ''); ?></td>
        <td style="padding:8px;"><?php echo e($a['published_at'] ?? ''); ?></td>
        <td style="padding:8px;">
          <a href="edit_article.php?id=<?php echo e($a['id']); ?>">Modifier</a>
          <?php if ((admin_user()['role'] ?? '') === 'admin'): ?>
            | <a href="articles.php?delete=<?php echo e($a['id']); ?>" onclick="return confirm('Supprimer cet article ?')">Supprimer</a>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php include __DIR__ . '/includes/footer.php';
