<?php
require_once 'config.php';
require_once 'posts.php';

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
                <button class="icon-btn" onclick="logout()">👤</button>
            </div>
        </div>
    </header>

    <div class="container">
        <aside class="sidebar">
            <div class="sidebar-item">
                <span>📱</span>
                <span>Feed</span>
            </div>
            <a href="mensagens.php" class="sidebar-item">
                <span>💬</span>
                <span>Mensagens</span>
            </a>
            <a href="profile.php" class="sidebar-item">
                <span>👤</span>
                <span>Perfil</span>
            </a>
        </aside>

        <!-- Main Feed -->
        <main class="feed">
            <!-- Post Creator -->
            <div class="post-creator">
                <textarea id="postContent" placeholder="Faça um post" rows="3"></textarea>
                <div class="post-actions">
                    <div class="post-options">
                        
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
            
        </aside>
    </div>

    <script src="index.js"></script>
</body>
</html>
