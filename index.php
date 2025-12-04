<?php
// Punto de entrada único para Railway
// Railway ejecuta: php -S 0.0.0.0:$PORT index.php

$request = $_SERVER['REQUEST_URI'];
$request_path = parse_url($request, PHP_URL_PATH);

// DEBUG: Ver qué se está solicitando (opcional)
// error_log("Request: " . $request_path);

// 1. Si es la API (crud.php)
if ($request_path === '/crud.php') {
    if (file_exists(__DIR__ . '/crud.php')) {
        include __DIR__ . '/crud.php';
    } else {
        header('Content-Type: application/json');
        http_response_code(404);
        echo json_encode(['error' => 'API file not found']);
    }
    exit;
}

// 2. Si es un archivo estático (CSS, JS, etc.)
if (preg_match('/\.(css|js|png|jpg|jpeg|gif|ico|svg)$/', $request_path)) {
    $file_path = __DIR__ . $request_path;
    if (file_exists($file_path)) {
        $ext = pathinfo($request_path, PATHINFO_EXTENSION);
        if ($ext === 'css') {
            header('Content-Type: text/css');
        } elseif ($ext === 'js') {
            header('Content-Type: application/javascript');
        }
        readfile($file_path);
        exit;
    }
}

// 3. Si es el favicon (evitar error común)
if ($request_path === '/favicon.ico') {
    header('Content-Type: image/x-icon');
    echo '';
    exit;
}

// 4. Todo lo demás: mostrar el frontend
if (file_exists(__DIR__ . '/index.html')) {
    include __DIR__ . '/index.html';
} else {
    header('Content-Type: text/html; charset=utf-8');
    echo '<!DOCTYPE html><html><head><title>Registro Eventos</title></head>';
    echo '<body><h1>Sistema de Registro para Eventos</h1>';
    echo '<p>Backend PHP funcionando correctamente.</p>';
    echo '<p><a href="/crud.php?test=1">Probar API</a></p>';
    echo '</body></html>';
}
?>