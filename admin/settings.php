<?php
require_once __DIR__ . '/includes/header.php';
require_role(['admin']);

$pdo = db();
$error = '';
$settings = [
    'site_name' => '',
    'site_logo' => '',
    'site_description' => '',
    'contact_email' => '',
    'contact_phone' => '',
    'social_facebook' => '',
    'social_whatsapp' => '',
    'social_x' => '',
    'brand_color' => '',
    'footer_text' => '',
];

if ($pdo) {
    $rows = $pdo->query("SELECT setting_key, setting_value FROM settings")->fetchAll();
    foreach ($rows as $row) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        foreach ($settings as $key => $val) {
            $settings[$key] = trim($_POST[$key] ?? '');
        }
        try {
            $pdo->exec("DELETE FROM settings");
            $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (:k, :v)");
            foreach ($settings as $k => $v) {
                $stmt->execute([':k' => $k, ':v' => $v]);
            }
        } catch (Throwable $e) {
            $error = 'Erreur lors de la sauvegarde.';
        }
    }
}
?>

<h1>Paramètres</h1>
<?php if ($error !== ''): ?>
  <div class="card" style="padding:12px; border-color:#b3341a;"><?php echo e($error); ?></div>
<?php endif; ?>

<form method="post" class="card" style="padding:16px;">
  <label class="meta">Nom du média</label>
  <input type="text" name="site_name" value="<?php echo e($settings['site_name']); ?>" style="width:100%; padding:8px; margin:6px 0 10px;" />

  <label class="meta">Logo (URL)</label>
  <input type="text" name="site_logo" value="<?php echo e($settings['site_logo']); ?>" style="width:100%; padding:8px; margin:6px 0 10px;" />

  <label class="meta">Description</label>
  <textarea name="site_description" rows="3" style="width:100%; padding:8px; margin:6px 0 10px;"><?php echo e($settings['site_description']); ?></textarea>

  <label class="meta">Email</label>
  <input type="text" name="contact_email" value="<?php echo e($settings['contact_email']); ?>" style="width:100%; padding:8px; margin:6px 0 10px;" />

  <label class="meta">Téléphone</label>
  <input type="text" name="contact_phone" value="<?php echo e($settings['contact_phone']); ?>" style="width:100%; padding:8px; margin:6px 0 10px;" />

  <label class="meta">Facebook</label>
  <input type="text" name="social_facebook" value="<?php echo e($settings['social_facebook']); ?>" style="width:100%; padding:8px; margin:6px 0 10px;" />

  <label class="meta">WhatsApp</label>
  <input type="text" name="social_whatsapp" value="<?php echo e($settings['social_whatsapp']); ?>" style="width:100%; padding:8px; margin:6px 0 10px;" />

  <label class="meta">X (Twitter)</label>
  <input type="text" name="social_x" value="<?php echo e($settings['social_x']); ?>" style="width:100%; padding:8px; margin:6px 0 10px;" />

  <label class="meta">Couleur principale</label>
  <input type="text" name="brand_color" value="<?php echo e($settings['brand_color']); ?>" style="width:100%; padding:8px; margin:6px 0 10px;" />

  <label class="meta">Texte footer</label>
  <input type="text" name="footer_text" value="<?php echo e($settings['footer_text']); ?>" style="width:100%; padding:8px; margin:6px 0 10px;" />

  <button class="btn" type="submit">Sauvegarder</button>
</form>

<?php include __DIR__ . '/includes/footer.php';
