<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema Moderno</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1>Autenticação</h1>
                <p>Introduza as suas credenciais</p>
            </div>

            <form id="loginForm">
                <div class="form-group">
                    <input 
                        type="email" 
                        id="email" 
                        name="email"
                        placeholder="seu@email.com" 
                        required
                    >
                </div>

                <div class="form-group">
                    <input 
                        type="password" 
                        id="password" 
                        name="password"
                        placeholder="sua palavra-passe" 
                        required
                    >
                </div>

                <div class="remember-forgot">
                    <label class="remember-label">
                        <input type="checkbox" id="remember" name="remember"> Manter-me autenticado
                    </label>
                    <a href="#forgot">Esqueceu a palavra-passe?</a>
                </div>

                <button type="submit" class="login-btn">Entrar</button>
            </form>

            <div class="signup-link">
                Não tem conta? <a href="signup.php">Criar uma</a>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            
            // Enviar dados via fetch
            fetch('api/login.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    email: email,
                    password: password
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    window.location.href = 'index.php';
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                alert('Erro ao processar login: ' + error);
            });
        });
    </script>
</body>
</html>