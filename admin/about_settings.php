<?php
require_once __DIR__ . '/includes/header.php';
require_role(['admin', 'editor']);

$pdo = db();
$error = '';
$success = '';
$about = [
    'title' => 'À propos',
    'intro' => 'Un média indépendant qui informe avec rigueur et proximité.',
    'history' => '',
    'mission' => '',
    'vision' => '',
    'method' => '',
  'about_values' => '',
    'distinctiveness' => '',
    'cta_text' => 'Nous contacter',
    'cta_link' => base_url('contact.php'),
];

if ($pdo) {
    $row = $pdo->query("SELECT * FROM about_settings ORDER BY id DESC LIMIT 1")->fetch();
    if ($row) {
        $about = array_merge($about, $row);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        foreach ($about as $key => $val) {
            if (in_array($key, ['id', 'updated_at'], true)) {
                continue;
            }
            $about[$key] = trim($_POST[$key] ?? '');
        }
        try {
            $pdo->exec("TRUNCATE TABLE about_settings");
        $stmt = $pdo->prepare("INSERT INTO about_settings (title, intro, history, mission, vision, method, about_values, distinctiveness, cta_text, cta_link) VALUES (:title, :intro, :history, :mission, :vision, :method, :about_values, :distinctiveness, :cta_text, :cta_link)");
        $stmt->execute([
            ':title' => $about['title'],
            ':intro' => $about['intro'],
            ':history' => $about['history'],
            ':mission' => $about['mission'],
            ':vision' => $about['vision'],
            ':method' => $about['method'],
            ':about_values' => $about['about_values'],
            ':distinctiveness' => $about['distinctiveness'],
            ':cta_text' => $about['cta_text'],
            ':cta_link' => $about['cta_link'],
        ]);
            $success = 'Contenu sauvegardé.';
        } catch (Throwable $e) {
            $error = 'Erreur lors de la sauvegarde.';
        }
    }
}
?>

<h1>Page À propos</h1>
<?php if ($error !== ''): ?>
  <div class="card" style="padding:12px; border-color:#b3341a;"><?php echo e($error); ?></div>
<?php elseif ($success !== ''): ?>
  <div class="card" style="padding:12px;"><?php echo e($success); ?></div>
<?php endif; ?>

<form method="post" class="card" style="padding:16px;">
  <label class="meta">Titre</label>
  <input type="text" name="title" value="<?php echo e($about['title']); ?>" style="width:100%; padding:8px; margin:6px 0 10px;" />

  <label class="meta">Intro</label>
  <textarea name="intro" rows="2" style="width:100%; padding:8px; margin:6px 0 10px;"><?php echo e($about['intro']); ?></textarea>

  <label class="meta">Histoire</label>
  <textarea name="history" rows="4" style="width:100%; padding:8px; margin:6px 0 10px;"><?php echo e($about['history']); ?></textarea>

  <label class="meta">Mission</label>
  <textarea name="mission" rows="4" style="width:100%; padding:8px; margin:6px 0 10px;"><?php echo e($about['mission']); ?></textarea>

  <label class="meta">Vision</label>
  <textarea name="vision" rows="4" style="width:100%; padding:8px; margin:6px 0 10px;"><?php echo e($about['vision']); ?></textarea>

  <label class="meta">Méthode</label>
  <textarea name="method" rows="4" style="width:100%; padding:8px; margin:6px 0 10px;"><?php echo e($about['method']); ?></textarea>

  <label class="meta">Valeurs & engagements</label>
  <textarea name="about_values" rows="4" style="width:100%; padding:8px; margin:6px 0 10px;"><?php echo e($about['about_values']); ?></textarea>

  <label class="meta">Ce qui nous distingue</label>
  <textarea name="distinctiveness" rows="4" style="width:100%; padding:8px; margin:6px 0 10px;"><?php echo e($about['distinctiveness']); ?></textarea>

  <label class="meta">Texte du bouton</label>
  <input type="text" name="cta_text" value="<?php echo e($about['cta_text']); ?>" style="width:100%; padding:8px; margin:6px 0 10px;" />

  <label class="meta">Lien du bouton</label>
  <input type="text" name="cta_link" value="<?php echo e($about['cta_link']); ?>" style="width:100%; padding:8px; margin:6px 0 10px;" />

  <button class="btn" type="submit">Sauvegarder</button>
</form>

<?php include __DIR__ . '/includes/footer.php';
