<?php
session_start();
header('Content-Type: application/json');
require_once '../config.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Utilizador não autenticado']);
    exit;
}

$user_id = $_SESSION['user_id'];
$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);

// Debug: registar o que foi recebido
error_log('Raw input: ' . $rawInput);
error_log('Parsed data: ' . json_encode($data));

if (!$data) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Dados JSON inválidos ou vazio']);
    exit;
}

$nome = $data['name'] ?? null;
$bio = $data['bio'] ?? null;
$location = $data['location'] ?? null;

error_log('Nome: ' . ($nome ?? 'NULL'));

// Validação básica
if (!$nome || empty(trim($nome))) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Nome é obrigatório']);
    exit;
}

try {
    // Atualizar a tabela 'users'
    $stmt_user = $conn->prepare("UPDATE users SET nome = :nome WHERE id = :id");
    $stmt_user->execute([':nome' => $nome, ':id' => $user_id]);

    // Verificar se o perfil existe
    $check = $conn->prepare("SELECT user_id FROM profiles WHERE user_id = :user_id");
    $check->execute([':user_id' => $user_id]);
    $profile_exists = $check->rowCount() > 0;
    
    if ($profile_exists) {
        // Profile existe - fazer UPDATE
        $stmt_profile = $conn->prepare("UPDATE profiles SET bio = :bio, location = :location WHERE user_id = :user_id");
        $stmt_profile->execute([':bio' => $bio, ':location' => $location, ':user_id' => $user_id]);
    } else {
        // Profile não existe - fazer INSERT
        $stmt_profile = $conn->prepare("INSERT INTO profiles (user_id, bio, location) VALUES (:user_id, :bio, :location)");
        $stmt_profile->execute([':user_id' => $user_id, ':bio' => $bio, ':location' => $location]);
    }

    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'Perfil atualizado com sucesso']);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro ao atualizar o perfil: ' . $e->getMessage()]);
}
?>
