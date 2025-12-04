<?php
require_once 'config.php';

class Posts {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    /**
     * Criar um novo post
     */
    public function createPost($user_id, $content) {
        // Validações básicas
        if (empty($content)) {
            return ['success' => false, 'message' => 'O conteúdo do post é obrigatório'];
        }
        
        if (strlen($content) > 5000) {
            return ['success' => false, 'message' => 'O post não pode ter mais de 5000 caracteres'];
        }
        
        // Inserir novo post
        $stmt = $this->conn->prepare("INSERT INTO posts (user_id, content) VALUES (?, ?)");
        $stmt->bind_param("is", $user_id, $content);
        
        if ($stmt->execute()) {
            $post_id = $this->conn->insert_id;
            $stmt->close();
            return ['success' => true, 'message' => 'Post publicado com êxito', 'post_id' => $post_id];
        } else {
            $stmt->close();
            return ['success' => false, 'message' => 'Erro ao publicar o post'];
        }
    }
    
    /**
     * Obter todos os posts com informações do utilizador
     */
    public function getAllPosts($limit = 20, $offset = 0) {
        $stmt = $this->conn->prepare("
            SELECT 
                p.post_id, 
                p.user_id, 
                p.content, 
                p.likes_count, 
                p.comments_count, 
                p.created_at,
                u.nome,
                u.email
            FROM posts p
            INNER JOIN users u ON p.user_id = u.id
            ORDER BY p.created_at DESC
            LIMIT ? OFFSET ?
        ");
        
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $posts = [];
        while ($row = $result->fetch_assoc()) {
            $posts[] = $row;
        }
        
        $stmt->close();
        return ['success' => true, 'posts' => $posts];
    }
    
    /**
     * Obter posts de um utilizador específico
     */
    public function getUserPosts($user_id, $limit = 20, $offset = 0) {
        $stmt = $this->conn->prepare("
            SELECT 
                p.post_id, 
                p.user_id, 
                p.content, 
                p.likes_count, 
                p.comments_count, 
                p.created_at,
                u.nome,
                u.email
            FROM posts p
            INNER JOIN users u ON p.user_id = u.id
            WHERE p.user_id = ?
            ORDER BY p.created_at DESC
            LIMIT ? OFFSET ?
        ");
        
        $stmt->bind_param("iii", $user_id, $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $posts = [];
        while ($row = $result->fetch_assoc()) {
            $posts[] = $row;
        }
        
        $stmt->close();
        return ['success' => true, 'posts' => $posts];
    }
    
    /**
     * Apagar um post
     */
    public function deletePost($post_id, $user_id) {
        // Verificar se o post pertence ao utilizador
        $stmt = $this->conn->prepare("SELECT user_id FROM posts WHERE post_id = ?");
        $stmt->bind_param("i", $post_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            $stmt->close();
            return ['success' => false, 'message' => 'Post não encontrado'];
        }
        
        $post = $result->fetch_assoc();
        $stmt->close();
        
        if ($post['user_id'] !== $user_id) {
            return ['success' => false, 'message' => 'Você não tem permissão para apagar este post'];
        }
        
        // Apagar o post
        $stmt = $this->conn->prepare("DELETE FROM posts WHERE post_id = ?");
        $stmt->bind_param("i", $post_id);
        
        if ($stmt->execute()) {
            $stmt->close();
            return ['success' => true, 'message' => 'Post apagado com êxito'];
        } else {
            $stmt->close();
            return ['success' => false, 'message' => 'Erro ao apagar o post'];
        }
    }
}

$posts = new Posts($conn);
?>
