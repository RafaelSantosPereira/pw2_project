<?php
session_start();
// O header deve ser definido antes de qualquer output
header('Content-Type: application/json');
require_once '../config.php'; // Ficheiro de configuração do Sistema

// O user_id é determinado pelo parâmetro GET 'user_id'
// Se não for fornecido, assume o id do user da sessão atual
$user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0);

if ($user_id === 0) {
    http_response_code(401); // Unauthorized
    echo json_encode(['error' => 'Utilizador não especificado ou não autenticado']);
    exit;
}

try {
    // Preparar a consulta para obter dados do perfil e do usuário
    $sql = "SELECT u.id, u.nome, u.email, u.followers_count, u.following_count, 
                   p.bio, p.website, p.location, p.birthdate, p.avatar_url,
                   (SELECT COUNT(*) FROM posts WHERE user_id = u.id) as posts_count
            FROM users u
            LEFT JOIN profiles p ON u.id = p.user_id
            WHERE u.id = :user_id";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(['error' => 'Erro a preparar a query.']);
        exit;
    }

    $stmt->execute(['user_id' => $user_id]);
    
    $profile_data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($profile_data) {
        // Adicionar um campo para indicar se o perfil é do user autenticado
        $profile_data['is_own_profile'] = isset($_SESSION['user_id']) && $user_id === (int)$_SESSION['user_id'];
        echo json_encode($profile_data);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Perfil não encontrado']);
    }

} catch (PDOException $e) {
    http_response_code(500);
    // Em produção, evite expor detalhes do erro.
    echo json_encode(['error' => 'Erro na base de dados: ' . $e->getMessage()]);
}
?>
