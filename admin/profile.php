<?php
require_once __DIR__ . '/includes/header.php';

$user = admin_user();
$userId = (int)($user['id'] ?? ($_GET['id'] ?? 0));

if ($userId <= 0) {
    echo '<div class="card" style="padding:12px;">Veuillez ouvrir la page avec un identifiant. Ex: profile.php?id=1</div>';
    include __DIR__ . '/includes/footer.php';
    exit;
}

$pdo = db();
$error = '';
$success = '';
$profile = null;
$team = null;

if ($pdo) {
    try {
        $stmt = $pdo->prepare("SELECT id, name, email, profile_image FROM users WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $userId]);
        $profile = $stmt->fetch();

        $stmt = $pdo->prepare("SELECT id, user_id, name, role, photo, bio, social FROM equipe WHERE user_id = :id LIMIT 1");
        $stmt->execute([':id' => $userId]);
        $team = $stmt->fetch();
    } catch (Throwable $e) {
        $error = 'Erreur de lecture des données (vérifie la table equipe).';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $image_url = trim($_POST['image_url'] ?? '');
    $team_role = trim($_POST['team_role'] ?? '');
    $team_photo = trim($_POST['team_photo'] ?? '');
    $team_bio = trim($_POST['team_bio'] ?? '');
    $team_social = trim($_POST['team_social'] ?? '');

    if ($name === '' || $email === '') {
        $error = 'Nom et email obligatoires.';
    } else {
        $imagePath = $profile['profile_image'] ?? '';
        if ($image_url !== '') {
            $imagePath = $image_url;
        }
        if (!empty($_FILES['image_file']['name'])) {
            $ext = pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION);
            $filename = 'profile_' . time() . '.' . $ext;
            $dest = __DIR__ . '/../uploads/' . $filename;
            if (move_uploaded_file($_FILES['image_file']['tmp_name'], $dest)) {
                $imagePath = base_url('uploads/' . $filename);
            }
        }

        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("UPDATE users SET name = :name, email = :email, profile_image = :profile_image WHERE id = :id");
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':profile_image' => $imagePath,
                ':id' => $userId,
            ]);

            // Upsert equipe
            if ($team) {
                $stmt = $pdo->prepare("UPDATE equipe SET name = :name, role = :role, photo = :photo, bio = :bio, social = :social WHERE user_id = :user_id");
                $stmt->execute([
                    ':name' => $name,
                    ':role' => $team_role !== '' ? $team_role : ($team['role'] ?? 'Membre'),
                    ':photo' => $team_photo !== '' ? $team_photo : ($team['photo'] ?? null),
                    ':bio' => $team_bio !== '' ? $team_bio : ($team['bio'] ?? null),
                    ':social' => $team_social !== '' ? $team_social : ($team['social'] ?? null),
                    ':user_id' => $userId,
                ]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO equipe (user_id, name, role, photo, bio, social) VALUES (:user_id, :name, :role, :photo, :bio, :social)");
                $stmt->execute([
                    ':user_id' => $userId,
                    ':name' => $name,
                    ':role' => $team_role !== '' ? $team_role : 'Membre',
                    ':photo' => $team_photo !== '' ? $team_photo : null,
                    ':bio' => $team_bio !== '' ? $team_bio : null,
                    ':social' => $team_social !== '' ? $team_social : null,
                ]);
            }

            $pdo->commit();
            $success = 'Profil mis à jour.';
        } catch (Throwable $e) {
            $pdo->rollBack();
            $error = 'Erreur de mise à jour.';
        }

        $stmt = $pdo->prepare("SELECT id, name, email, profile_image FROM users WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $userId]);
        $profile = $stmt->fetch();

        $stmt = $pdo->prepare("SELECT id, user_id, name, role, photo, bio, social FROM equipe WHERE user_id = :id LIMIT 1");
        $stmt->execute([':id' => $userId]);
        $team = $stmt->fetch();
    }
}
?>

<h1>Mon profil</h1>
<?php if ($error !== ''): ?>
  <div class="card" style="padding:12px; border-color:#b3341a;"><?php echo e($error); ?></div>
<?php elseif ($success !== ''): ?>
  <div class="card" style="padding:12px;"><?php echo e($success); ?></div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data" class="card" style="padding:16px; max-width:520px;">
  <label class="meta">Nom</label>
  <input type="text" name="name" value="<?php echo e($profile['name'] ?? ''); ?>" style="width:100%; padding:8px; margin:6px 0 10px;" />

  <label class="meta">Email</label>
  <input type="email" name="email" value="<?php echo e($profile['email'] ?? ''); ?>" style="width:100%; padding:8px; margin:6px 0 10px;" />

  <label class="meta">Photo de profil (URL)</label>
  <input type="text" name="image_url" value="" placeholder="https://..." style="width:100%; padding:8px; margin:6px 0 10px;" />

  <label class="meta">Ou charger depuis l’appareil</label>
  <input type="file" name="image_file" style="margin:6px 0 10px;" />

  <?php if (!empty($profile['profile_image'])): ?>
    <img src="<?php echo e($profile['profile_image']); ?>" alt="Profil" style="max-width:120px; border-radius:8px; margin:6px 0 10px;" />
  <?php endif; ?>

  <hr style="margin:10px 0;" />
  <h3>Profil équipe</h3>
  <label class="meta">Rôle affiché</label>
  <input type="text" name="team_role" value="<?php echo e($team['role'] ?? ''); ?>" style="width:100%; padding:8px; margin:6px 0 10px;" />

  <label class="meta">Photo équipe (URL)</label>
  <input type="text" name="team_photo" value="<?php echo e($team['photo'] ?? ''); ?>" style="width:100%; padding:8px; margin:6px 0 10px;" />

  <label class="meta">Bio</label>
  <textarea name="team_bio" rows="3" style="width:100%; padding:8px; margin:6px 0 10px;"><?php echo e($team['bio'] ?? ''); ?></textarea>

  <label class="meta">Réseau social</label>
  <input type="text" name="team_social" value="<?php echo e($team['social'] ?? ''); ?>" style="width:100%; padding:8px; margin:6px 0 10px;" />

  <button class="btn" type="submit">Enregistrer</button>
</form>

<?php include __DIR__ . '/includes/footer.php';
