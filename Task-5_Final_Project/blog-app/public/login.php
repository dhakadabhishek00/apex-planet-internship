<?php

require_once __DIR__ . '/../app/bootstrap.php';

if (is_logged_in()) {
    redirect('index.php');
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    $stmt = db()->prepare('SELECT * FROM users WHERE email = :email');
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id' => (int) $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'role' => $user['role'],
        ];
        redirect('index.php');
    }

    $error = 'Invalid email or password.';
}

$title = 'Login';
require_once __DIR__ . '/../app/layout/header.php';
?>

<form class="form-panel" method="post">
  <h1>Login</h1>
  <?php if ($error): ?>
    <div class="alert error"><?= e($error) ?></div>
  <?php endif; ?>
  <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
  <label for="email">Email</label>
  <input id="email" name="email" type="email" required>
  <label for="password">Password</label>
  <input id="password" name="password" type="password" required>
  <button type="submit">Login</button>
</form>

<?php require_once __DIR__ . '/../app/layout/footer.php'; ?>

