<?php

require 'bootstrap.php';

$category_id = (int)($_GET['id'] ?? 0);
if (!$category_id) {
    header('Location: index.php');
    exit;
}

$category = $db->getCategoryById($category_id);
if (!$category) {
    header('Location: index.php');
    exit;
}

$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$threads = $db->getThreadsByCategory($category_id, $limit, $offset);
$totalThreads = $db->countThreadsByCategory($category_id);
$totalPages = ceil($totalThreads / $limit);

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?= sanitize($category['name']) ?> - DoppelServe-Forum</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>DoppelServe-Forum</h1>
        <p>by doppelserve.com</p>
    </header>
    
    <nav>
        <a href="index.php">Home</a>
        <?php if (isLoggedIn()): ?>
            | <a href="create_thread.php">New Thread</a>
            | <a href="logout.php">Logout</a>
        <?php else: ?>
            | <a href="login.php">Login</a>
            | <a href="register.php">Register</a>
        <?php endif; ?>
    </nav>
    
    <h2><?= sanitize($category['name']) ?></h2>
    <p><?= sanitize($category['description']) ?></p>
    
    <?php foreach ($threads as $thread): ?>
        <div class="thread">
            <h3><a href="view_thread.php?id=<?= $thread['id'] ?>"><?= sanitize($thread['title']) ?></a></h3>
            <p>By <?= sanitize($thread['username']) ?> | <?= date('M j, Y', strtotime($thread['created_at'])) ?></p>
        </div>
    <?php endforeach; ?>
    
    <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <?php if ($i == $page): ?>
                    <span><?= $i ?></span>
                <?php else: ?>
                    <a href="?id=<?= $category_id ?>&page=<?= $i ?>"><?= $i ?></a>
                <?php endif; ?>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
    
    <footer>
        <p>&copy; <?= date('Y') ?> doppelserve.com - DoppelServe-Forum</p>
    </footer>
</body>
</html>
