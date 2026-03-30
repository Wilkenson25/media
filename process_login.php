<?php
require_once __DIR__ . '/config/db.php';

@ini_set('session.save_path', sys_get_temp_dir());
session_start();
$pdo = db();

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '') {
    header('Location: ' . base_url('login.php?error=Champs%20obligatoires'));
    exit;
}

if (!$pdo) {
    header('Location: ' . base_url('login.php?error=DB%20indisponible'));
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT u.id, u.name, u.email, u.password, u.role_id, r.name AS role_name FROM users u LEFT JOIN roles r ON r.id = u.role_id WHERE u.email = :email LIMIT 1");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'] ?? '')) {
        $_SESSION['admin_user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role_id' => $user['role_id'],
            'role' => $user['role_name'],
        ];
        header('Location: ' . base_url('admin/dashboard.php'));
        exit;
    }
} catch (Throwable $e) {
    // fall through
}

header('Location: ' . base_url('login.php?error=Identifiants%20invalides'));
exit;
