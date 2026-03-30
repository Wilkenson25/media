<?php
require_once __DIR__ . '/config/db.php';

function e($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function make_excerpt($text, $limit = 160)
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

$pdo = db();
$category = null;
$articles = [];
$recent_articles = [];
$popular_articles = [];
$categories = [];

$slug = trim($_GET['slug'] ?? '');
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;
$totalPages = 1;

if ($pdo) {
    try {
        $stmt = $pdo->prepare("SELECT id, name, slug FROM categories WHERE slug = :slug LIMIT 1");
        $stmt->execute([':slug' => $slug]);
        $category = $stmt->fetch();

        $stmt = $pdo->prepare("SELECT id, name, slug FROM categories ORDER BY name ASC");
        $stmt->execute();
        $categories = $stmt->fetchAll();

        if ($category) {
            $total = (int)$pdo->prepare("SELECT COUNT(*) FROM articles WHERE status = 'published' AND category_id = :cid");
            $countStmt = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE status = 'published' AND category_id = :cid");
            $countStmt->execute([':cid' => $category['id']]);
            $total = (int)$countStmt->fetchColumn();
            $totalPages = (int)max(1, ceil($total / $perPage));

            $stmt = $pdo->prepare("SELECT id, title, slug, image_url, excerpt, content, published_at FROM articles WHERE status = 'published' AND category_id = :cid ORDER BY published_at DESC LIMIT :limit OFFSET :offset");
            $stmt->bindValue(':cid', $category['id'], PDO::PARAM_INT);
            $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $articles = $stmt->fetchAll();
        }

        $stmt = $pdo->prepare("SELECT id, title, slug, image_url, excerpt, content, published_at FROM articles WHERE status = 'published' ORDER BY published_at DESC LIMIT 5");
        $stmt->execute();
        $recent_articles = $stmt->fetchAll();

        $stmt = $pdo->prepare("SELECT id, title, slug, image_url, excerpt, content, views, published_at FROM articles WHERE status = 'published' ORDER BY views DESC, published_at DESC LIMIT 5");
        $stmt->execute();
        $popular_articles = $stmt->fetchAll();
    } catch (Throwable $e) {
        // ignore
    }
}

$page_title = ($category['name'] ?? 'Catégorie') . ' | Média en ligne';
$page_description = 'Articles de la catégorie ' . ($category['name'] ?? '');
$page_url = base_url('category.php?slug=' . $slug);

include __DIR__ . '/includes/header.php';
?>

<section class="section">
  <div class="container">
    <h1><?php echo e($category['name'] ?? 'Catégorie'); ?></h1>
    <p class="meta">Articles classés par thème.</p>

    <?php if (!empty($articles)): ?>
      <div class="grid grid-3">
        <?php foreach ($articles as $article): ?>
          <article class="card">
            <?php if (!empty($article['image_url'])): ?>
              <img src="<?php echo e($article['image_url']); ?>" alt="<?php echo e($article['title']); ?>" loading="lazy" />
            <?php endif; ?>
            <div class="card-body">
              <h3><?php echo e($article['title'] ?? ''); ?></h3>
              <p><?php echo e(make_excerpt($article['excerpt'] ?? $article['content'] ?? '')); ?></p>
              <a class="btn" href="<?php echo e(base_url('article.php?slug=' . ($article['slug'] ?? ''))); ?>">Lire la suite</a>
            </div>
          </article>
        <?php endforeach; ?>
      </div>

      <div class="section">
        <?php if ($page > 1): ?>
          <a class="btn" href="<?php echo e(base_url('category.php?slug=' . $slug . '&page=' . ($page - 1))); ?>">Page précédente</a>
        <?php endif; ?>
        <?php if ($page < $totalPages): ?>
          <a class="btn" href="<?php echo e(base_url('category.php?slug=' . $slug . '&page=' . ($page + 1))); ?>">Page suivante</a>
        <?php endif; ?>
        <span class="meta">Page <?php echo e($page); ?> / <?php echo e($totalPages); ?></span>
      </div>
    <?php else: ?>
      <p>Aucun article dans cette catégorie.</p>
    <?php endif; ?>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="layout-with-sidebar">
      <div>
        <h2>Catégories populaires</h2>
        <div class="category-bar">
          <?php foreach ($categories as $cat): ?>
            <a href="<?php echo e(base_url('category.php?slug=' . ($cat['slug'] ?? ''))); ?>"><?php echo e($cat['name'] ?? ''); ?></a>
          <?php endforeach; ?>
        </div>
      </div>
      <aside class="sidebar">
        <h4>Populaires</h4>
        <ul>
          <?php foreach (array_slice($popular_articles, 0, 3) as $pop): ?>
            <li><a href="<?php echo e(base_url('article.php?slug=' . ($pop['slug'] ?? ''))); ?>"><?php echo e($pop['title'] ?? ''); ?></a></li>
          <?php endforeach; ?>
        </ul>

        <h4>Récents</h4>
        <ul>
          <?php foreach (array_slice($recent_articles, 0, 3) as $rec): ?>
            <li><a href="<?php echo e(base_url('article.php?slug=' . ($rec['slug'] ?? ''))); ?>"><?php echo e($rec['title'] ?? ''); ?></a></li>
          <?php endforeach; ?>
        </ul>
      </aside>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php';
