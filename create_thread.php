<?php

require_once 'bootstrap.php';
requireLogin();

// Get category_id from URL if provided
$selected_category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : null;

// Redirect to home if no category specified
if (!$selected_category_id) {
    header('Location: index.php');
    exit;
}

$categories = $db->getCategories();

// Keep form data on validation error
$title = '';
$body = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateToken($_POST['token'] ?? '')) {
        $error = 'Invalid request';
    } else {
        $title = $_POST['title'] ?? '';
        $body = $_POST['body'] ?? '';
        $category_id = (int)($_POST['category_id'] ?? 0);
        
        $errors = [];
        
        if ($titleError = validateTitle($title)) {
            $errors[] = $titleError;
        }
        if ($bodyError = validateBody($body)) {
            $errors[] = $bodyError;
        }
        if (!$category_id) {
            $errors[] = 'Select a category';
        }
        
        if (empty($errors)) {
            $thread_id = $db->createThread($_SESSION['user_id'], $category_id, trim($title), trim($body));
            header('Location: category.php?id=' . $category_id);
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
    
if (isset($errors) && !empty($errors)) {
    foreach ($errors as $err) {
        echo showError($err);
    }
}
    ?>
    
    <form method="post">
        <input type="hidden" name="token" value="<?= generateToken() ?>">
        <input type="hidden" name="category_id" value="<?= $selected_category_id ?>">
        <?php
        // Get the selected category for display
        $selected_category = null;
        foreach ($categories as $category) {
            if ($category['id'] == $selected_category_id) {
                $selected_category = $category;
                break;
            }
        }
        ?>
        <p><strong>Category:</strong> <?= sanitize($selected_category['name']) ?></p>
        <label>Title: <input type="text" name="title" value="<?= sanitize($title) ?>" required></label><br>
        <label>Body: <textarea name="body" required><?= sanitize($body) ?></textarea></label><br>
        <button type="submit">Create</button>
    </form>
    
    <footer>
        <p>&copy; <?= date('Y') ?> doppelserve.com - DoppelServe-Forum</p>
    </footer>
</body>
</html>