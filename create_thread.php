<?php

require_once 'bootstrap.php';
requireLogin();

$categories = $db->getCategories();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateToken($_POST['token'] ?? '')) {
        $error = 'Invalid request';
    } else {
        $title = $_POST['title'] ?? '';
        $body = $_POST['body'] ?? '';
        $category_id = (int)($_POST['category_id'] ?? 0);
        
        if ($titleError = validateTitle($title)) {
            $error = $titleError;
        } elseif ($bodyError = validateBody($body)) {
            $error = $bodyError;
        } elseif (!$category_id) {
            $error = 'Select a category';
        } else {
            $db->createThread($_SESSION['user_id'], $category_id, trim($title), trim($body));
            header('Location: index.php');
            exit;
        }
    }
}
    
?>
  
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Thread - DoppelServe-Forum</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>DoppelServe-Forum</h1>
        <p>by doppelserve.com</p>
    </header>
    
    <nav>
        <a href="index.php">Home</a> | <a href="create_thread.php">New Thread</a> | <a href="logout.php">Logout</a>
    </nav>
    
    <h2>Create Thread</h2>
    
<?php 
 if (isset($error)) {
    echo showError($error); 
    }
    ?>
    
    <form method="post">
        <input type="hidden" name="token" value="<?= generateToken() ?>">
        <label>Category:
            <select name="category_id" required>
                <option value="">Select category</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= $category['id'] ?>"><?= sanitize($category['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </label><br>
        <label>Title: <input type="text" name="title" required></label><br>
        <label>Body: <textarea name="body" required></textarea></label><br>
        <button type="submit">Create</button>
    </form>
    
    <footer>
        <p>&copy; <?= date('Y') ?> doppelserve.com - DoppelServe-Forum</p>
    </footer>
</body>
</html>
