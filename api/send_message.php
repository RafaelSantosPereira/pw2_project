<?php
session_start();
include '../config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    die(json_encode(['error' => 'User not authenticated']));
}

$data = json_decode(file_get_contents('php://input'), true);

if (empty($data['receiver_id']) || empty($data['message_text'])) {
    http_response_code(400);
    die(json_encode(['error' => 'receiver_id and message_text are required']));
}

$sender_id = $_SESSION['user_id'];
$receiver_id = $data['receiver_id'];
$message_text = $data['message_text'];

try {
    $sql = "INSERT INTO messages (sender_id, receiver_id, message_text) VALUES (:sender_id, :receiver_id, :message_text)";
    $stmt = $conn->prepare($sql);
    
    $stmt->execute([
        'sender_id' => $sender_id,
        'receiver_id' => $receiver_id,
        'message_text' => $message_text
    ]);
    
    // Retorna a mensagem recém-criada
    $last_id = $conn->lastInsertId();
    $select_sql = "SELECT * FROM messages WHERE message_id = :last_id";
    $select_stmt = $conn->prepare($select_sql);
    $select_stmt->execute(['last_id' => $last_id]);
    $newMessage = $select_stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode($newMessage);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}

$conn = null;
?>