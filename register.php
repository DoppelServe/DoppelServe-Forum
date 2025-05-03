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
        } elseif ($err = validatePassword($password)) {
            $error = $err;
        } else {
            $exists = $db->getUserByUsername(trim($username));
            
            if ($exists) {
                $error = 'Username taken';
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $db->createUser(trim($username), $hash);
                header('Location: login.php');
                exit;
            }
        }
    }
}
    
?>
  
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - DoppelServe-Forum</title>
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
    
    <h2>Register</h2>
    
<?php
    if (isset($error)) {
        echo showError($error);
    }
        ?>
    
    <form method="post">
        <input type="hidden" name="token" value="<?= generateToken() ?>">
        <label>Username: <input type="text" name="username" required></label><br>
        <p class="help">3-16 letters only</p>
        <label>Password: <input type="password" name="password" required></label><br>
        <p class="help">8+ chars, must include uppercase, lowercase, number, special char</p>
        <button type="submit">Register</button>
    </form>
    
    <footer>
        <p>&copy; <?= date('Y') ?> doppelserve.com - DoppelServe-Forum</p>
    </footer>
</body>
</html>
