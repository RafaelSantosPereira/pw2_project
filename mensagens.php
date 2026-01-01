<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mensagens - SocialNet</title>
    <link rel="stylesheet" href="home.css">
    <link rel="stylesheet" href="messages.css">
</head>
<body>
    <header>
        <div class="header-content">
            <div class="logo">SocialNet</div>
            <div class="search-bar">
                <input type="text" placeholder="Pesquisar...">
            </div>
            <div class="header-icons">
                <button class="icon-btn" onclick="window.location.href='index.php'">🏠</button>
                <button class="icon-btn">💬</button>
                <button class="icon-btn">🔔</button>
                <button class="icon-btn" onclick="logout()">👤</button>
            </div>
        </div>
    </header>

    <div class="container">
        <aside class="sidebar">
            <a href="index.php" class="sidebar-item">
                <span>📱</span>
                <span>Feed</span>
            </a>
            <div class="sidebar-item">
                <span>👥</span>
                <span>Amigos</span>
            </div>
            <a href="mensagens.php" class="sidebar-item">
                <span>💬</span>
                <span>Mensagens</span>
            </a>
            <a href="profile.php" class="sidebar-item">
                <span>👤</span>
                <span>Perfil</span>
            </a>
            <div class="sidebar-item">
                <span>⚙️</span>
                <span>Configurações</span>
            </div>
        </aside>

        <main class="feed">
            <div class="messages-page">
                <div class="messages-container">
                    <div class="users-list">
                        <h3>Conversas</h3>
                        <div class="users-scroll">
                            <!-- User list will be loaded here -->
                        </div>
                    </div>
                    <div class="chat-area">
                        <div class="chat-header">
                            <div class="empty-state">
                                <span style="font-size: 48px;">💬</span>
                                <p>Selecione uma conversa para começar</p>
                            </div>
                        </div>
                        <div class="chat-messages">
                            <!-- Messages will be loaded here -->
                        </div>
                        <div class="chat-form">
                            <input type="text" id="message-input" placeholder="Escreva uma mensagem...">
                            <button id="send-button">Enviar</button>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <aside class="suggestions">
            <h3>Sugestões de Amizade</h3>
            <div class="suggestion-item">
                <div class="avatar"></div>
                <div class="suggestion-info">
                    <h4>Ana Costa</h4>
                    <p>12 amigos em comum</p>
                </div>
                <button class="follow-btn">Seguir</button>
            </div>
            <div class="suggestion-item">
                <div class="avatar"></div>
                <div class="suggestion-info">
                    <h4>Pedro Lima</h4>
                    <p>8 amigos em comum</p>
                </div>
                <button class="follow-btn">Seguir</button>
            </div>
        </aside>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Define currentUserId apenas se ainda não existir
        if (typeof currentUserId === 'undefined') {
            var currentUserId = <?php echo $_SESSION['user_id']; ?>;
        }
    </script>
    <script src="messages.js"></script>
</body>
</html>