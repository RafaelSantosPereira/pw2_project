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
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $stmt->close();
            return ['success' => false, 'message' => 'Email já registado'];
        }
        $stmt->close();
        
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        
        $stmt = $this->conn->prepare("INSERT INTO users (nome, email, password_hash) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $hashed_password);
        
        if ($stmt->execute()) {
            $stmt->close();
            return ['success' => true, 'message' => 'Conta criada com êxito'];
        } else {
            $stmt->close();
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
        
        // Verificar se o utilizador existe
        $stmt = $this->conn->prepare("SELECT id, nome, email, password_hash FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        // Se não retornar nada, não existe
        if ($result->num_rows === 0) {
            $stmt->close();
            return ['success' => false, 'message' => 'Email ou palavra-passe incorretos'];
        }
        
        $user = $result->fetch_assoc();
        $stmt->close();
        
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
    }
    
    public function logout() {
        session_start();
        session_destroy();
        return ['success' => true, 'message' => 'Sessão finalizada com êxito'];
    }
}

$auth = new Auth($conn);
?>
