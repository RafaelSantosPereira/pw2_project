<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Se o user_id não for passado por GET, usa o da sessão
$user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : $_SESSION['user_id'];
$is_own_profile = ($user_id === $_SESSION['user_id']);

?>
<!DOCTYPE html>
<html lang="pt-pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Utilizador</title>
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
            <a href="profile.php" class="sidebar-item active">
                <span>👤</span>
                <span>Perfil</span>
            </a>
            <div class="sidebar-item">
                <span>⚙️</span>
                <span>Configurações</span>
            </div>
        </aside>

        <main class="feed">
            <div class="profile-page">
                <div class="profile-header-banner">
                </div>
                <div class="profile-header-content">
                    <div class="profile-avatar-container">
                        <div id="profile-avatar" class="profile-avatar">
                            <img id="avatar-img" src="https://via.placeholder.com/150" alt="Avatar">
                        </div>
                    <button class="edit-profile-btn" onclick="toggleEditForm(event)">Editar Perfil</button>                    </div>
                    <div class="profile-info">
                        <h1 id="profile-name" class="profile-name"></h1>
                        <div class="profile-details">
                            <p id="profile-location" class="profile-location"></p>
                            <p id="profile-bio" class="profile-bio"></p>
                        </div>
                        <div id="profile-stats" class="profile-stats">
                        </div>
                    </div>
                </div>

                <div id="edit-profile-form" style="display: none;">
                    <button type="button" class="close-form-btn" onclick="toggleEditForm()">×</button>
                    <form onsubmit="updateProfile(event)">
                        <label for="name">Nome:</label>
                        <input type="text" id="name" name="name">
                        <label for="bio">Bio:</label>
                        <textarea id="bio" name="bio"></textarea>
                        <label for="location">Localização:</label>
                        <input type="text" id="location" name="location">
                        <button type="submit">Guardar</button>
                    </form>
                </div>

                <div id="posts-container" class="profile-content">
                </div>
            </div>
        </main>

        <aside class="suggestions">
        </aside>
    </div>

    <script src="index.js"></script>
    <script src="profile.js"></script>

</body>
</html>
