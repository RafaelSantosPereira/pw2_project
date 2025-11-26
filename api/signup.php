<?php
header('Content-Type: application/json');
require_once '../config.php';
require_once '../auth.php';

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

// Registrar usuário
$result = $auth->signup(
    $data['name'] ?? '',
    $data['email'] ?? '',
    $data['password'] ?? ''
);

echo json_encode($result);
?>
