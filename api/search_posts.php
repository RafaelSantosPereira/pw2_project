<?php
session_start();
include '../config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    die(json_encode(['error' => 'User not authenticated']));
}

if (!isset($_GET['q']) || empty(trim($_GET['q']))) {
    http_response_code(400);
    die(json_encode(['error' => 'Search query is required']));
}

$search_query = trim($_GET['q']);
$search_param = "%{$search_query}%";

try {
    // Pesquisa posts pelo conteúdo
    $sql = "SELECT p.post_id, p.user_id, p.content, p.likes_count, p.comments_count, p.created_at, u.nome
            FROM posts p
            JOIN users u ON p.user_id = u.id
            WHERE p.content LIKE ?
            ORDER BY p.created_at DESC
            LIMIT 50";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([$search_param]);
    
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'posts' => $posts,
        'count' => count($posts)
    ]);
    
} catch (PDOException $e) {
    error_log("search_posts.php - Erro: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}

$conn = null;
?>