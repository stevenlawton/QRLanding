<?php

// --- Security headers (sent on every response) ---
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');

// --- Read and validate tag ---
$tag = strtolower($_GET['tag'] ?? '');

if ($tag === '') {
    http_response_code(400);
    echo '<!doctype html><title>400 Bad Request</title><h1>400 Bad Request</h1><p>Missing tag.</p>';
    exit;
}

if (!preg_match('/^[a-zA-Z0-9_-]+$/', $tag)) {
    http_response_code(400);
    echo '<!doctype html><title>400 Bad Request</title><h1>400 Bad Request</h1><p>Invalid tag format.</p>';
    exit;
}

// --- Load redirects ---
$path = __DIR__ . '/redirects.json';

if (!file_exists($path)) {
    error_log('QRLanding: redirects.json not found');
    http_response_code(500);
    echo '<!doctype html><title>500 Internal Server Error</title><h1>500 Internal Server Error</h1><p>Server misconfiguration.</p>';
    exit;
}

$json = file_get_contents($path);
$redirects = json_decode($json, true);

if (!is_array($redirects)) {
    error_log('QRLanding: redirects.json failed to parse — ' . json_last_error_msg());
    http_response_code(500);
    echo '<!doctype html><title>500 Internal Server Error</title><h1>500 Internal Server Error</h1><p>Server misconfiguration.</p>';
    exit;
}

// --- Look up tag ---
if (!isset($redirects[$tag])) {
    http_response_code(404);
    echo '<!doctype html><title>404 Not Found</title><h1>404 Not Found</h1><p>Unknown tag: ' . htmlspecialchars($tag, ENT_QUOTES, 'UTF-8') . '</p>';
    exit;
}

// --- Validate destination URL ---
$url = $redirects[$tag];

if (!preg_match('~^https?://~i', $url)) {
    error_log('QRLanding: invalid URL for tag "' . $tag . '"');
    http_response_code(500);
    echo '<!doctype html><title>500 Internal Server Error</title><h1>500 Internal Server Error</h1><p>Server misconfiguration.</p>';
    exit;
}

// --- Redirect ---
header('Cache-Control: no-cache');
header('Location: ' . $url, true, 302);
exit;
