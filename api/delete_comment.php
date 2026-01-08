<?php
require_once '../config.php';

session_start();

header('Content-Type: application/json');

error_log("delete_comment.php - Iniciado");
error_log("delete_comment.php - User ID da sessão: " . ($_SESSION['user_id'] ?? 'não definido'));

// Verificar se o utilizador está autenticado
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Utilizador não autenticado']);
    exit;
}

$input = file_get_contents('php://input');
error_log("delete_comment.php - Input recebido: " . $input);

$data = json_decode($input, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    error_log("delete_comment.php - Erro JSON: " . json_last_error_msg());
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'JSON inválido']);
    exit;
}

if (!isset($data['comment_id'])) {
    error_log("delete_comment.php - comment_id não fornecido");
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Comment ID é obrigatório']);
    exit;
}

$comment_id = (int)$data['comment_id'];
$user_id = (int)$_SESSION['user_id'];

error_log("delete_comment.php - Tentando apagar comment_id: $comment_id por user_id: $user_id");

try {
    // Obter informações do comentário
    $stmt = $GLOBALS['conn']->prepare("SELECT user_id, post_id FROM comments WHERE comment_id = ?");
    $stmt->execute([$comment_id]);
    
    $comment = $stmt->fetch(PDO::FETCH_ASSOC);
    
    error_log("delete_comment.php - Comentário encontrado: " . ($comment ? 'sim' : 'não'));
    
    if (!$comment) {
        error_log("delete_comment.php - Comentário $comment_id não encontrado no banco");
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Comentário não encontrado']);
        exit;
    }
    
    error_log("delete_comment.php - Comentário pertence ao user_id: " . $comment['user_id']);
    
    // Verificar se é o autor do comentário
    if ((int)$comment['user_id'] !== $user_id) {
        error_log("delete_comment.php - Usuário $user_id não tem permissão (dono: {$comment['user_id']})");
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Você não tem permissão para apagar este comentário']);
        exit;
    }
    
    // Apagar comentário (o trigger decrementará o contador automaticamente)
    $stmt = $GLOBALS['conn']->prepare("DELETE FROM comments WHERE comment_id = ?");
    $result = $stmt->execute([$comment_id]);
    
    error_log("delete_comment.php - Comentário apagado com sucesso: " . ($result ? 'sim' : 'não'));
    
    echo json_encode(['success' => true, 'message' => 'Comentário apagado com êxito']);
    
} catch (PDOException $e) {
    error_log("delete_comment.php - Erro PDO: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro ao apagar comentário: ' . $e->getMessage()]);
} catch (Exception $e) {
    error_log("delete_comment.php - Erro: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
}
?>