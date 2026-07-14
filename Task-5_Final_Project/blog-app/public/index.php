<?php

require_once __DIR__ . '/../app/bootstrap.php';

$title = 'Posts';
$perPage = 5;
$page = max(1, (int) ($_GET['page'] ?? 1));
$search = trim((string) ($_GET['search'] ?? ''));
$offset = ($page - 1) * $perPage;
$params = [];
$where = '';

if ($search !== '') {
    $where = 'WHERE posts.title LIKE :search OR posts.content LIKE :search';
    $params['search'] = '%' . $search . '%';
}

$countStmt = db()->prepare("SELECT COUNT(*) FROM posts $where");
$countStmt->execute($params);
$total = (int) $countStmt->fetchColumn();
$pages = max(1, (int) ceil($total / $perPage));

$sql = "SELECT posts.*, users.username
        FROM posts
        JOIN users ON users.id = posts.user_id
        $where
        ORDER BY posts.created_at DESC
        LIMIT :limit OFFSET :offset";
$stmt = db()->prepare($sql);
foreach ($params as $key => $value) {
    $stmt->bindValue(':' . $key, $value, PDO::PARAM_STR);
}
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$posts = $stmt->fetchAll();

require_once __DIR__ . '/../app/layout/header.php';
?>

<div class="toolbar">
  <h1>Posts</h1>
  <form class="search" method="get">
    <input type="search" name="search" placeholder="Search posts" value="<?= e($search) ?>">
    <button type="submit">Search</button>
  </form>
</div>

<?php if (!$posts): ?>
  <div class="card">No posts found.</div>
<?php endif; ?>

<?php foreach ($posts as $post): ?>
  <article class="card">
    <h2><?= e($post['title']) ?></h2>
    <p class="meta">By <?= e($post['username']) ?> on <?= e($post['created_at']) ?></p>
    <p><?= nl2br(e($post['content'])) ?></p>
    <?php if (can_manage_post($post)): ?>
      <div class="actions">
        <a class="button secondary" href="post_form.php?id=<?= (int) $post['id'] ?>">Edit</a>
        <form method="post" action="delete_post.php" onsubmit="return confirm('Delete this post?');">
          <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
          <input type="hidden" name="id" value="<?= (int) $post['id'] ?>">
          <button class="danger" type="submit">Delete</button>
        </form>
      </div>
    <?php endif; ?>
  </article>
<?php endforeach; ?>

<div class="pagination">
  <?php for ($i = 1; $i <= $pages; $i++): ?>
    <?php if ($i === $page): ?>
      <span class="active"><?= $i ?></span>
    <?php else: ?>
      <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
    <?php endif; ?>
  <?php endfor; ?>
</div>

<?php require_once __DIR__ . '/../app/layout/footer.php'; ?>

