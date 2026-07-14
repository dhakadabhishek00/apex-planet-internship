<?php $user = current_user(); ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($title ?? 'Blog App') ?></title>
  <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
  <header class="topbar">
    <a class="brand" href="index.php">Blog App</a>
    <nav>
      <a href="index.php">Posts</a>
      <?php if ($user): ?>
        <?php if (can_create_post()): ?>
          <a href="post_form.php">New Post</a>
        <?php endif; ?>
        <span><?= e($user['username']) ?> (<?= e($user['role']) ?>)</span>
        <a href="logout.php">Logout</a>
      <?php else: ?>
        <a href="login.php">Login</a>
        <a href="register.php">Register</a>
      <?php endif; ?>
    </nav>
  </header>
  <main class="container">
    <?php if ($message = flash('success')): ?>
      <div class="alert success"><?= e($message) ?></div>
    <?php endif; ?>
    <?php if ($message = flash('error')): ?>
      <div class="alert error"><?= e($message) ?></div>
    <?php endif; ?>

