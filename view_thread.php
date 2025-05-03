<?php
require 'bootstrap.php';

$thread_id = (int)($_GET['id'] ?? 0);
if (!$thread_id) {
    header('Location: index.php');
    exit;
}

$thread = $db->getThread($thread_id);

if (!$thread) {
    header('Location: index.php');
    exit;
}

$replies = $db->getRepliesByThread($thread_id);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?= sanitize($thread['title']) ?> - DoppelServe-Forum</title>
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
    
    <h2><?= sanitize($thread['title']) ?></h2>
    
    <div class="thread">
        <p><?= nl2br(sanitize($thread['body'])) ?></p>
        <p>In <?= sanitize($thread['category_name']) ?> | By <?= sanitize($thread['username']) ?> | <?= date('M j, Y', strtotime($thread['created_at'])) ?></p>
    </div>
    
    <h3>Replies</h3>
    
    <?php foreach ($replies as $reply): ?>
        <div class="reply">
            <p><?= nl2br(sanitize($reply['body'])) ?></p>
            <p>By <?= sanitize($reply['username']) ?> | <?= date('M j, Y', strtotime($reply['created_at'])) ?></p>
        </div>
    <?php endforeach; ?>
    
    <?php if (isLoggedIn()): ?>
        <h3>Post Reply</h3>
        <form method="post" action="reply.php">
            <input type="hidden" name="token" value="<?= generateToken() ?>">
            <input type="hidden" name="thread_id" value="<?= $thread_id ?>">
            <label>Reply: <textarea name="body" required></textarea></label><br>
            <button type="submit">Reply</button>
        </form>
    <?php else: ?>
        <p><a href="login.php">Login</a> to reply.</p>
    <?php endif; ?>
    
    <footer>
        <p>&copy; <?= date('Y') ?> doppelserve.com - DoppelServe-Forum</p>
    </footer>
</body>
</html>
