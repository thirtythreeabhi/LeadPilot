<?php
declare(strict_types=1);

$route = $_GET['route'] ?? '';
$handlers = [
    'bootstrap.php' => 'bootstrap.php',
    'analyze.php' => 'analyze.php',
    'generate.php' => 'generate.php',
    'review.php' => 'review.php',
    'templates.php' => 'templates.php',
];

if ($route === '') {
    require dirname(__DIR__) . '/public/index.php';
    return;
}

if (!is_string($route) || !isset($handlers[$route])) {
    http_response_code(404);
    header('Content-Type: application/json; charset=utf-8');
    echo '{"error":"Not found."}';
    return;
}

require dirname(__DIR__) . '/src/Api/' . $handlers[$route];
