<?php
require_once 'config.php';

class Auth {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    public function signup($name, $email, $password) {
        if (empty($name) || empty($email) || empty($password)) {
            return ['success' => false, 'message' => 'Todos os campos são obrigatórios'];
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Email inválido'];
        }
        
        if (strlen($password) < 6) {
            return ['success' => false, 'message' => 'Palavra-passe deve ter pelo menos 6 caracteres'];
        }
        
        // Verificar se email já existe
        try {
            $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->rowCount() > 0) {
                return ['success' => false, 'message' => 'Email já registado'];
            }
            
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            
            $stmt = $this->conn->prepare("INSERT INTO users (nome, email, password_hash) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $hashed_password]);
            
            return ['success' => true, 'message' => 'Conta criada com êxito'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erro ao criar a conta'];
        }
    }
    
    public function login($email, $password) {
        if (empty($email) || empty($password)) {
            return ['success' => false, 'message' => 'Email e senha são obrigatórios'];
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Email inválido'];
        }
        
        try {
            // Verificar se o utilizador existe
            $stmt = $this->conn->prepare("SELECT id, nome, email, password_hash FROM users WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->rowCount() === 0) {
                return ['success' => false, 'message' => 'Email ou palavra-passe incorretos'];
            }
            
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Verificar palavra-passe
            if (!password_verify($password, $user['password_hash'])) {
                return ['success' => false, 'message' => 'Email ou palavra-passe incorretos'];
            }
            
            // Iniciar sessão
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['nome'];
            $_SESSION['user_email'] = $user['email'];
            
            return ['success' => true, 'message' => 'Autenticação realizada com êxito'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erro ao fazer login'];
        }
    }
    
    public function logout() {
        session_start();
        session_destroy();
        return ['success' => true, 'message' => 'Sessão finalizada com êxito'];
    }
}

$auth = new Auth($conn);
?>
