<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    die(json_encode(['error' => 'User not logged in']));
}

$data = json_decode(file_get_contents('php://input'), true);

if (empty($data['following_id'])) {
    http_response_code(400);
    die(json_encode(['error' => 'following_id is required']));
}

$follower_id = $_SESSION['user_id'];
$following_id = $data['following_id'];

if ($follower_id == $following_id) {
    http_response_code(400);
    die(json_encode(['error' => 'User cannot follow themselves']));
}

try {
    $sql = "INSERT INTO followers (follower_id, following_id) VALUES (:follower_id, :following_id)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        'follower_id' => $follower_id,
        'following_id' => $following_id
    ]);

    echo json_encode(['success' => 'User followed successfully']);

} catch (PDOException $e) {
    if ($e->getCode() == 23000) { 
        http_response_code(409);
        echo json_encode(['error' => 'User already follows this user']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to follow user']);
    }
}

$conn = null;
?>