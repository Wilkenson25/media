<?php
?>
<section class="section">
  <div class="container">
    <div class="layout-with-sidebar">
      <div>
        <h2 class="section-title">Actualités principales</h2>
        <div class="grid grid-2">
          <?php foreach (array_slice($recent_articles, 0, 4) as $article): ?>
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

        <h2 class="section-title" style="margin-top:24px;">Articles populaires</h2>
        <div class="grid grid-2">
          <?php foreach (array_slice($popular_articles, 0, 4) as $article): ?>
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

        <h2 class="section-title" style="margin-top:24px;">Vidéos ou podcasts récents</h2>
        <div class="grid grid-3">
          <?php foreach (array_slice($trending_articles, 0, 3) as $article): ?>
            <article class="card">
              <?php if (!empty($article['image_url'])): ?>
                <img src="<?php echo e($article['image_url']); ?>" alt="<?php echo e($article['title']); ?>" loading="lazy" />
              <?php endif; ?>
              <div class="card-body">
                <h3><?php echo e($article['title'] ?? ''); ?></h3>
                <p><?php echo e(make_excerpt($article['excerpt'] ?? $article['content'] ?? '')); ?></p>
                <a class="btn" href="<?php echo e(base_url('article.php?slug=' . ($article['slug'] ?? ''))); ?>">Voir</a>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      </div>

      <aside class="sidebar">
        <h4>Newsletter</h4>
        <form class="card" style="padding:12px;" method="post" action="#">
          <input type="email" name="newsletter" placeholder="Votre email" style="width:100%; padding:8px; margin:6px 0 10px;" />
          <button class="btn" type="submit">S'abonner</button>
        </form>

        <h4>Réseaux sociaux</h4>
        <p class="meta">Facebook • X • Instagram</p>

        <h4>Recommandés</h4>
        <ul>
          <?php foreach (array_slice($recent_articles, 0, 3) as $rec): ?>
            <li><a href="<?php echo e(base_url('article.php?slug=' . ($rec['slug'] ?? ''))); ?>"><?php echo e($rec['title'] ?? ''); ?></a></li>
          <?php endforeach; ?>
        </ul>
      </aside>
    </div>
  </div>
</section>
