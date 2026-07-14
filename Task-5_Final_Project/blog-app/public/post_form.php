<?php

require_once __DIR__ . '/../app/bootstrap.php';

require_login();

if (!can_create_post()) {
    http_response_code(403);
    exit('You do not have permission to create posts.');
}

$id = (int) ($_GET['id'] ?? 0);
$post = ['title' => '', 'content' => '', 'user_id' => current_user()['id']];
$errors = [];

if ($id > 0) {
    $stmt = db()->prepare('SELECT * FROM posts WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $post = $stmt->fetch();

    if (!$post || !can_manage_post($post)) {
        http_response_code(403);
        exit('You do not have permission to edit this post.');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $titleValue = trim((string) ($_POST['title'] ?? ''));
    $content = trim((string) ($_POST['content'] ?? ''));

    if ($titleValue === '' || strlen($titleValue) > 180) {
        $errors[] = 'Title is required and must be 180 characters or fewer.';
    }

    if ($content === '' || strlen($content) < 10) {
        $errors[] = 'Content must be at least 10 characters.';
    }

    if (!$errors) {
        if ($id > 0) {
            $stmt = db()->prepare('UPDATE posts SET title = :title, content = :content WHERE id = :id');
            $stmt->execute(['title' => $titleValue, 'content' => $content, 'id' => $id]);
            flash('success', 'Post updated.');
        } else {
            $stmt = db()->prepare('INSERT INTO posts (user_id, title, content) VALUES (:user_id, :title, :content)');
            $stmt->execute([
                'user_id' => current_user()['id'],
                'title' => $titleValue,
                'content' => $content,
            ]);
            flash('success', 'Post created.');
        }

        redirect('index.php');
    }

    $post['title'] = $titleValue;
    $post['content'] = $content;
}

$title = $id > 0 ? 'Edit Post' : 'New Post';
require_once __DIR__ . '/../app/layout/header.php';
?>

<form class="form-panel" method="post">
  <h1><?= e($title) ?></h1>
  <?php foreach ($errors as $error): ?>
    <div class="alert error"><?= e($error) ?></div>
  <?php endforeach; ?>
  <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
  <label for="title">Title</label>
  <input id="title" name="title" required maxlength="180" value="<?= e($post['title']) ?>">
  <label for="content">Content</label>
  <textarea id="content" name="content" required minlength="10"><?= e($post['content']) ?></textarea>
  <button type="submit">Save Post</button>
  <a class="button secondary" href="index.php">Cancel</a>
</form>

<?php require_once __DIR__ . '/../app/layout/footer.php'; ?>

