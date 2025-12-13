<?php
require_once '../config.php';

session_start();

if (!isset($_GET['post_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'comments' => []]);
    exit;
}

$post_id = (int)$_GET['post_id'];

try {
    $stmt = $GLOBALS['conn']->prepare("
        SELECT 
            c.comment_id,
            c.user_id,
            c.content,
            c.created_at,
            u.nome
        FROM comments c
        INNER JOIN users u ON c.user_id = u.id
        WHERE c.post_id = ?
        ORDER BY c.created_at ASC
    ");
    $stmt->execute([$post_id]);
    
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'comments' => $comments]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'comments' => []]);
}
?>
