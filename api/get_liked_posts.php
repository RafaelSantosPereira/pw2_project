<?php
require_once '../config.php';

session_start();

// Verificar se o utilizador está autenticado
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'posts_liked' => []]);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    // Obter todos os posts que este utilizador já deu like
    $stmt = $GLOBALS['conn']->prepare("SELECT post_id FROM likes WHERE user_id = ?");
    $stmt->execute([$user_id]);
    
    $liked_posts = [];
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $liked_posts[] = $row['post_id'];
    }
    
    echo json_encode(['success' => true, 'posts_liked' => $liked_posts]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'posts_liked' => []]);
}
?>
