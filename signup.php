<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Conta - Sistema</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1>Criar Conta</h1>
                <p>Preencha os dados para se registar</p>
            </div>

            <form id="signupForm">
                <div class="form-group">
                    <input 
                        type="text" 
                        id="name" 
                        name="name"
                        placeholder="seu nome" 
                        required
                    >
                </div>

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
                        placeholder="escolha uma palavra-passe" 
                        required
                    >
                </div>

                <div class="form-group">
                    <input 
                        type="password" 
                        id="confirm-password" 
                        name="confirm_password"
                        placeholder="confirme a palavra-passe" 
                        required
                    >
                </div>

                <button type="submit" class="login-btn">Criar Conta</button>
            </form>

            <div class="signup-link">
                Já tem conta? <a href="login.php">Fazer login</a>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('signupForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm-password').value;
            
            if (password !== confirmPassword) {
                alert('As palavras-passe não correspondem!');
                return;
            }
            
            // Enviar dados via fetch
            fetch('api/signup.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    name: name,
                    email: email,
                    password: password
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    window.location.href = 'login.php';
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                alert('Erro ao processar registo: ' + error);
            });
        });
    </script>
</body>
</html>
