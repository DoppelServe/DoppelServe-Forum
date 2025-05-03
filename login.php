<?php

require_once 'bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateToken($_POST['token'] ?? '')) {
        $error = 'Invalid request';
    } else {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if ($err = validateUsername($username)) {
            $error = $err;
        } else {
            $user = $db->getUserByUsername(trim($username));
            
            if ($user && password_verify($password, $user['password'])) {
                login($user['username'], $user['id']);
                header('Location: index.php');
                exit;
            } else {
                $error = 'Invalid credentials';
            }
        }
    }
}
    
?>
  
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - DoppelServe-Forum</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>DoppelServe-Forum</h1>
        <p>by doppelserve.com</p>
    </header>
    
    <nav>
        <a href="index.php">Home</a> | <a href="login.php">Login</a> | <a href="register.php">Register</a>
    </nav>
    
    <h2>Login</h2>
    
    <?php if (isset($error)) echo showError($error); ?>
    
    <form method="post">
        <input type="hidden" name="token" value="<?= generateToken() ?>">
        <label>Username: <input type="text" name="username" required></label><br>
        <label>Password: <input type="password" name="password" required></label><br>
        <button type="submit">Login</button>
    </form>
    
    <footer>
        <p>&copy; <?= date('Y') ?> doppelserve.com - DoppelServe-Forum</p>
    </footer>
</body>
</html>
