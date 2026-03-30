<?php
?>
<section id="categories" class="section">
  <div class="container">
    <h2 class="section-title">Catégories</h2>
    <?php if (!empty($categories_with_articles)): ?>
      <?php foreach ($categories_with_articles as $block): ?>
        <div class="section">
          <h3><?php echo e($block['category']['name'] ?? ''); ?></h3>
          <?php if (!empty($block['articles'])): ?>
            <div class="grid grid-3">
              <?php foreach ($block['articles'] as $article): ?>
                <article class="card">
                  <?php if (!empty($article['image_url'])): ?>
                    <img src="<?php echo e($article['image_url']); ?>" alt="<?php echo e($article['title']); ?>" loading="lazy" />
                  <?php endif; ?>
                  <div class="card-body">
                    <?php if (!empty($article['is_trending'] ?? 0)): ?>
                      <span class="badge">🔥 Tendance</span>
                    <?php endif; ?>
                    <h4><a href="<?php echo e(base_url('article.php?slug=' . ($article['slug'] ?? ''))); ?>"><?php echo e($article['title'] ?? ''); ?></a></h4>
                    <p class="meta"><?php echo reading_time_minutes($article['content'] ?? $article['excerpt'] ?? ''); ?> min de lecture</p>
                    <p><?php echo e(make_excerpt($article['excerpt'] ?? $article['content'] ?? '')); ?></p>
                    <a class="btn" href="<?php echo e(base_url('article.php?slug=' . ($article['slug'] ?? ''))); ?>">Lire la suite</a>
                  </div>
                </article>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <p>Aucun article dans cette catégorie.</p>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p>Aucune catégorie disponible.</p>
    <?php endif; ?>
  </div>
</section>
