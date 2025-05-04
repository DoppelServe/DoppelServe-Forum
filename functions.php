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

/**
 * Checks if the input meets the minimum length requirement.
 *
 * @param string $input The input string to check.
 * @param int $min The minimum length required.
 * @param string|null $error Optional custom error message.
 * @return string|null Error message if validation fails, null otherwise.
 */
function checkMinLength($input, $min, $error = null) {
    if (strlen($input) < $min) {
        if ($error !== null) {
            return sprintf($error, $min);
        } else {
            return "Must be at least $min characters";
        }
    }
    return null;
}

/**
 * Checks if the input exceeds the maximum length requirement.
 *
 * @param string $input The input string to check.
 * @param int $max The maximum length allowed.
 * @param string|null $error Optional custom error message.
 * @return string|null Error message if validation fails, null otherwise.
 */
function checkMaxLength($input, $max, $error = null) {
    if (strlen($input) > $max) {
        if ($error !== null) {
            return sprintf($error, $max);
        } else {
            return "Must be no more than $max characters";
        }
    }
    return null;
}

/**
 * Checks if the input matches the specified regex pattern.
 *
 * @param string $input The input string to check.
 * @param string $pattern The regex pattern to match against.
 * @param string|null $patternError Optional custom error message.
 * @return string|null Error message if validation fails, null otherwise.
 */
function checkPattern($input, $pattern, $patternError = null) {
    if (!preg_match($pattern, $input)) {
        return $patternError ?? "Invalid format";
    }
    return null;
}

/**
 * Checks if the input meets the complexity requirements.
 *
 * @param string $input The input string to check.
 * @return string|null Error message if validation fails, null otherwise.
 */
function checkComplexity($input) {
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
    return null;
}

/**
 * Validates the input against the provided rules.
 *
 * @param string $input The input string to validate.
 * @param array $rules The validation rules.
 * @return string|null Error message if validation fails, null otherwise.
 */
function validateInput($input, $rules) {
    $input = trim($input);
    
    if (isset($rules['min'])) {
        $error = checkMinLength($input, $rules['min'], $rules['error'] ?? null);
        if ($error) {
            return $error;
        }
    }
    
    if (isset($rules['max'])) {
        $error = checkMaxLength($input, $rules['max'], $rules['error'] ?? null);
        if ($error) {
            return $error;
        }
    }
    
    if (isset($rules['pattern'])) {
        $error = checkPattern($input, $rules['pattern'], $rules['patternError'] ?? null);
        if ($error) {
            return $error;
        }
    }
    
    if (isset($rules['complex']) && $rules['complex']) {
        $error = checkComplexity($input);
        if ($error) {
            return $error;
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
