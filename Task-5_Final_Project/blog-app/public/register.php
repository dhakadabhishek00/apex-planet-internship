<?php

require_once __DIR__ . '/../app/bootstrap.php';

if (is_logged_in()) {
    redirect('index.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $username = trim((string) ($_POST['username'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    if ($username === '' || strlen($username) < 3) {
        $errors[] = 'Username must be at least 3 characters.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Enter a valid email address.';
    }

    if (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters.';
    }

    if (!$errors) {
        $stmt = db()->prepare('SELECT id FROM users WHERE email = :email OR username = :username');
        $stmt->execute(['email' => $email, 'username' => $username]);

        if ($stmt->fetch()) {
            $errors[] = 'Username or email already exists.';
        } else {
            $stmt = db()->prepare('INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, :role)');
            $stmt->execute([
                'username' => $username,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'role' => 'user',
            ]);
            flash('success', 'Registration complete. Please log in.');
            redirect('login.php');
        }
    }
}

$title = 'Register';
require_once __DIR__ . '/../app/layout/header.php';
?>

<form class="form-panel" method="post">
  <h1>Register</h1>
  <?php foreach ($errors as $error): ?>
    <div class="alert error"><?= e($error) ?></div>
  <?php endforeach; ?>
  <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
  <label for="username">Username</label>
  <input id="username" name="username" required minlength="3">
  <label for="email">Email</label>
  <input id="email" name="email" type="email" required>
  <label for="password">Password</label>
  <input id="password" name="password" type="password" required minlength="8">
  <button type="submit">Create Account</button>
</form>

<?php require_once __DIR__ . '/../app/layout/footer.php'; ?>

