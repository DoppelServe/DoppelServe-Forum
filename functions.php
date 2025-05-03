<?php

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function login($username, $userId) {
    $_SESSION['user_id'] = $userId;
    $_SESSION['username'] = $username;
    session_regenerate_id(true);
}

function logout() {
    $_SESSION = [];
    session_destroy();
}

function validateInput($input, $rules) {
    $input = trim($input);
    
    if (isset($rules['min']) && strlen($input) < $rules['min']) {
        return sprintf($rules['error'] ?? "Must be at least %d characters", $rules['min']);
    }
    if (isset($rules['max']) && strlen($input) > $rules['max']) {
        return sprintf($rules['error'] ?? "Must be no more than %d characters", $rules['max']);
    }
    
    if (isset($rules['pattern']) && !preg_match($rules['pattern'], $input)) {
        return $rules['patternError'] ?? "Invalid format";
    }
    
    if (isset($rules['complex']) && $rules['complex']) {
        $patterns = [
            '/[A-Z]/' => 'uppercase letter',
            '/[a-z]/' => 'lowercase letter',
            '/[0-9]/' => 'number',
            '/[^A-Za-z0-9]/' => 'special character'
        ];
        $missing = [];
        foreach ($patterns as $pattern => $type) {
            if (!preg_match($pattern, $input)) {
                $missing[] = $type;
            }
        }
        if ($missing) {
            return "Password needs: " . implode(', ', $missing);
        }
    }
    
    return null;
}

function validateUsername($username) {
    return validateInput($username, [
        'min' => 3,
        'max' => 16,
        'pattern' => '/^[a-zA-Z]+$/',
        'patternError' => 'Username must be letters only',
        'error' => 'Username must be 3-16 characters'
    ]);
}

function validatePassword($password) {
    return validateInput($password, [
        'min' => 8,
        'complex' => true,
        'error' => 'Password must be at least 8 characters'
    ]);
}

function validateTitle($title) {
    return validateInput($title, [
        'min' => 3,
        'max' => 255,
        'error' => 'Title must be 3-255 characters'
    ]);
}

function validateBody($body) {
    return validateInput($body, [
        'min' => 10,
        'error' => 'Body must be at least 10 characters'
    ]);
}

function generateToken() {
    if (empty($_SESSION['token'])) {
        $_SESSION['token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['token'];
}

function validateToken($token) {
    return isset($_SESSION['token']) && hash_equals($_SESSION['token'], $token);
}

function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function showError($message) {
    return '<div class="error">' . sanitize($message) . '</div>';
}
