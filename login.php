<?php
require_once __DIR__ . '/config/db.php';

function e($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

@ini_set('session.save_path', sys_get_temp_dir());
session_start();
if (!empty($_SESSION['admin_user'])) {
    header('Location: ' . base_url('admin/dashboard.php'));
    exit;
}

$page_title = 'Connexion | Média en ligne';
$page_description = 'Connexion au panneau d’administration.';
$page_url = base_url('login.php');

$error = $_GET['error'] ?? '';
$site_name = 'Media360';
if ($pdo) {
    try {
        $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'site_name' LIMIT 1");
        $stmt->execute();
        $val = $stmt->fetchColumn();
        if (!empty($val)) {
            $site_name = $val;
        }
    } catch (Throwable $e) {
        // keep default
    }
}

include __DIR__ . '/includes/header.php';
?>

<section class="section">
  <div class="container">
    <h1>Connexion</h1>
    <?php if ($error !== ''): ?>
      <div class="card" style="padding:12px; border-color:#b3341a;"><?php echo e($error); ?></div>
    <?php endif; ?>
    <form method="post" action="<?php echo e(base_url('process_login.php')); ?>" class="card" style="padding:16px; max-width:420px;">
      <label class="meta">Email</label>
      <input type="email" name="email" style="width:100%; padding:8px; margin:6px 0 10px;" required />
      <label class="meta">Mot de passe</label>
      <input type="password" name="password" style="width:100%; padding:8px; margin:6px 0 10px;" required />
      <button class="btn" type="submit">Se connecter</button>
    </form>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php';
