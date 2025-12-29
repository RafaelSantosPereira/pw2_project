<?php
include '../config.php';

if (empty($_GET['user_id'])) {
    http_response_code(400);
    die(json_encode(['error' => 'user_id is required']));
}

$user_id = $_GET['user_id'];

try {
    $sql = "SELECT u.id, u.nome FROM followers f JOIN users u ON f.following_id = u.id WHERE f.follower_id = :user_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['user_id' => $user_id]);
    
    $following = $stmt->fetchAll();
    
    echo json_encode($following);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

$conn = null;
?>