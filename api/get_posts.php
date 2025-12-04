<?php
header('Content-Type: application/json');
require_once '../config.php';
require_once '../posts.php';

// Apenas GET é permitido
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

// Obter parâmetros
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20; // quantidade de posts a retornar
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0; // de onde começar neste caso pelo primeiro post

// Validar parâmetros
if ($limit < 1 || $limit > 100) $limit = 20;
if ($offset < 0) $offset = 0;

// Obter todos os posts
$result = $posts->getAllPosts($limit, $offset);

echo json_encode($result);
?>
