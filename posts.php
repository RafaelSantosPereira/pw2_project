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
        
        try {
            // Inserir novo post
            $stmt = $this->conn->prepare("INSERT INTO posts (user_id, content) VALUES (?, ?)");
            $stmt->execute([$user_id, $content]);
            
            $post_id = $this->conn->lastInsertId();
            
            return ['success' => true, 'message' => 'Post publicado com êxito', 'post_id' => $post_id];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erro ao publicar o post'];
        }
    }
    
    /**
     * Obter todos os posts com informações do utilizador
     */
    public function getAllPosts($limit = 20, $offset = 0) {
        try {
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
            
            $stmt->execute([$limit, $offset]);
            $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return ['success' => true, 'posts' => $posts];
        } catch (PDOException $e) {
            return ['success' => false, 'posts' => []];
        }
    }
    
    /**
     * Obter posts de um utilizador específico
     */
    public function getUserPosts($user_id, $limit = 20, $offset = 0) {
        try {
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
            
            $stmt->execute([$user_id, $limit, $offset]);
            $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return ['success' => true, 'posts' => $posts];
        } catch (PDOException $e) {
            return ['success' => false, 'posts' => []];
        }
    }
    
    /**
     * Apagar um post
     */
    public function deletePost($post_id, $user_id) {
        try {
            // Verificar se o post pertence ao utilizador
            $stmt = $this->conn->prepare("SELECT user_id FROM posts WHERE post_id = ?");
            $stmt->execute([$post_id]);
            
            if ($stmt->rowCount() === 0) {
                return ['success' => false, 'message' => 'Post não encontrado'];
            }
            
            $post = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($post['user_id'] !== $user_id) {
                return ['success' => false, 'message' => 'Você não tem permissão para apagar este post'];
            }
            
            // Apagar o post
            $stmt = $this->conn->prepare("DELETE FROM posts WHERE post_id = ?");
            $stmt->execute([$post_id]);
            
            return ['success' => true, 'message' => 'Post apagado com êxito'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erro ao apagar o post'];
        }
    }
}

$posts = new Posts($conn);
?>
