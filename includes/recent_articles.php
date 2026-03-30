<?php
?>
<section class="section">
  <div class="container">
    <h2 class="section-title">Articles récents</h2>
    <?php if (!empty($recent_articles)): ?>
      <div class="grid grid-3">
        <?php foreach ($recent_articles as $article): ?>
          <article class="card">
            <?php if (!empty($article['image_url'])): ?>
              <img src="<?php echo e($article['image_url']); ?>" alt="<?php echo e($article['title']); ?>" loading="lazy" />
            <?php endif; ?>
            <div class="card-body">
              <h3><?php echo e($article['title'] ?? ''); ?></h3>
              <p class="meta"><?php echo reading_time_minutes($article['content'] ?? $article['excerpt'] ?? ''); ?> min de lecture</p>
              <p><?php echo e(make_excerpt($article['excerpt'] ?? $article['content'] ?? '')); ?></p>
              <a class="btn" href="<?php echo e(base_url('article.php?slug=' . ($article['slug'] ?? ''))); ?>">Lire la suite</a>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p>Aucun article récent pour le moment.</p>
    <?php endif; ?>
  </div>
</section>
