<?php

require_once 'bootstrap.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateToken($_POST['token'] ?? '')) {
        header('Location: index.php');
        exit;
    }
    
    $thread_id = (int)($_POST['thread_id'] ?? 0);
    $body = $_POST['body'] ?? '';
    
    if ($bodyError = validateBody($body)) {
        $_SESSION['error'] = $bodyError;
    } else {
        $db->createReply($thread_id, $_SESSION['user_id'], trim($body));
    }
    
    header("Location: view_thread.php?id=$thread_id");
    exit;
}

header('Location: index.php');
