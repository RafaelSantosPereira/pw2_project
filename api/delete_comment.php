<?php
require_once '../config.php';

session_start();

// Verificar se o utilizador está autenticado
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Utilizador não autenticado']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['comment_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Comment ID é obrigatório']);
    exit;
}

$comment_id = (int)$data['comment_id'];
$user_id = $_SESSION['user_id'];

try {
    // Obter informações do comentário
    $stmt = $GLOBALS['conn']->prepare("SELECT user_id, post_id FROM comments WHERE comment_id = ?");
    $stmt->execute([$comment_id]);
    
    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Comentário não encontrado']);
        exit;
    }
    
    $comment = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Verificar se é o autor do comentário
    if ($comment['user_id'] !== $user_id) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Você não tem permissão para apagar este comentário']);
        exit;
    }
    
    // Apagar comentário (o trigger decrementará o contador automaticamente)
    $stmt = $GLOBALS['conn']->prepare("DELETE FROM comments WHERE comment_id = ?");
    $stmt->execute([$comment_id]);
    
    echo json_encode(['success' => true, 'message' => 'Comentário apagado com êxito']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro ao apagar comentário']);
}
?>
