<?php
require_once '../config.php';

session_start();

header('Content-Type: application/json');

// Verificar se o utilizador está autenticado
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Utilizador não autenticado']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['post_id']) || !isset($data['content'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Post ID e conteúdo são obrigatórios']);
    exit;
}

$post_id = (int)$data['post_id'];
$content = trim($data['content']);
$user_id = (int)$_SESSION['user_id'];

if (empty($content)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'O comentário não pode estar vazio']);
    exit;
}

try {
    // Inserir comentário
    $stmt = $GLOBALS['conn']->prepare("INSERT INTO comments (user_id, post_id, content, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$user_id, $post_id, $content]);
    
    $comment_id = $GLOBALS['conn']->lastInsertId();
    
    // Buscar o comentário completo com informações do usuário
    $stmt = $GLOBALS['conn']->prepare("
        SELECT c.comment_id, c.user_id, c.post_id, c.content, c.created_at, u.nome
        FROM comments c
        JOIN users u ON c.user_id = u.id
        WHERE c.comment_id = ?
    ");
    $stmt->execute([$comment_id]);
    
    $comment = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$comment) {
        throw new Exception('Erro ao recuperar comentário criado');
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Comentário adicionado com êxito',
        'comment' => $comment
    ]);
    
} catch (PDOException $e) {
    error_log("create_comment.php - Erro: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro ao adicionar comentário: ' . $e->getMessage()]);
} catch (Exception $e) {
    error_log("create_comment.php - Erro: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>