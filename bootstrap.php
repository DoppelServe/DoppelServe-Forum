<?php

session_start();
require_once 'config.php';
require_once 'database.php';
require_once 'functions.php';

header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: no-referrer");
header("Content-Security-Policy: default-src 'self'");
header("Permissions-Policy: interest-cohort=()");

$db = new Database($pdo);
