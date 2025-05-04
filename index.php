<?php

require_once 'bootstrap.php';

$categories = $db->getCategories();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>DoppelServe-Forum</title>
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
            | <a href="logout.php">Logout</a>
        <?php else: ?>
            | <a href="login.php">Login</a>
            | <a href="register.php">Register</a>
        <?php endif; ?>
    </nav>

    <h2>Categories</h2>
    <div class="categories">
        <?php foreach ($categories as $category): ?>
            <div class="category">
                <h3><a href="category.php?id=<?= $category['id'] ?>"><?= sanitize($category['name']) ?></a></h3>
                <p><?= sanitize($category['description']) ?></p>
            </div>
        <?php endforeach; ?>
    </div>
    
    <footer>
        <p>&copy; <?= date('Y') ?> doppelserve.com - DoppelServe-Forum</p>
    </footer>
</body>
</html>
