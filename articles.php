<?php
require_once __DIR__ . '/config/db.php';

function e($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function make_excerpt($text, $limit = 180)
{
    $text = trim(strip_tags((string)$text));
    if ($text === '') {
        return '';
    }
    if (mb_strlen($text) <= $limit) {
        return $text;
    }
    return mb_substr($text, 0, $limit - 3) . '...';
}

function reading_time_minutes($text)
{
    $words = str_word_count(strip_tags((string)$text));
    $minutes = (int)max(1, ceil($words / 200));
    return $minutes;
}

$pdo = db();
$articles = [];
$categories = [];

$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;
 $totalPages = 1;

if ($pdo) {
    try {
        $total = (int)$pdo->query("SELECT COUNT(*) FROM articles WHERE status = 'published'")->fetchColumn();
        $totalPages = (int)max(1, ceil($total / $perPage));

        $stmt = $pdo->prepare("SELECT a.id, a.title, a.slug, a.image_url, a.excerpt, a.content, a.category_id, a.published_at, c.name AS category_name FROM articles a LEFT JOIN categories c ON c.id = a.category_id WHERE a.status = 'published' ORDER BY a.published_at DESC LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $articles = $stmt->fetchAll();

        $stmt = $pdo->prepare("SELECT id, name, slug FROM categories ORDER BY name ASC");
        $stmt->execute();
        $categories = $stmt->fetchAll();
    } catch (Throwable $e) {
        // Keep empty arrays on error. In production, log the error.
    }
}

include __DIR__ . '/includes/header.php';
?>

<section class="section">
  <div class="container">
    <h2 class="section-title">Tous les articles</h2>
    <?php if (!empty($articles)): ?>
      <div class="grid grid-3">
        <?php foreach ($articles as $article): ?>
          <article class="card">
            <?php if (!empty($article['image_url'])): ?>
              <img src="<?php echo e($article['image_url']); ?>" alt="<?php echo e($article['title']); ?>" loading="lazy" />
            <?php endif; ?>
            <div class="card-body">
              <?php if (!empty($article['is_trending'] ?? 0)): ?>
                <span class="badge">🔥 Tendance</span>
              <?php endif; ?>
              <h3><?php echo e($article['title'] ?? ''); ?></h3>
              <p class="meta"><?php echo reading_time_minutes($article['content'] ?? $article['excerpt'] ?? ''); ?> min de lecture</p>
              <p><?php echo e(make_excerpt($article['excerpt'] ?? $article['content'] ?? '')); ?></p>
              <a class="btn" href="<?php echo e(base_url('article.php?slug=' . ($article['slug'] ?? ''))); ?>">Lire la suite</a>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
      <div class="section">
        <?php if ($page > 1): ?>
          <a class="btn" href="<?php echo e(base_url('articles.php?page=' . ($page - 1))); ?>">Page précédente</a>
        <?php endif; ?>
        <?php if ($page < $totalPages): ?>
          <a class="btn" href="<?php echo e(base_url('articles.php?page=' . ($page + 1))); ?>">Page suivante</a>
        <?php endif; ?>
        <span class="meta">Page <?php echo e($page); ?> / <?php echo e($totalPages); ?></span>
      </div>
    <?php else: ?>
      <p>Aucun article pour le moment.</p>
    <?php endif; ?>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php';
