<?php
session_start();
include '../config.php';

header('Content-Type: application/json');

// Log para debug
error_log("get_messages.php - Session user_id: " . ($_SESSION['user_id'] ?? 'não definido'));
error_log("get_messages.php - GET user_id: " . ($_GET['user_id'] ?? 'não definido'));

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    die(json_encode(['error' => 'User not authenticated']));
}

if (!isset($_GET['user_id']) || empty($_GET['user_id'])) {
    http_response_code(400);
    die(json_encode(['error' => 'user_id is required']));
}

$current_user_id = (int)$_SESSION['user_id'];
$other_user_id = (int)$_GET['user_id'];

// Validação adicional
if ($other_user_id <= 0) {
    http_response_code(400);
    die(json_encode(['error' => 'Invalid user_id']));
}

try {
    // Usando ? em vez de :parametros duplicados
    // A ordem dos valores no execute deve corresponder à ordem dos ? na query
    $sql = "SELECT message_id, sender_id, receiver_id, message_text, created_at 
            FROM messages 
            WHERE (sender_id = ? AND receiver_id = ?) 
               OR (sender_id = ? AND receiver_id = ?)
            ORDER BY created_at ASC";
            
    $stmt = $conn->prepare($sql);
    // Ordem: current_user, other_user, other_user, current_user
    $stmt->execute([$current_user_id, $other_user_id, $other_user_id, $current_user_id]);
    
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Se não houver mensagens, retorna array vazio
    if ($messages === false) {
        $messages = [];
    }
    
    error_log("get_messages.php - Mensagens encontradas: " . count($messages));
    
    echo json_encode($messages);
    
} catch (PDOException $e) {
    error_log("get_messages.php - Erro: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}

$conn = null;
?>