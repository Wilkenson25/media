<?php
// Basic DB connection. Update with your real credentials or .env values.
$dbHost = getenv('DB_HOST') ?: 'localhost';
$dbName = getenv('DB_NAME') ?: 'media_db';
$dbUser = getenv('DB_USER') ?: 'root';
$dbPass = getenv('DB_PASS') ?: '';
$dbCharset = 'utf8mb4';

$baseUrl = getenv('BASE_URL');
if (!$baseUrl) {
    $baseUrl = '/media';
}
$baseUrl = '/' . trim($baseUrl, '/');
if ($baseUrl === '/') {
    $baseUrl = '';
}

$pdo = null;
try {
    $dsn = "mysql:host={$dbHost};dbname={$dbName};charset={$dbCharset}";
    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (Throwable $e) {
    // In production, log this error instead of showing it.
    $pdo = null;
}

function db()
{
    global $pdo;
    return $pdo;
}

function base_url($path = '')
{
    global $baseUrl;
    $path = ltrim($path, '/');
    return $baseUrl . '/' . $path;
}
