<?php
session_start();
include '../config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    die(json_encode(['error' => 'User not authenticated']));
}

$user_id = $_SESSION['user_id'];

try {
    // A query para buscar os usuários que o usuário logado segue
    $sql = "SELECT u.id, u.nome, p.avatar_url 
            FROM followers f 
            JOIN users u ON f.following_id = u.id
            LEFT JOIN profiles p ON u.id = p.user_id
            WHERE f.follower_id = :user_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['user_id' => $user_id]);
    
    $conversations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($conversations);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}

$conn = null;
?>