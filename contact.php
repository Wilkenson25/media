<?php
require_once __DIR__ . '/config/db.php';

function e($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

$page_title = 'Contact | Média en ligne';
$page_description = 'Contactez notre rédaction.';
$page_og_type = 'website';
$page_url = base_url('contact.php');

$pdo = db();
$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name === '' || $email === '' || $subject === '' || $message === '') {
        $error = 'Veuillez remplir tous les champs.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Adresse email invalide.';
    } elseif ($pdo) {
        try {
            $stmt = $pdo->prepare("INSERT INTO messages (name, email, subject, message, created_at) VALUES (:name, :email, :subject, :message, NOW())");
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':subject' => $subject,
                ':message' => $message,
            ]);
            $success = true;
        } catch (Throwable $e) {
            $error = 'Erreur lors de l\'envoi. Réessayez.';
        }
    } else {
        $error = 'Connexion base de données indisponible.';
    }
}

include __DIR__ . '/includes/header.php';
?>

<section class="section premium-section">
  <div class="container">
    <h1>Contact</h1>
    <p class="meta">Écrivez-nous, nous répondons rapidement.</p>

    <?php if ($success): ?>
      <div class="card" style="padding:12px;">Votre message a été envoyé.</div>
    <?php elseif ($error !== ''): ?>
      <div class="card" style="padding:12px; border-color:#b3341a;"><?php echo e($error); ?></div>
    <?php endif; ?>

    <div class="grid grid-2">
      <form method="post" class="card premium-card" style="padding:18px;">
        <label class="meta">Nom</label>
        <input type="text" name="name" value="<?php echo e($_POST['name'] ?? ''); ?>" style="width:100%; padding:8px; margin:6px 0 10px;" />

        <label class="meta">Email</label>
        <input type="email" name="email" value="<?php echo e($_POST['email'] ?? ''); ?>" style="width:100%; padding:8px; margin:6px 0 10px;" />

        <label class="meta">Sujet</label>
        <input type="text" name="subject" value="<?php echo e($_POST['subject'] ?? ''); ?>" style="width:100%; padding:8px; margin:6px 0 10px;" />

        <label class="meta">Message</label>
        <textarea name="message" rows="5" style="width:100%; padding:8px; margin:6px 0 10px;"><?php echo e($_POST['message'] ?? ''); ?></textarea>

        <button class="btn" type="submit">Envoyer</button>
      </form>

      <div class="card premium-card" style="padding:18px;">
        <h3>Infos de contact</h3>
        <p class="meta">Email: contact@media360.ht</p>
        <p class="meta">Téléphone: +509 0000 0000</p>
        <p class="meta">Réseaux: Facebook • X • Instagram</p>
      </div>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php';
