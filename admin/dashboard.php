<?php
require_once __DIR__ . '/includes/header.php';

$pdo = db();
$stats = [
    'articles' => 0,
    'categories' => 0,
    'users' => 0,
    'messages' => 0,
];
$recent_articles = [];
$recent_messages = [];

if ($pdo) {
    try {
        $stats['articles'] = (int)$pdo->query("SELECT COUNT(*) FROM articles")->fetchColumn();
        $stats['categories'] = (int)$pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
        $stats['users'] = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
        $stats['messages'] = (int)$pdo->query("SELECT COUNT(*) FROM messages")->fetchColumn();

        $stmt = $pdo->query("SELECT id, title, slug, published_at FROM articles ORDER BY published_at DESC LIMIT 5");
        $recent_articles = $stmt->fetchAll();

        $stmt = $pdo->query("SELECT id, name, subject, created_at FROM messages ORDER BY created_at DESC LIMIT 5");
        $recent_messages = $stmt->fetchAll();
    } catch (Throwable $e) {
        // ignore
    }
}
?>

<h1>Dashboard</h1>
<div class="grid grid-3">
  <div class="card" style="padding:12px;">Articles: <?php echo e($stats['articles']); ?></div>
  <div class="card" style="padding:12px;">Catégories: <?php echo e($stats['categories']); ?></div>
  <div class="card" style="padding:12px;">Utilisateurs: <?php echo e($stats['users']); ?></div>
  <div class="card" style="padding:12px;">Messages: <?php echo e($stats['messages']); ?></div>
</div>

<section class="section">
  <h2>Articles récents</h2>
  <ul>
    <?php foreach ($recent_articles as $a): ?>
      <li><?php echo e($a['title'] ?? ''); ?> <span class="meta">(<?php echo e($a['published_at'] ?? ''); ?>)</span></li>
    <?php endforeach; ?>
  </ul>
</section>

<section class="section">
  <h2>Messages récents</h2>
  <ul>
    <?php foreach ($recent_messages as $m): ?>
      <li><?php echo e($m['name'] ?? ''); ?> — <?php echo e($m['subject'] ?? ''); ?></li>
    <?php endforeach; ?>
  </ul>
</section>

<?php include __DIR__ . '/includes/footer.php';
