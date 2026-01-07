document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-input');
    const urlParams = new URLSearchParams(window.location.search);
    const initialQuery = urlParams.get('q');
    
    // Se há uma query inicial, executar pesquisa
    if (initialQuery && initialQuery.trim()) {
        performSearch(initialQuery.trim());
    }
    
    // Pesquisar ao pressionar Enter
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            const query = this.value.trim();
            if (query) {
                // Atualizar URL sem recarregar página
                window.history.pushState({}, '', `search.php?q=${encodeURIComponent(query)}`);
                performSearch(query);
            }
        }
    });
    
    // Filtros
    const filterButtons = document.querySelectorAll('.filter-btn');
    filterButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            // Remover active de todos
            filterButtons.forEach(b => b.classList.remove('active'));
            // Adicionar active ao clicado
            this.classList.add('active');
            
            const filter = this.dataset.filter;
            applyFilter(filter);
        });
    });
});

function applyFilter(filter) {
    const peopleSection = document.getElementById('people-results');
    const postsSection = document.getElementById('posts-results');
    
    if (filter === 'all') {
        peopleSection.classList.remove('hidden');
        postsSection.classList.remove('hidden');
    } else if (filter === 'people') {
        peopleSection.classList.remove('hidden');
        postsSection.classList.add('hidden');
    } else if (filter === 'posts') {
        peopleSection.classList.add('hidden');
        postsSection.classList.remove('hidden');
    }
}

async function performSearch(query) {
    document.getElementById('search-title').textContent = `Resultados para "${query}"`;
    
    // Pesquisar pessoas e posts em paralelo
    await Promise.all([
        searchPeople(query),
        searchPosts(query)
    ]);
}

async function searchPeople(query) {
    const container = document.getElementById('people-container');
    container.innerHTML = '<p class="loading-message">Pesquisando pessoas...</p>';
    
    try {
        const response = await fetch(`../api/search_users.php?q=${encodeURIComponent(query)}`);
        const data = await response.json();
        
        if (data.success && data.users.length > 0) {
            container.innerHTML = '';
            data.users.forEach(user => {
                container.appendChild(createPersonCard(user));
            });
        } else {
            container.innerHTML = `
                <div class="no-results">
                    <div class="no-results-icon">👤</div>
                    <h4>Nenhuma pessoa encontrada</h4>
                    <p>Tente pesquisar com outros termos</p>
                </div>
            `;
        }
    } catch (error) {
        console.error('Erro ao pesquisar pessoas:', error);
        container.innerHTML = '<p class="loading-message" style="color: #ff6b6b;">Erro ao pesquisar pessoas</p>';
    }
}

async function searchPosts(query) {
    const container = document.getElementById('posts-container');
    container.innerHTML = '<p class="loading-message">Pesquisando posts...</p>';
    
    try {
        const response = await fetch(`../api/search_posts.php?q=${encodeURIComponent(query)}`);
        const data = await response.json();
        
        if (data.success && data.posts.length > 0) {
            container.innerHTML = '';
            data.posts.forEach(post => {
                // Verificar se a função createPostElement existe (do index.js)
                if (typeof createPostElement === 'function') {
                    container.appendChild(createPostElement(post));
                } else {
                    console.error('createPostElement não está definida');
                }
            });
        } else {
            container.innerHTML = `
                <div class="no-results">
                    <div class="no-results-icon">📝</div>
                    <h4>Nenhum post encontrado</h4>
                    <p>Tente pesquisar com outros termos</p>
                </div>
            `;
        }
    } catch (error) {
        console.error('Erro ao pesquisar posts:', error);
        container.innerHTML = '<p class="loading-message" style="color: #ff6b6b;">Erro ao pesquisar posts</p>';
    }
}

function createPersonCard(user) {
    const card = document.createElement('div');
    card.className = 'person-card';
    card.onclick = () => window.location.href = `../profile.php?user_id=${user.id}`;
    
    const initial = (user.nome || 'U').charAt(0).toUpperCase();
    
    card.innerHTML = `
        <div class="person-avatar">${initial}</div>
        <div class="person-info">
            <div class="person-name">${escapeHtml(user.nome)}</div>
            <div class="person-bio">${escapeHtml(user.bio || 'Sem biografia')}</div>
            <div class="person-stats">${user.followers_count || 0} seguidores • ${user.following_count || 0} seguindo</div>
        </div>
    `;
    
    return card;
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}