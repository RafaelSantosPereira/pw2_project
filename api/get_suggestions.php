<?php
session_start();
include '../config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    die(json_encode(['error' => 'User not authenticated']));
}

$current_user_id = (int)$_SESSION['user_id'];

try {
    // Busca users que o usuário atual NÃO segue e que não sejam ele mesmo
    // Ordena por número de seguidores (users mais populares primeiro)
    $sql = "SELECT u.id, u.nome, u.followers_count, u.following_count, p.bio, p.location,
            (SELECT COUNT(*) 
             FROM followers f1 
             WHERE f1.follower_id = :current_user_id 
             AND f1.following_id IN (
                 SELECT f2.following_id 
                 FROM followers f2 
                 WHERE f2.follower_id = u.id
             )) as mutual_friends
            FROM users u
            LEFT JOIN profiles p ON u.id = p.user_id
            WHERE u.id != :current_user_id2
            AND u.id NOT IN (
                SELECT following_id 
                FROM followers 
                WHERE follower_id = :current_user_id3
            )
            ORDER BY u.followers_count DESC, mutual_friends DESC
            LIMIT 10";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        'current_user_id' => $current_user_id,
        'current_user_id2' => $current_user_id,
        'current_user_id3' => $current_user_id
    ]);
    
    $suggestions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'suggestions' => $suggestions
    ]);
    
} catch (PDOException $e) {
    error_log("get_suggestions.php - Erro: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}

$conn = null;
?>