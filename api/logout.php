<?php
header('Content-Type: application/json');
require_once '../config.php';
require_once '../auth.php';

// Fazer logout
$result = $auth->logout();

echo json_encode($result);
?>
