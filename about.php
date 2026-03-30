<?php
require_once __DIR__ . '/config/db.php';

function e($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

$page_title = 'À propos | Média en ligne';
$page_description = 'Notre histoire, notre mission et notre équipe.';
$page_og_type = 'website';
$page_url = base_url('about.php');

$team = [];

$pdo = db();
$about = null;
if ($pdo) {
    try {
        $about = $pdo->query("SELECT * FROM about_settings ORDER BY id DESC LIMIT 1")->fetch();
        $team = $pdo->query("SELECT name, role, photo, bio, social FROM equipe ORDER BY id ASC")->fetchAll();
    } catch (Throwable $e) {
        $about = null;
        $team = [];
    }
}

include __DIR__ . '/includes/header.php';
?>

<section class="section premium-section">
  <div class="container">
    <h1><?php echo e($about['title'] ?? 'À propos'); ?></h1>
    <p class="meta"><?php echo e($about['intro'] ?? 'Un média indépendant qui informe avec rigueur et proximité.'); ?></p>

    <div class="card premium-card" style="padding:18px;">
      <h2>Notre histoire</h2>
      <p><?php echo e($about['history'] ?? ''); ?></p>
      <h2>Mission</h2>
      <p><?php echo e($about['mission'] ?? ''); ?></p>
      <h2>Vision</h2>
      <p><?php echo e($about['vision'] ?? ''); ?></p>
      <h2>Notre méthode</h2>
      <p><?php echo e($about['method'] ?? ''); ?></p>
    </div>
  </div>
</section>

<section class="section premium-section">
  <div class="container">
    <h2 class="section-title">Repères</h2>
    <div class="grid grid-3">
      <div class="card" style="padding:14px;">
        <h3>2019</h3>
        <p>Lancement du média avec une équipe de 4 journalistes.</p>
      </div>
      <div class="card" style="padding:14px;">
        <h3>2021</h3>
        <p>Couverture nationale et partenariats communautaires.</p>
      </div>
      <div class="card" style="padding:14px;">
        <h3>2024</h3>
        <p>Développement des formats vidéo et podcasts.</p>
      </div>
    </div>
  </div>
</section>

<section class="section premium-section">
  <div class="container">
    <h2 class="section-title">Notre équipe</h2>
    <div class="grid grid-3">
      <?php foreach ($team as $member): ?>
        <article class="card">
          <?php if (!empty($member['photo'])): ?>
            <img src="<?php echo e($member['photo']); ?>" alt="<?php echo e($member['name']); ?>" loading="lazy" />
          <?php endif; ?>
          <div class="card-body">
            <h3><?php echo e($member['name']); ?></h3>
            <p class="meta"><?php echo e($member['role']); ?></p>
            <a class="btn" href="<?php echo e($member['social']); ?>">Réseau</a>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section premium-section">
  <div class="container">
    <h2 class="section-title">Valeurs & engagements</h2>
    <div class="grid grid-3">
      <div class="card" style="padding:14px;">
        <p><?php echo e($about['about_values'] ?? ''); ?></p>
      </div>
    </div>
  </div>
</section>

<section class="section premium-section">
  <div class="container">
    <h2 class="section-title">Ce qui nous distingue</h2>
    <div class="card" style="padding:14px;">
      <p><?php echo e($about['distinctiveness'] ?? ''); ?></p>
    </div>
  </div>
</section>

<section class="section premium-section">
  <div class="container">
    <div class="card" style="padding:16px; text-align:center;">
      <h2>Travailler avec nous</h2>
      <p class="meta">Partenariats, suggestions de sujets, collaboration éditoriale.</p>
      <a class="btn" href="<?php echo e($about['cta_link'] ?? base_url('contact.php')); ?>"><?php echo e($about['cta_text'] ?? 'Nous contacter'); ?></a>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php';
