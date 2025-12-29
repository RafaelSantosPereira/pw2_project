// Variável global para armazenar o ID do utilizador autenticado
let currentUserId = null;
let likedPosts = [];

document.addEventListener('DOMContentLoaded', async function() {
    // Obter user ID da API antes de carregar posts
    try {
        const response = await fetch('api/get_user_info.php');
        const data = await response.json();
        if (data.success) {
            currentUserId = data.user_id;
            
            // Obter lista de posts que o utilizador já deu like
            const likedResponse = await fetch('api/get_liked_posts.php');
            const likedData = await likedResponse.json();
            if (likedData.success) {
                likedPosts = likedData.posts_liked;
            }
            
            // Apenas carregar posts se o container principal de posts existir
            if (document.getElementById('postsContainer')) {
                loadPosts();
            }
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
    
    const date = new Date(post.created_at);
    const formattedDate = formatTimeAgo(date);
    const canDelete = currentUserId === post.user_id;
    const isLiked = likedPosts.includes(post.post_id);

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
            <span id="likes-count-${post.post_id}">👍 ${post.likes_count} Like${post.likes_count !== 1 ? 's' : ''}</span>
            <span id="comments-count-${post.post_id}">💬 ${post.comments_count} comentário${post.comments_count !== 1 ? 's' : ''}</span>
        </div>
        <div class="post-interactions">
            <button class="interact-btn like-btn${isLiked ? ' liked' : ''}" onclick="toggleLike(${post.post_id})" title="Like">
                <svg viewBox="0 0 24 24" class="heart-icon" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                </svg>
                <span class="like-text">Like</span>
            </button>
            <button class="interact-btn" onclick="toggleComments(${post.post_id})">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                </svg>
                <span class="like-text">Comentários</span>
            </button>
            <button class="interact-btn">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"></path>
                    <polyline points="16 6 12 2 8 6"></polyline>
                    <line x1="12" y1="2" x2="12" y2="15"></line>
                </svg>
                <span class="like-text">Compartilhar</span>
            </button>
        </div>
        <div class="comments-section" id="comments-section-${post.post_id}" style="display: none;">
            <div class="comment-input-area">
                <input type="text" class="comment-input" placeholder="Adicione um comentário..." id="comment-input-${post.post_id}" onkeypress="handleCommentKeypress(event, ${post.post_id})">
                <button class="comment-btn" onclick="publishComment(${post.post_id})">Enviar</button>
            </div>
            <div class="comments-list" id="comments-list-${post.post_id}">
                <p style="text-align: center; color: #888; padding: 20px;">Nenhum comentário ainda</p>
            </div>  
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

function toggleLike(postId) {
    (async () => {
        try {
            const response = await fetch('api/like_post.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ post_id: postId })
            });
            const data = await response.json();
            
            if (data.success) {
                // Encontrar o botão de like
                const likeBtn = document.querySelector(`#post-${postId} .like-btn`);
                const likesCount = document.getElementById(`likes-count-${postId}`);
                
                // Atualizar o estado no array global
                if (data.liked) {
                    likedPosts.push(postId);
                    likeBtn.classList.add('liked');
                } else {
                    likedPosts = likedPosts.filter(id => id !== postId);
                    likeBtn.classList.remove('liked');
                }
                
                // Atualizar o contador
                const currentCount = parseInt(likesCount.textContent.match(/\d+/)[0]);
                const newCount = data.liked ? currentCount + 1 : currentCount - 1;
                likesCount.textContent = `👍 ${newCount} Like${newCount !== 1 ? 's' : ''}`;
            } else {
                console.error('Erro ao processar like:', data.message);
            }
        } catch (error) {
            console.error('Erro ao fazer like:', error);
        }
    })();
}

function toggleComments(postId) {
    const commentsSection = document.getElementById(`comments-section-${postId}`);
    const isVisible = commentsSection.style.display !== 'none';
    
    if (isVisible) {
        commentsSection.style.display = 'none';
    } else {
        commentsSection.style.display = 'block';
        // Carregar comentários quando abre
        loadCommentsForPost(postId);
        // Focar no campo de input quando abre
        setTimeout(() => {
            document.getElementById(`comment-input-${postId}`).focus();
        }, 100);
    }
}

async function loadCommentsForPost(postId) {
    const commentsList = document.getElementById(`comments-list-${postId}`);
    
    try {
        const response = await fetch(`api/get_comments.php?post_id=${postId}`);
        const data = await response.json();
        
        if (data.success && data.comments.length > 0) {
            commentsList.innerHTML = '';
            data.comments.forEach(comment => {
                const commentElement = createCommentElement(comment, postId);
                commentsList.appendChild(commentElement);
            });
        } else {
            commentsList.innerHTML = '<p style="text-align: center; color: #888; padding: 20px;">Nenhum comentário ainda</p>';
        }
    } catch (error) {
        console.error('Erro ao carregar comentários:', error);
        commentsList.innerHTML = '<p style="text-align: center; color: #ff6b6b; padding: 20px;">Erro ao carregar comentários</p>';
    }
}

function createCommentElement(comment, postId) {
    const commentDiv = document.createElement('div');
    commentDiv.className = 'comment';
    commentDiv.id = `comment-${comment.comment_id}`;
    
    const date = new Date(comment.created_at);
    const formattedDate = formatTimeAgo(date);
    const canDelete = currentUserId === comment.user_id;
    
    let commentHTML = `
        <div class="comment-header">
            <div class="comment-avatar" style="background: linear-gradient(135deg, #8b5cf6, #a78bfa); display: flex; align-items: center; justify-content: center;">
                <span style="color: white; font-weight: bold; font-size: 12px;">${comment.nome.charAt(0).toUpperCase()}</span>
            </div>
            <div class="comment-info">
                <h4>${escapeHtml(comment.nome)}</h4>
                <p>${formattedDate}</p>
            </div>
        </div>
    `;
    
    if (canDelete) {
        commentHTML += `<button class="delete-comment-btn" onclick="deleteComment(${comment.comment_id}, ${postId})" title="Apagar comentário">×</button>`;
    }
    
    commentHTML += `
        <div class="comment-content">
            <p>${escapeHtml(comment.content)}</p>
        </div>
    `;
    
    commentDiv.innerHTML = commentHTML;
    return commentDiv;
}

function handleCommentKeypress(event, postId) {
    if (event.key === 'Enter') {
        event.preventDefault();
        publishComment(postId);
    }
}

function publishComment(postId) {
    const input = document.getElementById(`comment-input-${postId}`);
    const content = input.value.trim();
    
    if (!content) {
        alert('O comentário não pode estar vazio');
        return;
    }
    
    (async () => {
        try {
            const response = await fetch('api/create_comment.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ post_id: postId, content: content })
            });
            const data = await response.json();
            
            if (data.success) {
                // Limpar input
                input.value = '';
                
                // Adicionar comentário à lista
                const commentsList = document.getElementById(`comments-list-${postId}`);
                
                // Se é a primeira mensagem "sem comentários", remover
                if (commentsList.querySelector('p[style*="text-align"]')) {
                    commentsList.innerHTML = '';
                }
                
                // Criar elemento do comentário
                const commentElement = createCommentElement(data.comment, postId);
                commentsList.appendChild(commentElement);
                
                // Atualizar contador de comentários
                const commentsCount = document.getElementById(`comments-count-${postId}`);
                const currentCount = parseInt(commentsCount.textContent.match(/\d+/)[0]);
                const newCount = currentCount + 1;
                commentsCount.textContent = `💬 ${newCount} comentário${newCount !== 1 ? 's' : ''}`;
            } else {
                alert(data.message || 'Erro ao adicionar comentário');
            }
        } catch (error) {
            alert('Erro ao adicionar comentário: ' + error);
            console.error('Erro:', error);
        }
    })();
}

function deleteComment(commentId, postId) {
    if (!confirm('Tem a certeza que deseja apagar este comentário?')) {
        return;
    }
    
    (async () => {
        try {
            const response = await fetch('api/delete_comment.php', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ comment_id: commentId })
            });
            const data = await response.json();
            
            if (data.success) {
                // Remover comentário da lista
                const commentElement = document.getElementById(`comment-${commentId}`);
                if (commentElement) {
                    commentElement.remove();
                }
                
                // Atualizar contador de comentários
                const commentsCount = document.getElementById(`comments-count-${postId}`);
                const currentCount = parseInt(commentsCount.textContent.match(/\d+/)[0]);
                const newCount = Math.max(0, currentCount - 1);
                commentsCount.textContent = `💬 ${newCount} comentário${newCount !== 1 ? 's' : ''}`;
                
                // Se não há mais comentários, mostrar mensagem
                const commentsList = document.getElementById(`comments-list-${postId}`);
                if (commentsList.querySelectorAll('.comment').length === 0) {
                    commentsList.innerHTML = '<p style="text-align: center; color: #888; padding: 20px;">Nenhum comentário ainda</p>';
                }
            } else {
                alert(data.message || 'Erro ao apagar comentário');
            }
        } catch (error) {
            alert('Erro ao apagar comentário: ' + error);
            console.error('Erro:', error);
        }
    })();
}