<?php
header('Content-Type: application/json');
require_once '../config.php';
require_once '../posts.php';

session_start();

// Verificar se o utilizador está autenticado
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Utilizador não autenticado']);
    exit;
}

// Apenas POST é permitido
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

// Obter dados JSON
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
    exit;
}

// Criar novo post
$result = $posts->createPost(
    $_SESSION['user_id'],
    $data['content'] ?? ''
);

echo json_encode($result);
?>
