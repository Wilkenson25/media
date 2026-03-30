<?php
?>
<section class="section hero">
  <div class="container">
    <h2 class="section-title">À la une aujourd'hui</h2>
    <?php if (!empty($featured_articles)): ?>
      <div class="slider" data-slider>
        <div class="slider-track">
          <?php foreach (array_slice($featured_articles, 0, 5) as $i => $slide): ?>
            <a class="slider-slide card hero-card hero-card--main<?php echo $i === 0 ? ' is-active' : ''; ?>" href="<?php echo e(base_url('article.php?slug=' . ($slide['slug'] ?? ''))); ?>" style="<?php echo !empty($slide['image_url']) ? 'background-image: url(' . e($slide['image_url']) . ');' : ''; ?>">
              <?php if (!empty($slide['image_url'])): ?>
                <img class="sr-only" src="<?php echo e($slide['image_url']); ?>" alt="<?php echo e($slide['title']); ?>" loading="<?php echo $i === 0 ? 'eager' : 'lazy'; ?>" />
              <?php endif; ?>
              <div class="card-body hero-overlay">
                <div class="meta">
                  <span class="badge">À la une</span>
                </div>
                <h3><?php echo e($slide['title'] ?? ''); ?></h3>
                <p class="meta"><?php echo reading_time_minutes($slide['content'] ?? $slide['excerpt'] ?? ''); ?> min de lecture</p>
              </div>
            </a>
          <?php endforeach; ?>
        </div>
        <button class="slider-btn prev" type="button" aria-label="Précédent">&larr;</button>
        <button class="slider-btn next" type="button" aria-label="Suivant">&rarr;</button>
        <div class="slider-dots">
          <?php foreach (array_slice($featured_articles, 0, 5) as $i => $slide): ?>
            <button class="dot<?php echo $i === 0 ? ' is-active' : ''; ?>" data-slide="<?php echo $i; ?>" type="button" aria-label="Aller à l'article <?php echo $i + 1; ?>"></button>
          <?php endforeach; ?>
        </div>
      </div>
    <?php else: ?>
      <p>Aucun article en vedette pour le moment.</p>
    <?php endif; ?>
  </div>
</section>
