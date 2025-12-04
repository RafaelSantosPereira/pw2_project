<?php
header('Content-Type: application/json');

session_start();


if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Usuário não autenticado']);
    exit;
}

echo json_encode([
    'success' => true,
    'user_id' => $_SESSION['user_id'],
    'user_name' => $_SESSION['user_name'],
    'user_email' => $_SESSION['user_email']
]);
?>
