document.addEventListener('DOMContentLoaded', async function() {
    
    let currentUserId = null;
    let selectedUserId = null;
    let pollingInterval = null;
    let lastMessageCount = 0;

    const usersScroll = document.querySelector('.users-scroll');
    const chatHeader = document.querySelector('.chat-header');
    const chatMessages = document.querySelector('.chat-messages');
    const messageInput = document.getElementById('message-input');
    const sendButton = document.getElementById('send-button');

    // 1. Inicialização e Autenticação
    try {
        const response = await fetch('api/get_user_info.php');
        const data = await response.json();
        
        if (data.success) {
            currentUserId = data.user_id;
            loadConversations();
            // Atualiza lista de conversas a cada 10s
            setInterval(() => loadConversations(true), 10000); 
        } else {
            usersScroll.innerHTML = '<p style="text-align: center; padding: 20px;">Por favor faça login.</p>';
            return;
        }
    } catch (error) {
        console.error('Erro de autenticação:', error);
        return;
    }

    // 2. Funções Principais

    async function loadConversations(isBackgroundUpdate = false) {
        try {
            const response = await fetch('api/get_conversations.php');
            const data = await response.json();
            
            if (data.error) return;

            const users = Array.isArray(data) ? data : [];

            if (!isBackgroundUpdate) usersScroll.innerHTML = ''; 
            
            // Guarda scroll atual para evitar saltos visuais na atualização
            const currentScroll = usersScroll.scrollTop; 
            usersScroll.innerHTML = '';

            users.forEach(user => {
                const initial = (user.nome || 'U').charAt(0).toUpperCase();
                const userDiv = document.createElement('div');
                userDiv.className = `user ${user.id == selectedUserId ? 'active' : ''}`; 
                userDiv.dataset.userId = user.id;
                userDiv.innerHTML = `<div class="avatar">${initial}</div><span>${user.nome}</span>`;

                userDiv.addEventListener('click', function() {
                    if (selectedUserId === user.id) return;
                    
                    selectedUserId = user.id;
                    document.querySelectorAll('.user').forEach(el => el.classList.remove('active'));
                    this.classList.add('active');
                    
                    lastMessageCount = 0; 
                    loadMessages(selectedUserId, user.nome, initial, false); 
                    startChatPolling(user.id, user.nome, initial);
                });

                usersScroll.append(userDiv);
            });
            
            if(isBackgroundUpdate) usersScroll.scrollTop = currentScroll;

        } catch (error) {
            console.error(error);
        }
    }

    function startChatPolling(userId, userName, userInitial) {
        // Limpa intervalo anterior para evitar conflitos
        if (pollingInterval) clearInterval(pollingInterval);

        pollingInterval = setInterval(() => {
            if (selectedUserId === userId) {
                loadMessages(userId, userName, userInitial, true);
            }
        }, 3000);
    }

    async function loadMessages(userId, userName, userInitial, isBackgroundUpdate = false) {
        if (!isBackgroundUpdate) {
            chatHeader.innerHTML = `<div class="avatar">${userInitial}</div><h3>${userName}</h3>`;
            chatMessages.innerHTML = '<p style="text-align: center; color: #666; padding: 20px;">Carregando...</p>';
        }

        try {
            const response = await fetch(`api/get_messages.php?user_id=${userId}`);
            const data = await response.json();

            if (data.error) {
                if (!isBackgroundUpdate) chatMessages.innerHTML = `<p>${data.error}</p>`;
                return;
            }

            const messages = Array.isArray(data) ? data : [];

            // Otimização: Se o nº de mensagens for igual, não redesenha
            if (isBackgroundUpdate && messages.length === lastMessageCount) return; 
            
            lastMessageCount = messages.length;

            // Verifica se o user está no fundo da página (com margem de 100px)
            const isAtBottom = (chatMessages.scrollHeight - chatMessages.scrollTop) <= (chatMessages.clientHeight + 100);

            chatMessages.innerHTML = ''; 

            if (messages.length === 0) {
                chatMessages.innerHTML = '<p style="text-align: center; color: #666; padding: 20px;">Nenhuma mensagem ainda.</p>';
                return;
            }

            messages.forEach(message => {
                const isSentByMe = message.sender_id == currentUserId;
                const messageClass = isSentByMe ? 'sent' : 'received';
                const timeString = new Date(message.created_at).toLocaleTimeString('pt-PT', { hour: '2-digit', minute: '2-digit' });

                const msgHTML = `
                    <div class="message ${messageClass}">
                        <p>${escapeHtml(message.message_text)}</p>
                        <span class="timestamp">${timeString}</span>
                    </div>
                `;
                chatMessages.insertAdjacentHTML('beforeend', msgHTML);
            });

            // Scroll automático apenas no início ou se o user já estiver em baixo
            if (!isBackgroundUpdate || isAtBottom) {
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }

        } catch (error) {
            console.error(error);
        }
    }

    async function sendMessage() {
        const messageText = messageInput.value.trim();
        if (messageText === '' || !selectedUserId) return;

        sendButton.disabled = true;
        
        try {
            const response = await fetch('api/send_message.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ receiver_id: selectedUserId, message_text: messageText })
            });

            const data = await response.json();
            if (data.success || !data.error) {
                messageInput.value = '';
                // Atualização imediata sem esperar pelo polling
                loadMessages(selectedUserId, null, null, true); 
            }
        } catch (error) {
            alert('Erro ao enviar mensagem');
        } finally {
            sendButton.disabled = false;
            messageInput.focus();
        }
    }

    // 3. Event Listeners
    
    sendButton.addEventListener('click', sendMessage);
    
    messageInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) { 
            e.preventDefault(); 
            sendMessage(); 
        }
    });

    // Proteção XSS
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
});