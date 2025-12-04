<?php
require_once 'config.php';

session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .dashboard-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        .dashboard-header {
            text-align: center;
            margin-bottom: 40px;
        }
        .dashboard-header h1 {
            color: #ffffff;
            font-size: 32px;
            margin-bottom: 10px;
        }
        .user-info {
            background: #242424;
            border-radius: 8px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.5);
        }
        .user-info p {
            color: #999999;
            margin-bottom: 15px;
            font-size: 14px;
        }
        .user-info p strong {
            color: #ffffff;
        }
        .logout-btn {
            width: 100%;
            padding: 12px;
            background: #8b5cf6;
            border: 1px solid #8b5cf6;
            border-radius: 6px;
            color: #ffffff;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .logout-btn:hover {
            background: #a78bfa;
            border-color: #a78bfa;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1>Bem-vindo!</h1>
            <p>Dashboard</p>
        </div>

        <div class="user-info">
            <p><strong>Nome:</strong> <?php echo htmlspecialchars($_SESSION['user_name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['user_email']); ?></p>
            <p><strong>ID:</strong> <?php echo $_SESSION['user_id']; ?></p>
            
            <button class="logout-btn" onclick="logout()">Logout</button>
        </div>
    </div>

    <script>
        function logout() {
            fetch('api/logout.php', {
                method: 'POST'
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                window.location.href = 'login.php';
            })
            .catch(error => {
                alert('Erro ao fazer logout: ' + error);
            });
        }
    </script>
</body>
</html>
