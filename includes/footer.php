<?php
?>
</main>
<footer class="site-footer">
  <div class="container">
    <div class="grid grid-3">
      <div>
        <h4>Liens rapides</h4>
        <ul>
          <li><a href="<?php echo e(base_url('index.php')); ?>">Accueil</a></li>
          <li><a href="<?php echo e(base_url('articles.php')); ?>">Actualités</a></li>
          <li><a href="<?php echo e(base_url('videos.php')); ?>">Vidéos</a></li>
          <li><a href="<?php echo e(base_url('podcasts.php')); ?>">Podcasts</a></li>
          <li><a href="<?php echo e(base_url('about.php')); ?>">À propos</a></li>
          <li><a href="<?php echo e(base_url('contact.php')); ?>">Contact</a></li>
        </ul>
      </div>
      <div>
        <h4>Réseaux sociaux</h4>
        <p class="meta">Facebook • X • Instagram</p>
      </div>
      <div>
        <h4>Contact</h4>
        <p class="meta">contact@media360.ht</p>
      </div>
    </div>
    <p class="meta">&copy; <?php echo date('Y'); ?> <a href="<?php echo e(base_url('login.php')); ?>"><?php echo e($site_name ?? 'Media360'); ?></a>. Tous droits réservés.</p>
  </div>
  <?php if (!empty($categories)): ?>
    <div class="container category-bar" style="padding-top:10px;">
      <?php foreach ($categories as $cat): ?>
        <a href="<?php echo e(base_url('categorie.php?slug=' . ($cat['slug'] ?? ''))); ?>"><?php echo e($cat['name'] ?? ''); ?></a>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</footer>
<script>
  (function () {
    var toggle = document.getElementById('menu-toggle');
    if (!toggle) return;
    toggle.addEventListener('click', function () {
      document.body.classList.toggle('menu-open');
    });
  })();
</script>
<script>
  (function () {
    var slider = document.querySelector('[data-slider]');
    if (!slider) return;
    var slides = slider.querySelectorAll('.slider-slide');
    var dots = slider.querySelectorAll('.slider-dots .dot');
    var prev = slider.querySelector('.slider-btn.prev');
    var next = slider.querySelector('.slider-btn.next');
    var index = 0;
    function show(i) {
      slides.forEach(function (s, idx) { s.classList.toggle('is-active', idx === i); });
      dots.forEach(function (d, idx) { d.classList.toggle('is-active', idx === i); });
      index = i;
    }
    if (prev) prev.addEventListener('click', function () { show((index - 1 + slides.length) % slides.length); });
    if (next) next.addEventListener('click', function () { show((index + 1) % slides.length); });
    dots.forEach(function (d, idx) { d.addEventListener('click', function () { show(idx); }); });
    setInterval(function () { show((index + 1) % slides.length); }, 6000);
  })();
</script>
</body>
</html>
