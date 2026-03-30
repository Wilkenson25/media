<?php
require_once __DIR__ . '/config/db.php';

function e($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function reading_time_minutes($text)
{
    $words = str_word_count(strip_tags((string)$text));
    $minutes = (int)max(1, ceil($words / 200));
    return $minutes;
}

$pdo = db();
$article = null;
$category = null;
$tags = [];

$slug = isset($_GET['slug']) ? trim((string)$_GET['slug']) : '';

if ($pdo && $slug !== '') {
    try {
        $stmt = $pdo->prepare("SELECT a.id, a.title, a.slug, a.image_url, a.excerpt, a.content, a.category_id, a.published_at, u.name AS author_name FROM articles a LEFT JOIN users u ON u.id = a.user_id WHERE a.status = 'published' AND a.slug = :slug LIMIT 1");
        $stmt->execute([':slug' => $slug]);
        $article = $stmt->fetch();

        if ($article && !empty($article['category_id'])) {
            $stmt = $pdo->prepare("SELECT id, name, slug FROM categories WHERE id = :id LIMIT 1");
            $stmt->execute([':id' => $article['category_id']]);
            $category = $stmt->fetch();
        }

        // Optional tags if you have article_tags + tags tables.
        // You can adapt this query to your schema if needed.
        try {
            $stmt = $pdo->prepare("SELECT t.name, t.slug FROM tags t INNER JOIN article_tags at ON at.tag_id = t.id WHERE at.article_id = :article_id");
            $stmt->execute([':article_id' => $article['id'] ?? 0]);
            $tags = $stmt->fetchAll();
        } catch (Throwable $e) {
            $tags = [];
        }
    } catch (Throwable $e) {
        $article = null;
    }
}

if ($article) {
    $page_title = ($article['title'] ?? '') . ' | Média en ligne';
    $page_description = $article['excerpt'] ?? '';
    $page_image = $article['image_url'] ?? '';
    $page_url = base_url('article/' . ($article['slug'] ?? ''));
    $page_og_type = 'article';
}

include __DIR__ . '/includes/header.php';
?>

<section class="section article-hero">
  <div class="container">
    <?php if ($article): ?>
      <div class="card article-card">
        <?php if (!empty($article['image_url'])): ?>
          <div class="article-cover" style="background-image:url('<?php echo e($article['image_url']); ?>');"></div>
        <?php endif; ?>
        <div class="article-body">
          <h1><?php echo e($article['title'] ?? ''); ?></h1>
          <p class="meta">
            <?php echo e($article['author_name'] ?? 'Rédaction'); ?>
            · <?php echo e(isset($article['published_at']) ? date('d/m/Y', strtotime($article['published_at'])) : ''); ?>
            · <?php echo reading_time_minutes($article['content'] ?? ''); ?> min de lecture
          </p>
          <?php if (!empty($article['excerpt'])): ?>
            <p class="meta article-excerpt"><?php echo e($article['excerpt']); ?></p>
          <?php endif; ?>
        </div>
      </div>
    <?php endif; ?>
  </div>
</section>

<section class="section">
  <div class="container">
    <?php if ($article): ?>
      <article class="card article-content" style="padding:18px;">
        <div class="section">
          <?php echo $article['content']; ?>
        </div>

        <div class="section">
          <p class="meta">
            Catégorie:
            <?php if ($category): ?>
              <a href="<?php echo e(base_url('categorie.php?slug=' . ($category['slug'] ?? ''))); ?>"><?php echo e($category['name'] ?? ''); ?></a>
            <?php else: ?>
              <span>Non classé</span>
            <?php endif; ?>
          </p>

          <?php if (!empty($tags)): ?>
            <p class="meta">
              Tags:
              <?php foreach ($tags as $tag): ?>
                <a href="<?php echo e(base_url('tag.php?slug=' . ($tag['slug'] ?? ''))); ?>">#<?php echo e($tag['name'] ?? ''); ?></a>
              <?php endforeach; ?>
            </p>
          <?php endif; ?>
        </div>

        <?php
          $currentUrl = base_url('article.php?slug=' . ($article['slug'] ?? ''));
          $shareText = urlencode($article['title'] ?? '');
          $shareUrl = urlencode($currentUrl);
        ?>
        <div class="section">
          <h3>Partager</h3>
          <a class="btn" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $shareUrl; ?>" target="_blank" rel="noopener">Facebook</a>
          <a class="btn" href="https://wa.me/?text=<?php echo $shareText; ?>%20<?php echo $shareUrl; ?>" target="_blank" rel="noopener">WhatsApp</a>
          <a class="btn" href="https://twitter.com/intent/tweet?text=<?php echo $shareText; ?>&url=<?php echo $shareUrl; ?>" target="_blank" rel="noopener">Twitter</a>
        </div>

        <div class="section">
          <h3>Commentaires</h3>
          <form method="post" action="#" class="card" style="padding:12px;">
            <label class="meta">Nom</label>
            <input type="text" name="name" style="width:100%; padding:8px; margin:6px 0 10px;" />
            <label class="meta">Commentaire</label>
            <textarea name="comment" rows="4" style="width:100%; padding:8px; margin:6px 0 10px;"></textarea>
            <button class="btn" type="submit">Publier</button>
          </form>
          <p class="meta">Les commentaires seront activés côté base de données.</p>
        </div>
      </article>
    <?php else: ?>
      <p>Article introuvable.</p>
    <?php endif; ?>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php';
