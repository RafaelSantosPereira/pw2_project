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

// Apenas DELETE é permitido
if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

// Obter dados JSON
$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data['post_id'])) {
    echo json_encode(['success' => false, 'message' => 'post_id é obrigatório']);
    exit;
}

// Apagar post
$result = $posts->deletePost(
    $data['post_id'],
    $_SESSION['user_id']
);

echo json_encode($result);
?>
