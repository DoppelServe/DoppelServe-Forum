<?php

/**
 * Checks if a user is logged in.
 *
 * @return bool True if the user is logged in (i.e., 'user_id' is set in the session), false otherwise.
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Ensures that a user is logged in, redirecting to the login page if not.
 *
 * If the user is not logged in (i.e., 'user_id' is not set in the session),
 * redirects to 'login.php' and terminates the script execution.
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

/**
 * Logs in a user by setting session variables and regenerating the session ID.
 *
 * @param string $username The username of the user logging in.
 * @param int|string $userId The unique identifier of the user.
 *
 * Sets 'user_id' and 'username' in the session and regenerates the session ID
 * for security purposes (e.g., to prevent session fixation attacks).
 */
function login($username, $userId) {
    $_SESSION['user_id'] = $userId;
    $_SESSION['username'] = $username;
    session_regenerate_id(true);
}

/**
 * Logs out the current user by clearing the session data and destroying the session.
 *
 * Unsets all session variables and destroys the session, effectively logging out the user.
 */
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

/**
 * Validates a username.
 *
 * @param string $username The username to validate.
 * @return string|null An error message if validation fails, null otherwise.
 *
 * The username must be between 3 and 16 characters long and contain only letters.
 * Custom error messages:
 * - 'error': Used for length validations (minimum and maximum).
 * - 'patternError': Used for pattern validation (letters only).
 */
function validateUsername($username) {
    return validateInput($username, [
        'min' => 3,
        'max' => 16,
        'pattern' => '/^[a-zA-Z]+$/',
        'patternError' => 'Username must be letters only',
        'error' => 'Username must be 3-16 characters'
    ]);
}

/**
 * Validates a password.
 *
 * @param string $password The password to validate.
 * @return string|null An error message if validation fails, null otherwise.
 *
 * The password must be at least 8 characters long and meet complexity requirements
 * (e.g., uppercase, lowercase, number, special character), as enforced by validateInput.
 * Custom error message:
 * - 'error': Used for minimum length validation.
 * Note: Complexity validation error messages are hardcoded in validateInput.
 */
function validatePassword($password) {
    return validateInput($password, [
        'min' => 8,
        'complex' => true,
        'error' => 'Password must be at least 8 characters'
    ]);
}

/**
 * Validates a title.
 *
 * @param string $title The title to validate.
 * @return string|null An error message if validation fails, null otherwise.
 *
 * The title must be between 3 and 255 characters long.
 * Custom error message:
 * - 'error': Used for length validations (minimum and maximum).
 */
function validateTitle($title) {
    return validateInput($title, [
        'min' => 3,
        'max' => 255,
        'error' => 'Title must be 3-255 characters'
    ]);
}

/**
 * Validates a body text.
 *
 * @param string $body The body text to validate.
 * @return string|null An error message if validation fails, null otherwise.
 *
 * The body must be at least 10 characters long.
 * Custom error message:
 * - 'error': Used for minimum length validation.
 */
function validateBody($body) {
    return validateInput($body, [
        'min' => 10,
        'error' => 'Body must be at least 10 characters'
    ]);
}

/**
 * Generates a session token for security purposes (e.g., CSRF protection).
 *
 * If a token does not already exist in the session, it generates a new one using
 * cryptographically secure random_bytes and stores it in $_SESSION['token'].
 *
 * @return string The generated or existing session token.
 */
function generateToken() {
    if (empty($_SESSION['token'])) {
        $_SESSION['token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['token'];
}

/**
 * Validates a provided token against the session token.
 *
 * Performs a timing-safe comparison using hash_equals to prevent timing attacks.
 *
 * @param string $token The token to validate.
 * @return bool True if the token matches the session token, false otherwise.
 */
function validateToken($token) {
    return isset($_SESSION['token']) && hash_equals($_SESSION['token'], $token);
}

/**
 * Sanitizes input to prevent XSS attacks.
 *
 * Trims whitespace and converts special characters to HTML entities using
 * htmlspecialchars with ENT_QUOTES to escape both single and double quotes.
 *
 * @param string $input The input to sanitize.
 * @return string The sanitized input.
 */
function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Displays an error message in a formatted HTML div.
 *
 * Sanitizes the message using the sanitize function to prevent XSS before wrapping
 * it in a div with class "error".
 *
 * @param string $message The error message to display.
 * @return string The HTML string containing the sanitized error message.
 */
function showError($message) {
    return '<div class="error">' . sanitize($message) . '</div>';
}
