<?php
require_once __DIR__ . '/includes/header.php';
require_role(['admin', 'editor']);

$pdo = db();
$messages = [];

if ($pdo) {
    if (isset($_GET['delete'])) {
        $id = (int)$_GET['delete'];
        $pdo->prepare("DELETE FROM messages WHERE id = :id")->execute([':id' => $id]);
        header('Location: messages.php');
        exit;
    }

    $messages = $pdo->query("SELECT id, name, email, subject, message, created_at FROM messages ORDER BY created_at DESC")->fetchAll();
}
?>

<h1>Messages</h1>

<table class="card" style="width:100%; border-collapse:collapse;">
  <thead>
    <tr>
      <th style="text-align:left; padding:8px;">Nom</th>
      <th style="text-align:left; padding:8px;">Sujet</th>
      <th style="text-align:left; padding:8px;">Date</th>
      <th style="text-align:left; padding:8px;">Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($messages as $m): ?>
      <tr>
        <td style="padding:8px;"><?php echo e($m['name']); ?></td>
        <td style="padding:8px;"><?php echo e($m['subject']); ?></td>
        <td style="padding:8px;"><?php echo e($m['created_at']); ?></td>
        <td style="padding:8px;">
          <details>
            <summary>Voir</summary>
            <p class="meta">Email: <?php echo e($m['email']); ?></p>
            <p><?php echo e($m['message']); ?></p>
          </details>
          | <a href="messages.php?delete=<?php echo e($m['id']); ?>" onclick="return confirm('Supprimer ce message ?')">Supprimer</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php include __DIR__ . '/includes/footer.php';
