<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$search_query = isset($_GET['q']) ? trim($_GET['q']) : '';
?>
<!DOCTYPE html>
<html lang="pt-pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesquisar - SocialNet</title>
    <link rel="stylesheet" href="../home.css">
    <link rel="stylesheet" href="search.css">
</head>
<body>
    <header>
        <div class="header-content">
            <div class="logo">SocialNet</div>
            <div class="search-bar">
                <input type="text" id="search-input" placeholder="Pesquisar..." value="<?php echo htmlspecialchars($search_query); ?>">
            </div>
            <div class="header-icons">
                <button class="icon-btn" onclick="logout()">👤</button>
            </div>
        </div>
    </header>

    <div class="container">
        <aside class="sidebar">
            <a href="../index.php" class="sidebar-item">
                <span>📱</span>
                <span>Feed</span>
            </a>
            <a href="../mensagens.php" class="sidebar-item">
                <span>💬</span>
                <span>Mensagens</span>
            </a>
            <a href="../profile.php" class="sidebar-item">
                <span>👤</span>
                <span>Perfil</span>
            </a>
        </aside>

        <main class="feed">
            <div class="search-results">
                <h2 id="search-title">Resultados da pesquisa</h2>
                
                <!-- Filtros -->
                <div class="search-filters">
                    <button class="filter-btn active" data-filter="all">Tudo</button>
                    <button class="filter-btn" data-filter="people">Pessoas</button>
                    <button class="filter-btn" data-filter="posts">Posts</button>
                </div>

                <!-- Resultados de Pessoas -->
                <div id="people-results" class="results-section">
                    <h3>Pessoas</h3>
                    <div id="people-container" class="people-container">
                        <p class="loading-message">Pesquisando pessoas...</p>
                    </div>
                </div>

                <!-- Resultados de Posts -->
                <div id="posts-results" class="results-section">
                    <h3>Posts</h3>
                    <div id="posts-container">
                        <p class="loading-message">Pesquisando posts...</p>
                    </div>
                </div>
            </div>
        </main>

        <aside class="suggestions">
        </aside>
    </div>

    <script src="../index.js"></script>
    <script>
        // Aguardar que index.js carregue completamente antes de carregar search.js
        if (typeof currentUserId === 'undefined') {
            console.warn('index.js ainda não carregou completamente');
        }
    </script>
    <script src="search.js"></script>
</body>
</html>