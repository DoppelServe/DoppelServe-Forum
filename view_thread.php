<?php
require_once 'bootstrap.php';
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

// Pagination settings
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 3;
$offset = ($page - 1) * $limit;

// Get paginated replies
$replies = $db->getRepliesByThreadPaginated($thread_id, $limit, $offset);
$totalReplies = $db->countRepliesByThread($thread_id);
$totalPages = ceil($totalReplies / $limit);

// Check for reply error in session
$replyError = null;
if (isset($_SESSION['error'])) {
    $replyError = $_SESSION['error'];
    unset($_SESSION['error']);
}
// Check for saved reply body
$savedReplyBody = '';
if (isset($_SESSION['reply_body'])) {
    $savedReplyBody = $_SESSION['reply_body'];
    unset($_SESSION['reply_body']);
}
?>
<!DOCTYPE html>
<html lang="en">
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
    
    <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <?php if ($i == $page): ?>
                    <span><?= $i ?></span>
                <?php else: ?>
                    <a href="?id=<?= $thread_id ?>&page=<?= $i ?>"><?= $i ?></a>
                <?php endif; ?>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
    
    <?php if (isLoggedIn()): ?>
        <h3>Post Reply</h3>
        
        <?php if ($replyError): ?>
            <?= showError($replyError) ?>
        <?php endif; ?>
        
        <form method="post" action="reply.php">
            <input type="hidden" name="token" value="<?= generateToken() ?>">
            <input type="hidden" name="thread_id" value="<?= $thread_id ?>">
            <label>Reply: <textarea name="body" required><?= sanitize($savedReplyBody) ?></textarea></label><br>
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