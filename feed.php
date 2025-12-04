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
    <title>Feed - Sistema</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            background: #1a1a1a;
        }

        .feed-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .feed-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding: 20px 0;
            border-bottom: 1px solid #333333;
        }

        .feed-header h1 {
            color: #ffffff;
            font-size: 28px;
            margin: 0;
        }

        .logout-btn {
            padding: 10px 20px;
            background: #8b5cf6;
            border: 1px solid #8b5cf6;
            border-radius: 6px;
            color: #ffffff;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .logout-btn:hover {
            background: #a78bfa;
            border-color: #a78bfa;
        }

        .create-post {
            background: #242424;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.5);
        }

        .user-info {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            gap: 12px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: #8b5cf6;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-weight: 600;
            font-size: 18px;
        }

        .user-name {
            color: #ffffff;
            font-weight: 500;
            font-size: 14px;
        }

        textarea {
            width: 100%;
            padding: 12px;
            background: #333333;
            border: 1px solid #404040;
            border-radius: 6px;
            color: #ffffff;
            font-size: 14px;
            font-family: inherit;
            resize: vertical;
            min-height: 100px;
            margin-bottom: 15px;
            transition: all 0.2s ease;
        }

        textarea:focus {
            outline: none;
            border-color: #8b5cf6;
            background: #3a3a3a;
            box-shadow: 0 0 8px rgba(139, 92, 246, 0.3);
        }

        textarea::placeholder {
            color: #666666;
        }

        .post-actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-publish {
            flex: 1;
            background: #8b5cf6;
            color: #ffffff;
            border: 1px solid #8b5cf6;
        }

        .btn-publish:hover {
            background: #a78bfa;
            border-color: #a78bfa;
        }

        .btn-publish:disabled {
            background: #666666;
            border-color: #666666;
            cursor: not-allowed;
        }

        .btn-cancel {
            background: #333333;
            color: #999999;
            border: 1px solid #404040;
        }

        .btn-cancel:hover {
            background: #3a3a3a;
        }

        .posts-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .post {
            background: #242424;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.5);
        }

        .post-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 12px;
        }

        .post-author {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .post-author-avatar {
            width: 36px;
            height: 36px;
            background: #8b5cf6;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-weight: 600;
            font-size: 16px;
        }

        .post-author-info h3 {
            color: #ffffff;
            font-size: 14px;
            font-weight: 600;
            margin: 0 0 4px 0;
        }

        .post-author-info p {
            color: #999999;
            font-size: 12px;
            margin: 0;
        }

        .post-delete-btn {
            background: none;
            border: none;
            color: #999999;
            cursor: pointer;
            font-size: 18px;
            padding: 0;
            transition: color 0.2s ease;
        }

        .post-delete-btn:hover {
            color: #ff6b6b;
        }

        .post-content {
            color: #e0e0e0;
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 15px;
            word-wrap: break-word;
        }

        .post-meta {
            display: flex;
            gap: 20px;
            color: #666666;
            font-size: 12px;
            padding-top: 12px;
            border-top: 1px solid #333333;
        }

        .loading {
            text-align: center;
            color: #666666;
            padding: 20px;
        }

        .error {
            background: #ff6b6b;
            color: #ffffff;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .empty-state {
            text-align: center;
            color: #666666;
            padding: 40px 20px;
        }

        .empty-state p {
            font-size: 16px;
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="feed-container">
        <div class="feed-header">
            <h1>Feed</h1>
            <button class="logout-btn" onclick="logout()">Logout</button>
        </div>

        <!-- Criar Post -->
        <div class="create-post">
            <div class="user-info">
                <div class="user-avatar"><?php echo strtoupper(substr($_SESSION['user_name'], 0, 1)); ?></div>
                <div class="user-name"><?php echo htmlspecialchars($_SESSION['user_name']); ?></div>
            </div>
            <textarea id="postContent" placeholder="O que está na sua mente?"></textarea>
            <div class="post-actions">
                <button class="btn btn-publish" onclick="publishPost()">Publicar</button>
            </div>
        </div>

        <!-- Feed de Posts -->
        <main id="postsContainer" class="posts-container">
            <div class="loading">Carregando posts...</div>
        </main>
    </div>

    <script>
        // Carregar posts ao iniciar a página
        document.addEventListener('DOMContentLoaded', function() {
            loadPosts();
        });

        function loadPosts() {
            const container = document.getElementById('postsContainer');
            container.innerHTML = '<div class="loading">Carregando posts...</div>';

            fetch('api/get_posts.php?limit=20&offset=0')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.posts.length > 0) {
                        container.innerHTML = '';
                        data.posts.forEach(post => {
                            container.appendChild(createPostElement(post));
                        });
                    } else {
                        container.innerHTML = '<div class="empty-state"><p>Nenhum post ainda. Seja o primeiro a publicar!</p></div>';
                    }
                })
                .catch(error => {
                    container.innerHTML = '<div class="error">Erro ao carregar posts: ' + error + '</div>';
                });
        }

        function createPostElement(post) {
            const div = document.createElement('div');
            div.className = 'post';
            div.id = 'post-' + post.post_id;

            const date = new Date(post.created_at);
            const formattedDate = date.toLocaleDateString('pt-BR') + ' ' + date.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });

            const canDelete = <?php echo $_SESSION['user_id']; ?> === post.user_id;
            const deleteBtn = canDelete ? `<button class="post-delete-btn" title="Apagar" onclick="deletePost(${post.post_id})">×</button>` : '';

            div.innerHTML = `
                <div class="post-header">
                    <div class="post-author">
                        <div class="post-author-avatar">${post.nome.charAt(0).toUpperCase()}</div>
                        <div class="post-author-info">
                            <h3>${escapeHtml(post.nome)}</h3>
                            <p>${escapeHtml(post.email)}</p>
                        </div>
                    </div>
                    ${deleteBtn}
                </div>
                <div class="post-content">${escapeHtml(post.content)}</div>
                <div class="post-meta">
                    <span>👍 ${post.likes_count} likes</span>
                    <span>💬 ${post.comments_count} comentários</span>
                    <span>${formattedDate}</span>
                </div>
            `;

            return div;
        }

        function publishPost() {
            const content = document.getElementById('postContent').value.trim();

            if (!content) {
                alert('O post não pode estar vazio');
                return;
            }

            const btn = event.target;
            btn.disabled = true;
            btn.textContent = 'Publicando...';

            fetch('api/create_post.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ content: content })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('postContent').value = '';
                    loadPosts();
                } else {
                    alert(data.message || 'Erro ao publicar post');
                }
            })
            .catch(error => {
                alert('Erro ao publicar post: ' + error);
            })
            .finally(() => {
                btn.disabled = false;
                btn.textContent = 'Publicar';
            });
        }

        function deletePost(postId) {
            if (!confirm('Tem a certeza que deseja apagar este post?')) {
                return;
            }

            fetch('api/delete_post.php', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ post_id: postId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('post-' + postId).remove();
                } else {
                    alert(data.message || 'Erro ao apagar post');
                }
            })
            .catch(error => {
                alert('Erro ao apagar post: ' + error);
            });
        }

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

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    </script>
</body>
</html>
