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

function reading_time_minutes($text)
{
    $words = str_word_count(strip_tags((string)$text));
    $minutes = (int)max(1, ceil($words / 200));
    return $minutes;
}

$pdo = db();
$featured_articles = [];
$recent_articles = [];
$popular_articles = [];
$trending_articles = [];
$categories = [];
$categories_with_articles = [];

if ($pdo) {
    try {
        $stmt = $pdo->prepare("SELECT a.id, a.title, a.slug, a.image_url, a.excerpt, a.content, a.category_id, a.published_at, c.name AS category_name FROM articles a LEFT JOIN categories c ON c.id = a.category_id WHERE a.status = 'published' ORDER BY a.published_at DESC LIMIT 5");
        $stmt->execute();
        $featured_articles = $stmt->fetchAll();

        if (count($featured_articles) === 0) {
            $stmt = $pdo->prepare("SELECT a.id, a.title, a.slug, a.image_url, a.excerpt, a.content, a.category_id, a.published_at, c.name AS category_name FROM articles a LEFT JOIN categories c ON c.id = a.category_id WHERE a.status = 'published' ORDER BY a.published_at DESC LIMIT 5");
            $stmt->execute();
            $featured_articles = $stmt->fetchAll();
        }

        $stmt = $pdo->prepare("SELECT a.id, a.title, a.slug, a.image_url, a.excerpt, a.content, a.category_id, a.published_at, c.name AS category_name FROM articles a LEFT JOIN categories c ON c.id = a.category_id WHERE a.status = 'published' ORDER BY a.published_at DESC LIMIT 6");
        $stmt->execute();
        $recent_articles = $stmt->fetchAll();

        $stmt = $pdo->prepare("SELECT a.id, a.title, a.slug, a.image_url, a.excerpt, a.content, a.category_id, a.published_at, a.views FROM articles a WHERE a.status = 'published' ORDER BY a.views DESC, a.published_at DESC LIMIT 5");
        $stmt->execute();
        $popular_articles = $stmt->fetchAll();

        $stmt = $pdo->prepare("SELECT a.id, a.title, a.slug, a.image_url, a.excerpt, a.content, a.category_id, a.published_at FROM articles a WHERE a.status = 'published' ORDER BY a.published_at DESC LIMIT 5");
        $stmt->execute();
        $trending_articles = $stmt->fetchAll();

        $stmt = $pdo->prepare("SELECT id, name, slug FROM categories ORDER BY name ASC");
        $stmt->execute();
        $categories = $stmt->fetchAll();

        foreach ($categories as $category) {
            $stmt = $pdo->prepare("SELECT id, title, slug, image_url, excerpt, content, category_id, published_at FROM articles WHERE status = 'published' AND category_id = :category_id ORDER BY published_at DESC LIMIT 5");
            $stmt->execute([':category_id' => $category['id']]);
            $articles = $stmt->fetchAll();
            $categories_with_articles[] = [
                'category' => $category,
                'articles' => $articles,
            ];
        }
    } catch (Throwable $e) {
        // Keep empty arrays on error. In production, log the error.
    }
}

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/featured.php';
include __DIR__ . '/includes/recent_articles.php';
include __DIR__ . '/includes/categories_section.php';
include __DIR__ . '/includes/home_sections.php';
include __DIR__ . '/includes/sidebar.php'; // Optionnel
include __DIR__ . '/includes/footer.php';
