// Variável global para armazenar o ID do utilizador autenticado
let currentUserId = null;

document.addEventListener('DOMContentLoaded', async function() {
    // Obter user ID da API antes de carregar posts
    try {
        const response = await fetch('api/get_user_info.php');
        const data = await response.json();
        if (data.success) {
            currentUserId = data.user_id;
            loadPosts();
        } else {
            console.error('Erro ao obter informações do utilizador');
        }
    } catch (error) {
        console.error('Erro:', error);
    }
});

// Carregar posts ao iniciar a página
async function loadPosts() {
    const container = document.getElementById('postsContainer');

    try {
        const response = await fetch('api/get_posts.php?limit=50&offset=0');
        const data = await response.json();
        if (data.success && data.posts.length > 0) {
            container.innerHTML = '';
            data.posts.forEach(post => {
                container.appendChild(createPostElement(post));
            });
        } else {
            container.innerHTML = '<p style="text-align: center; color: #999999; padding: 20px;">Nenhum post ainda. Seja o primeiro a publicar!</p>';
        }
    } catch (error) {
        container.innerHTML = '<p style="text-align: center; color: #ff6b6b; padding: 20px;">Erro ao carregar posts</p>';
        console.error('Erro ao carregar posts:', error);
    }
}

function createPostElement(post) {
    const article = document.createElement('article');
    article.className = 'post';
    article.id = 'post-' + post.post_id;
    // REMOVIDO: O espaçamento agora é gerido exclusivamente pelo CSS (gap)
    
    const date = new Date(post.created_at);
    const formattedDate = formatTimeAgo(date);
    const canDelete = currentUserId === post.user_id;

    // Construir o HTML do post
    let postHTML = `
        <div class="post-header">
            <div class="avatar" style="background: linear-gradient(135deg, #8b5cf6, #a78bfa); display: flex; align-items: center; justify-content: center;">
                <span style="color: white; font-weight: bold; font-size: 20px;">${post.nome.charAt(0).toUpperCase()}</span>
            </div>
            <div class="post-info">
                <h3>${escapeHtml(post.nome)}</h3>
                <p>${formattedDate}</p>
            </div>
        </div>
    `;

    if (canDelete) {
        postHTML += `<button class="delete-post-btn" onclick="deletePost(${post.post_id})" title="Apagar post">×</button>`;
    }

    postHTML += `
        <div class="post-content">
            <p>${escapeHtml(post.content)}</p>
        </div>
        <div class="post-stats">
            <span>👍 ${post.likes_count} Likes</span>
            <span>💬 ${post.comments_count} comentários</span>
        </div>
        <div class="post-interactions">
            <button class="interact-btn">❤️ Like</button>
            <button class="interact-btn">💬 Comentar</button>
            <button class="interact-btn">↗️ Compartilhar</button>
        </div>
    `;

    article.innerHTML = postHTML;
    return article;
}

function publishPost() {
    const content = document.getElementById('postContent').value.trim();

    if (!content) {
        alert('O post não pode estar vazio');
        return;
    }

    const btn = event.target;
    btn.disabled = true;
    btn.textContent = 'A publicar...';

    (async () => {
        try {
            const response = await fetch('api/create_post.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ content: content })
            });
            const data = await response.json();
            if (data.success) {
                document.getElementById('postContent').value = '';
                loadPosts();
            } else {
                alert(data.message || 'Erro ao publicar post');
            }
        } catch (error) {
            alert('Erro ao publicar post: ' + error);
            console.error('Erro:', error);
        } finally {
            btn.disabled = false;
            btn.textContent = 'Publicar';
        }
    })();
}

function deletePost(postId) {
    if (!confirm('Tem a certeza que deseja apagar este post?')) {
        return;
    }

    (async () => {
        try {
            const response = await fetch('api/delete_post.php', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ post_id: postId })
            });
            const data = await response.json();
            if (data.success) {
                const postElement = document.getElementById('post-' + postId);
                if (postElement) {
                    postElement.remove();
                }
            } else {
                alert(data.message || 'Erro ao apagar post');
            }
        } catch (error) {
            alert('Erro ao apagar post: ' + error);
            console.error('Erro:', error);
        }
    })();
}

function logout() {
    if (!confirm('Tem a certeza que deseja finalizar a sessão?')) {
        return;
    }

    (async () => {
        try {
            const response = await fetch('api/logout.php', {
                method: 'POST'
            });
            const data = await response.json();
            window.location.href = 'login.php';
        } catch (error) {
            alert('Erro ao fazer logout: ' + error);
            console.error('Erro:', error);
        }
    })();
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function formatTimeAgo(date) {
    const now = new Date();
    const diffMs = now - date;
    const diffMins = Math.floor(diffMs / 60000);
    const diffHours = Math.floor(diffMs / 3600000);
    const diffDays = Math.floor(diffMs / 86400000);

    if (diffMins < 1) return 'Agora';
    if (diffMins < 60) return `Há ${diffMins} minuto${diffMins > 1 ? 's' : ''}`;
    if (diffHours < 24) return `Há ${diffHours} hora${diffHours > 1 ? 's' : ''}`;
    if (diffDays < 7) return `Há ${diffDays} dia${diffDays > 1 ? 's' : ''}`;
    
    return date.toLocaleDateString('pt-PT');
}