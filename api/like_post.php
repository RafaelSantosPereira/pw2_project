<?php
require_once '../config.php';
require_once '../posts.php';

session_start();

// Verificar se o utilizador está autenticado
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Utilizador não autenticado']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['post_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Post ID é obrigatório']);
    exit;
}

$post_id = (int)$data['post_id'];
$user_id = $_SESSION['user_id'];

try {
    // Verificar se o utilizador já deu like a este post
    $stmt = $GLOBALS['conn']->prepare("SELECT * FROM likes WHERE user_id = ? AND post_id = ?");
    $stmt->execute([$user_id, $post_id]);
    
    if ($stmt->rowCount() > 0) {
        // Remover like (o trigger decrementará o contador automaticamente)
        $stmt = $GLOBALS['conn']->prepare("DELETE FROM likes WHERE user_id = ? AND post_id = ?");
        $stmt->execute([$user_id, $post_id]);
        
        echo json_encode(['success' => true, 'message' => 'Like removido', 'liked' => false]);
    } else {
        // Adicionar like (o trigger incrementará o contador automaticamente)
        $stmt = $GLOBALS['conn']->prepare("INSERT INTO likes (user_id, post_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $post_id]);
        
        echo json_encode(['success' => true, 'message' => 'Like adicionado', 'liked' => true]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro ao processar like']);
}
?>
