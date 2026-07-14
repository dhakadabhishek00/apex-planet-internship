<?php

require_once __DIR__ . '/../app/bootstrap.php';

require_login();
verify_csrf();

$id = (int) ($_POST['id'] ?? 0);

$stmt = db()->prepare('SELECT * FROM posts WHERE id = :id');
$stmt->execute(['id' => $id]);
$post = $stmt->fetch();

if (!$post || !can_manage_post($post)) {
    http_response_code(403);
    exit('You do not have permission to delete this post.');
}

$stmt = db()->prepare('DELETE FROM posts WHERE id = :id');
$stmt->execute(['id' => $id]);
flash('success', 'Post deleted.');
redirect('index.php');

