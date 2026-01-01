document.addEventListener('DOMContentLoaded', function() {
    // Extrai o 'user_id' da URL
    const urlParams = new URLSearchParams(window.location.search);
    const userId = urlParams.get('user_id');

    // Constrói a URL da API para ir buscar o perfil
    const apiUrl = userId ? `api/get_profile.php?user_id=${userId}` : 'api/get_profile.php';

    // Vai buscar os dados do Perfil
    fetch(apiUrl)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                console.error(data.error);
                document.querySelector('.feed').innerHTML = `<p style="text-align:center; color: #ff6b6b;">${data.error}</p>`;
                return;
            }
            
            // Preenche as informações do perfil
            document.getElementById('profile-name').textContent = data.nome || 'Nome do Utilizador';
            document.getElementById('profile-location').textContent = data.location || '';
            document.getElementById('profile-bio').textContent = data.bio || 'Sem biografia.';
            
            // Preenche os status como blocos visuais
            const statsEl = document.getElementById('profile-stats');
            statsEl.innerHTML = `
                <div class="stat-item">
                    <strong>${data.posts_count || 0}</strong>
                    <span>Posts</span>
                </div>
                <div class="stat-item">
                    <strong>${data.followers_count || 0}</strong>
                    <span>Seguidores</span>
                </div>
                <div class="stat-item">
                    <strong>${data.following_count || 0}</strong>
                    <span>Seguindo</span>
                </div>
            `;

            // Atualiza o avatar com a letra inicial
            const avatarElement = document.getElementById('profile-avatar');
            const initialLetter = (data.nome || 'U').charAt(0).toUpperCase();
            avatarElement.setAttribute('data-initial', initialLetter);

            // Esconde ou mostra o botão de editar perfil
            const editBtn = document.querySelector('.edit-profile-btn');
            if (data.is_own_profile) {
                editBtn.style.display = 'block';
                // Preenche o formulário de edição com os dados atuais
                document.getElementById('name').value = data.nome || '';
                document.getElementById('bio').value = data.bio || '';
                document.getElementById('location').value = data.location || '';
            } else {
                editBtn.style.display = 'none';
            }

            // Botão de Seguir/Parar de Seguir
            const followBtn = document.getElementById('follow-unfollow-btn');
            if (!data.is_own_profile) {
                followBtn.style.display = 'block';
                followBtn.textContent = data.is_following ? 'Parar de Seguir' : 'Seguir';
                followBtn.addEventListener('click', () => toggleFollow(data.id, data.is_following));
            }

            // Carregar os posts do utilizador
            loadUserPosts(data.id);
        })
        .catch(error => {
            console.error('Erro ao carregar o perfil:', error);
            document.querySelector('.feed').innerHTML = `<p style="text-align:center; color: #ff6b6b;">Erro ao carregar o perfil.</p>`;
        });
    
    // Setup para fechar o formulário ao clicar fora
    setupFormOverlay();
});

async function toggleFollow(userId, isFollowing) {
    const url = isFollowing ? 'api/unfollow_user.php' : 'api/follow_user.php';
    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ following_id: userId })
        });
        const result = await response.json();
        if (result.success) {
            // Recarregar a página para atualizar o estado do botão e contagem de seguidores
            window.location.reload();
        } else {
            throw new Error(result.error || 'Ocorreu um erro');
        }
    } catch (error) {
        console.error('Erro ao seguir/parar de seguir:', error);
        alert(error.message);
    }
}

// Carregar os posts de um user especifico
async function loadUserPosts(userId) {
    const container = document.getElementById('posts-container');
    try {
        const response = await fetch(`api/get_posts.php?user_id=${userId}`);
        const data = await response.json();

        if (data.success && data.posts.length > 0) {
            container.innerHTML = '';
            data.posts.forEach(post => {
                // A função createPostElement é definida em index.js
                container.appendChild(createPostElement(post));
            });
        } else {
            container.innerHTML = '<p style="text-align: center; color: #999999; padding: 20px;">Este utilizador ainda não fez publicações.</p>';
        }
    } catch (error) {
        console.error('Erro ao carregar posts:', error);
        container.innerHTML = '<p style="text-align: center; color: #ff6b6b; padding: 20px;">Erro ao carregar posts.</p>';
    }
}


function toggleEditForm(event) {
    // Se o evento existir, impede que ele chegue ao 'document' e feche o form logo de seguida
    if (event) {
        event.stopPropagation();
    }

    const form = document.getElementById('edit-profile-form');
    
    if (form.style.display === 'none' || form.style.display === '') {
        form.style.display = 'block';
        document.body.classList.add('form-open');
    } else {
        form.style.display = 'none';
        document.body.classList.remove('form-open');
    }
}

function setupFormOverlay() {
    const form = document.getElementById('edit-profile-form');
    
    // Impede que cliques DENTRO do formulário o fechem
    form.addEventListener('click', function(event) {
        event.stopPropagation();
    });
    
    // Fecha ao clicar em qualquer lugar do documento (fora do form)
    document.addEventListener('click', function(event) {
        if (form.style.display === 'block') {
            form.style.display = 'none';
            document.body.classList.remove('form-open');
        }
    });
}

function updateProfile(event) {
    event.preventDefault();
    event.stopPropagation();
    
    const form = event.target; // Certifica-te que o onsubmit está na tag <form>
    const btn = form.querySelector('button[type="submit"]');
    const originalText = btn.textContent;

    btn.disabled = true;
    btn.textContent = 'Guardando...';

    // Usar getElementById é mais seguro para garantir que apanha o valor certo
    const data = {
        name: document.getElementById('name').value, 
        bio: document.getElementById('bio').value,
        location: document.getElementById('location').value
    };

    console.log('Dados a enviar:', data); // Verifica na consola se "name" tem texto

    // Adiciona uma barra "/" no início se a pasta api estiver na raiz do site
    fetch('api/update_profile.php', { 
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)  
    })
    .then(async response => {
        // Lemos o texto da resposta independentemente do status
        const text = await response.text(); 
        
        // Tenta fazer parse do JSON
        let json;
        try {
            json = JSON.parse(text);
        } catch (e) {
            console.error('Resposta não é JSON válido:', text);
            throw new Error(`Erro de servidor: ${response.status}`);
        }

        if (!response.ok) {
            // Se o PHP devolveu 400 ou 500, usamos a mensagem dele
            throw new Error(json.message || `Erro HTTP! status: ${response.status}`);
        }
        
        return json; // Retorna o objeto JSON para o próximo .then
    })
    .then(data => {
        if (data.success) {
            alert('Perfil atualizado com sucesso!');
            document.getElementById('edit-profile-form').style.display = 'none';
            document.body.classList.remove('form-open');
            setTimeout(() => window.location.reload(), 500);
        } else {
            alert('Erro: ' + (data.message || 'Erro desconhecido'));
        }
    })
    .catch(error => {
        console.error('Erro detalhado:', error);
        alert(error.message); // Agora o alerta vai mostrar a mensagem real do PHP (ex: "Nome é obrigatório")
    })
    .finally(() => {
        btn.disabled = false;
        btn.textContent = originalText;
    });
}
