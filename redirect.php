<?php

$tag = $_GET['tag'] ?? '';

if ($tag === '') {
    http_response_code(400);
    echo 'Missing tag.';
    exit;
}

$json = file_get_contents(__DIR__ . '/redirects.json');
$redirects = json_decode($json, true);

if (!is_array($redirects) || !isset($redirects[$tag])) {
    http_response_code(404);
    echo 'Unknown tag: ' . htmlspecialchars($tag, ENT_QUOTES, 'UTF-8');
    exit;
}

header('Location: ' . $redirects[$tag], true, 302);
exit;
