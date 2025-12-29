<?php
require_once 'config.php';
require_once 'posts.php';

session_start();

// Verificar se o usuário está logado
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
    <title>SocialNet - Página Inicial</title>
    <link rel="stylesheet" href="home.css">
</head>
<body>
    <header>
        <div class="header-content">
            <div class="logo">SocialNet</div>
            <div class="search-bar">
                <input type="text" placeholder="Pesquisar...">
            </div>
            <div class="header-icons">
                <button class="icon-btn">🏠</button>
                <button class="icon-btn">💬</button>
                <button class="icon-btn">🔔</button>
                <button class="icon-btn" onclick="logout()">👤</button>
            </div>
        </div>
    </header>

    <div class="container">
        <aside class="sidebar">
            <div class="sidebar-item active">
                <span>📱</span>
                <span>Feed</span>
            </div>
            <div class="sidebar-item">
                <span>👥</span>
                <span>Amigos</span>
            </div>
            <a href="profile.php" class="sidebar-item">
                <span>👤</span>
                <span>Perfil</span>
            </a>
            <div class="sidebar-item">
                <span>⚙️</span>
                <span>Configurações</span>
            </div>
        </aside>

        <!-- Main Feed -->
        <main class="feed">
            <!-- Post Creator -->
            <div class="post-creator">
                <textarea id="postContent" placeholder="No que você está pensando?" rows="3"></textarea>
                <div class="post-actions">
                    <div class="post-options">
                        <button class="option-btn" disabled>📷 Foto</button>
                        <button class="option-btn" disabled>🎥 Vídeo</button>
                        <button class="option-btn" disabled>😊 Emoji</button>
                    </div>
                    <button class="post-btn" onclick="publishPost()">Publicar</button>
                </div>
            </div>

            <!-- Posts Container -->
            <div id="postsContainer">
                <p style="text-align: center; color: #999999; padding: 20px;">Carregando posts...</p>
            </div>
        </main>

        <!-- Right Sidebar -->
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
            <div class="suggestion-item">
                <div class="avatar"></div>
                <div class="suggestion-info">
                    <h4>Carla Souza</h4>
                    <p>15 amigos em comum</p>
                </div>
                <button class="follow-btn">Seguir</button>
            </div>
        </aside>
    </div>

    <script src="index.js"></script>
</body>
</html>
