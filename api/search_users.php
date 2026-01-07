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
    // Pesquisa por nome ou email
    $sql = "SELECT u.id, u.nome, u.email, u.followers_count, u.following_count, p.bio, p.location
            FROM users u
            LEFT JOIN profiles p ON u.id = p.user_id
            WHERE u.nome LIKE ? OR u.email LIKE ?
            ORDER BY u.followers_count DESC
            LIMIT 20";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([$search_param, $search_param]);
    
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'users' => $users,
        'count' => count($users)
    ]);
    
} catch (PDOException $e) {
    error_log("search_users.php - Erro: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}

$conn = null;
?>