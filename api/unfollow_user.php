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

try {
    $sql = "DELETE FROM followers WHERE follower_id = :follower_id AND following_id = :following_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        'follower_id' => $follower_id,
        'following_id' => $following_id
    ]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => 'User unfollowed successfully']);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Follow relationship not found']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

$conn = null;
?>