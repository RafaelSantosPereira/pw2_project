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

if (!isset($data['post_id']) || !isset($data['content'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Post ID e conteúdo são obrigatórios']);
    exit;
}

$post_id = (int)$data['post_id'];
$user_id = $_SESSION['user_id'];
$content = $data['content'];

// Validações básicas
if (empty($content)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'O comentário não pode estar vazio']);
    exit;
}

if (strlen($content) > 500) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'O comentário não pode ter mais de 500 caracteres']);
    exit;
}

try {
    // Inserir comentário (o trigger incrementará o contador automaticamente)
    $stmt = $GLOBALS['conn']->prepare("INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)");
    $stmt->execute([$post_id, $user_id, $content]);
    
    // Obter informações do utilizador
    $stmt = $GLOBALS['conn']->prepare("SELECT id, nome FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'message' => 'Comentário adicionado com êxito',
        'comment' => [
            'comment_id' => $GLOBALS['conn']->lastInsertId(),
            'user_id' => $user_id,
            'nome' => $user['nome'],
            'content' => $content,
            'created_at' => date('Y-m-d H:i:s')
        ]
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro ao adicionar comentário']);
}
?>
