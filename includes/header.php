<?php
?><!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?php echo e($page_title ?? 'Accueil | Média en ligne'); ?></title>
  <meta name="description" content="<?php echo e($page_description ?? 'Actualités et reportages en temps réel.'); ?>" />
  <meta property="og:title" content="<?php echo e($page_title ?? 'Accueil | Média en ligne'); ?>" />
  <meta property="og:description" content="<?php echo e($page_description ?? 'Actualités et reportages en temps réel.'); ?>" />
  <meta property="og:type" content="<?php echo e($page_og_type ?? 'website'); ?>" />
  <meta property="og:url" content="<?php echo e($page_url ?? base_url('index.php')); ?>" />
  <?php if (!empty($page_image)): ?>
    <meta property="og:image" content="<?php echo e($page_image); ?>" />
  <?php endif; ?>
  <meta name="twitter:card" content="summary_large_image" />
  <link rel="stylesheet" href="<?php echo e(base_url('assets/styles.css')); ?>" />
</head>
<body>
<header class="site-header">
  <div class="container header-row">
    <div class="logo">Media360</div>
    <nav class="main-nav" aria-label="Menu principal" id="main-nav">
      <a href="<?php echo e(base_url('index.php')); ?>">Accueil</a>
      <a href="<?php echo e(base_url('articles.php')); ?>">Actualités</a>
      <a href="<?php echo e(base_url('videos.php')); ?>">Vidéos</a>
      <a href="<?php echo e(base_url('podcasts.php')); ?>">Podcasts</a>
      <a href="<?php echo e(base_url('about.php')); ?>">À propos</a>
      <a href="<?php echo e(base_url('contact.php')); ?>">Contact</a>
    </nav>
    <form class="search-bar" method="get" action="<?php echo e(base_url('search.php')); ?>">
      <input type="text" name="q" placeholder="Rechercher..." />
    </form>
    <button class="menu-toggle" aria-label="Menu mobile" id="menu-toggle">
      <span></span>
    </button>
  </div>
  <?php
    $isCategoryPage = str_ends_with($_SERVER['SCRIPT_NAME'] ?? '', 'categorie.php');
    $activeCategorySlug = $isCategoryPage ? ($_GET['slug'] ?? '') : '';
  ?>
  <div class="container nav-categories category-bar" id="nav-categories">
    <?php foreach ($categories as $cat): ?>
      <?php $isActive = ($activeCategorySlug !== '' && ($cat['slug'] ?? '') === $activeCategorySlug); ?>
      <a class="<?php echo $isActive ? 'active' : ''; ?>" href="<?php echo e(base_url('categorie.php?slug=' . ($cat['slug'] ?? ''))); ?>">
        <?php echo e($cat['name'] ?? ''); ?>
      </a>
    <?php endforeach; ?>
  </div>
</header>
<main>
