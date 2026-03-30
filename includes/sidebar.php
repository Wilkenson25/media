<?php
?>
<section class="section">
  <div class="container">
    <div class="layout-with-sidebar">
      <div>
        <!-- Placeholder to keep layout balanced. The main content is above. -->
        <p class="meta">Votre contenu continue ici si besoin.</p>
      </div>
      <aside class="sidebar">
        <h4>Populaires</h4>
        <ul>
          <?php if (!empty($popular_articles)): ?>
            <?php foreach (array_slice($popular_articles, 0, 3) as $pop): ?>
              <li>
              <a href="<?php echo e(base_url('article.php?slug=' . ($pop['slug'] ?? ''))); ?>"><?php echo e($pop['title'] ?? ''); ?></a>
                <?php if (!empty($pop['views'])): ?>
                  <span class="meta">(<?php echo e($pop['views']); ?> vues)</span>
                <?php endif; ?>
              </li>
            <?php endforeach; ?>
          <?php else: ?>
            <li class="meta">Pas de données pour l’instant.</li>
          <?php endif; ?>
        </ul>

        <h4>Récents</h4>
        <ul>
          <?php foreach (array_slice($recent_articles, 0, 3) as $rec): ?>
            <li><a href="<?php echo e(base_url('article.php?slug=' . ($rec['slug'] ?? ''))); ?>"><?php echo e($rec['title'] ?? ''); ?></a></li>
          <?php endforeach; ?>
        </ul>

        <h4>Réseaux sociaux</h4>
        <p class="meta">Suivez-nous sur Facebook, X, Instagram.</p>

        <h4>Publicité</h4>
        <div class="card" style="padding:10px; text-align:center;">
          <p class="meta">Espace publicitaire 300x250</p>
        </div>
      </aside>
    </div>
  </div>
</section>
