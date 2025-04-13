<?php
// preview.php

if (!isset($_GET['url'])) {
    http_response_code(400);
    echo json_encode(['error' => 'No URL provided']);
    exit;
}

$url = $_GET['url'];

// Validate and sanitize URL
if (!filter_var($url, FILTER_VALIDATE_URL)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid URL']);
    exit;
}

// Fetch content (timeout & user-agent recommended)
$options = [
    'http' => [
        'method' => "GET",
        'header' => "User-Agent: LinkPreviewBot/1.0\r\n",
        'timeout' => 5
    ]
];
$context = stream_context_create($options);
$html = @file_get_contents($url, false, $context);

if ($html === FALSE) {
    http_response_code(500);
    echo json_encode(['error' => 'Error while trying to fetch URL']);
    exit;
}

// Parse HTML
libxml_use_internal_errors(true);
$doc = new DOMDocument();
$doc->loadHTML($html);
$xpath = new DOMXPath($doc);

$sitename = '';
$title = '';
$description = '';
$image = '';

// Try to get OG tags
$ogSiteName = $xpath->query('//meta[@property="og:site_name"]')->item(0);
if ($ogSiteName) $sitename = $ogSiteName->getAttribute('content');

$ogTitle = $xpath->query('//meta[@property="og:title"]')->item(0);
if ($ogTitle) $title = $ogTitle->getAttribute('content');

$ogDesc = $xpath->query('//meta[@property="og:description"]')->item(0);
if ($ogDesc) $description = $ogDesc->getAttribute('content');

$ogImage = $xpath->query('//meta[@property="og:image"]')->item(0);
if ($ogImage) $image = $ogImage->getAttribute('content');

// Fallback to <title>
if (empty($title)) {
    $titleNode = $doc->getElementsByTagName("title")->item(0);
    if ($titleNode) $title = $titleNode->nodeValue;
}

echo json_encode([
    'sitename' => $sitename,
    'title' => $title,
    'description' => $description,
    'image' => $image
]);