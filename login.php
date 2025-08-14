<?php
session_start();

// Redirecionar se já estiver logado
if (isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit();
}

$erro = '';
$sucesso = '';

// Processar login
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];
    
    if (empty($email) || empty($senha)) {
        $erro = 'Por favor, preencha todos os campos.';
    } else {
        // Simulação de autenticação (em produção, usar banco de dados real)
        if ($email === 'admin@teste.com' && $senha === '123456') {
            $_SESSION['usuario_id'] = 1;
            $_SESSION['usuario_nome'] = 'Administrador';
            $_SESSION['usuario_email'] = $email;
            $_SESSION['usuario_tipo'] = 'admin';
            
            header('Location: admin.php');
            exit();
        } elseif ($email === 'user@teste.com' && $senha === '123456') {
            $_SESSION['usuario_id'] = 2;
            $_SESSION['usuario_nome'] = 'Usuário Teste';
            $_SESSION['usuario_email'] = $email;
            $_SESSION['usuario_tipo'] = 'cliente';
            
            header('Location: index.php');
            exit();
        } else {
            $erro = 'Email ou senha incorretos.';
        }
    }
}

// Processar registro
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['registro'])) {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];
    $telefone = trim($_POST['telefone']);
    
    if (empty($nome) || empty($email) || empty($senha) || empty($confirmar_senha)) {
        $erro = 'Por favor, preencha todos os campos obrigatórios.';
    } elseif ($senha !== $confirmar_senha) {
        $erro = 'As senhas não coincidem.';
    } elseif (strlen($senha) < 6) {
        $erro = 'A senha deve ter pelo menos 6 caracteres.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'Email inválido.';
    } else {
        // Simulação de criação de usuário
        $sucesso = 'Conta criada com sucesso! Use: user@teste.com / 123456 para fazer login.';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - E-commerce Project</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="components.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css">
    <?php include 'header.php'; ?>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Montserrat:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: var(--spacing-lg);
            background: linear-gradient(135deg, var(--dark-bg) 0%, var(--darker-bg) 100%);
            position: relative;
            overflow: hidden;
        }
        
        .auth-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 80%, rgba(233, 69, 96, 0.08) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(15, 52, 96, 0.08) 0%, transparent 50%);
            pointer-events: none;
        }
        
        .auth-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px) saturate(180%);
            border-radius: var(--border-radius-xl);
            padding: var(--spacing-3xl);
            border: 1px solid var(--glass-border);
            box-shadow: var(--shadow-xl);
            width: 100%;
            max-width: 450px;
            position: relative;
            z-index: 1;
            animation: slideInUp 0.8s ease-out;
        }
        
        .auth-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--accent-color));
            border-radius: var(--border-radius-xl) var(--border-radius-xl) 0 0;
        }
        
        .auth-header {
            text-align: center;
            margin-bottom: var(--spacing-3xl);
        }
        
        .auth-logo {
            font-family: 'Montserrat', sans-serif;
            font-size: 2rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: var(--spacing-lg);
            display: block;
        }
        
        .auth-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-light);
            margin-bottom: var(--spacing-sm);
        }
        
        .auth-subtitle {
            color: var(--text-muted);
            font-size: 0.9rem;
        }
        
        .auth-tabs {
            display: flex;
            margin-bottom: var(--spacing-xl);
            background: rgba(255, 255, 255, 0.05);
            border-radius: var(--border-radius-full);
            padding: var(--spacing-xs);
            backdrop-filter: blur(10px);
        }
        
        .auth-tab {
            flex: 1;
            background: none;
            border: none;
            color: var(--text-muted);
            padding: var(--spacing-md);
            border-radius: var(--border-radius-full);
            cursor: pointer;
            transition: all var(--transition-normal);
            font-weight: 500;
        }
        
        .auth-tab.active {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: white;
            box-shadow: var(--shadow-md);
        }
        
        .auth-form {
            display: none;
        }
        
        .auth-form.active {
            display: block;
            animation: fadeInUp 0.5s ease-out;
        }
        
        .form-group {
            margin-bottom: var(--spacing-xl);
            position: relative;
        }
        
        .form-input {
            width: 100%;
            padding: var(--spacing-lg);
            border: 2px solid var(--glass-border);
            border-radius: var(--border-radius-md);
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(10px);
            color: var(--text-light);
            font-size: 1rem;
            transition: all var(--transition-normal);
        }
        
        .form-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(233, 69, 96, 0.2);
            background: rgba(255, 255, 255, 0.12);
            transform: translateY(-2px);
        }
        
        .form-input::placeholder {
            color: var(--text-dark);
        }
        
        .form-label {
            position: absolute;
            top: var(--spacing-lg);
            left: var(--spacing-lg);
            color: var(--text-dark);
            transition: all var(--transition-normal);
            pointer-events: none;
            background: transparent;
        }
        
        .form-input:focus + .form-label,
        .form-input:not(:placeholder-shown) + .form-label {
            top: -8px;
            left: var(--spacing-md);
            font-size: 0.75rem;
            color: var(--primary-color);
            background: var(--dark-bg);
            padding: 0 var(--spacing-xs);
        }
        
        .auth-button {
            width: 100%;
            padding: var(--spacing-lg);
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: white;
            border: none;
            border-radius: var(--border-radius-md);
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all var(--transition-normal);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            position: relative;
            overflow: hidden;
        }
        
        .auth-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left var(--transition-slow);
        }
        
        .auth-button:hover::before {
            left: 100%;
        }
        
        .auth-button:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }
        
        .auth-button:active {
            transform: translateY(0);
        }
        
        .auth-link {
            text-align: center;
            margin-top: var(--spacing-xl);
        }
        
        .auth-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: color var(--transition-normal);
        }
        
        .auth-link a:hover {
            color: var(--accent-color);
        }
        
        .back-to-site {
            position: absolute;
            top: var(--spacing-lg);
            left: var(--spacing-lg);
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            color: var(--text-light);
            padding: var(--spacing-sm) var(--spacing-lg);
            border-radius: var(--border-radius-full);
            text-decoration: none;
            transition: all var(--transition-normal);
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
            font-size: 0.9rem;
            z-index: 10;
        }
        
        .back-to-site:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(-5px);
            box-shadow: var(--shadow-md);
        }
        
        .password-toggle {
            position: absolute;
            right: var(--spacing-lg);
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            font-size: 1.2rem;
            transition: color var(--transition-normal);
        }
        
        .password-toggle:hover {
            color: var(--text-light);
        }
        
        .alert {
            padding: var(--spacing-lg);
            border-radius: var(--border-radius-md);
            margin-bottom: var(--spacing-xl);
            font-weight: 500;
            backdrop-filter: blur(10px);
            border: 1px solid;
        }
        
        .alert-success {
            background: rgba(40, 167, 69, 0.2);
            border-color: rgba(40, 167, 69, 0.3);
            color: #28a745;
        }
        
        .alert-error {
            background: rgba(220, 53, 69, 0.2);
            border-color: rgba(220, 53, 69, 0.3);
            color: #dc3545;
        }
        
        .demo-info {
            background: rgba(255, 193, 7, 0.2);
            border: 1px solid rgba(255, 193, 7, 0.3);
            color: #ffc107;
            padding: var(--spacing-lg);
            border-radius: var(--border-radius-md);
            margin-bottom: var(--spacing-xl);
            backdrop-filter: blur(10px);
            font-size: 0.9rem;
        }
        
        .demo-info strong {
            display: block;
            margin-bottom: var(--spacing-sm);
        }
        
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @media (max-width: 480px) {
            .auth-card {
                padding: var(--spacing-xl);
                margin: var(--spacing-md);
            }
            
            .auth-logo {
                font-size: 1.5rem;
            }
            
            .auth-title {
                font-size: 1.2rem;
            }
            
            .back-to-site {
                position: relative;
                top: auto;
                left: auto;
                margin-bottom: var(--spacing-lg);
                align-self: flex-start;
            }
            
            .auth-container {
                padding: var(--spacing-md);
            }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <a href="index.php" class="back-to-site">
            ← Voltar ao Site
        </a>
        
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-logo">E-commerce Project</div>
                <h1 class="auth-title">Bem-vindo de volta!</h1>
                <p class="auth-subtitle">Acesse sua conta ou crie uma nova</p>
            </div>
            
            <div class="demo-info">
                <strong>🔑 Contas de Demonstração:</strong>
                Admin: admin@teste.com / 123456<br>
                Usuário: user@teste.com / 123456
            </div>
            
            <?php if ($erro): ?>
                <div class="alert alert-error">
                    <strong>Erro:</strong> <?php echo htmlspecialchars($erro); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($sucesso): ?>
                <div class="alert alert-success">
                    <strong>Sucesso:</strong> <?php echo htmlspecialchars($sucesso); ?>
                </div>
            <?php endif; ?>
            
            <div class="auth-tabs">
                <button class="auth-tab active" onclick="switchTab('login')">Login</button>
                <button class="auth-tab" onclick="switchTab('registro')">Registro</button>
            </div>
            
            <!-- Formulário de Login -->
            <form class="auth-form active" id="login-form" method="POST">
                <div class="form-group">
                    <input type="email" class="form-input" name="email" placeholder=" " required>
                    <label class="form-label">Email</label>
                </div>
                
                <div class="form-group">
                    <input type="password" class="form-input" name="senha" placeholder=" " required>
                    <label class="form-label">Senha</label>
                    <button type="button" class="password-toggle" onclick="togglePassword(this)"><i class="fa-solid fa-eye"></i></button>
                </div>
                
                <button type="submit" name="login" class="auth-button">
                    Entrar
                </button>
                
                <div class="auth-link">
                    <a href="#" onclick="switchTab('registro')">Não tem uma conta? Registre-se</a>
                </div>
            </form>
            
            <!-- Formulário de Registro -->
            <form class="auth-form" id="registro-form" method="POST">
                <div class="form-group">
                    <input type="text" class="form-input" name="nome" placeholder=" " required>
                    <label class="form-label">Nome Completo</label>
                </div>
                
                <div class="form-group">
                    <input type="email" class="form-input" name="email" placeholder=" " required>
                    <label class="form-label">Email</label>
                </div>
                
                <div class="form-group">
                    <input type="tel" class="form-input" name="telefone" placeholder=" ">
                    <label class="form-label">Telefone (opcional)</label>
                </div>
                
                <div class="form-group">
                    <input type="password" class="form-input" name="senha" placeholder=" " required>
                    <label class="form-label">Senha</label>
                    <button type="button" class="password-toggle" onclick="togglePassword(this)"><i class="fa-solid fa-eye"></i></button>
                </div>
                
                <div class="form-group">
                    <input type="text" class="form-input" name="confirmar_senha" placeholder=" " required>
                    <label class="form-label">Confirmar Senha</label>
                    <button type="button" class="password-toggle" onclick="togglePassword(this)"><i class="fa-solid fa-eye"></i></button>
                </div>
                
                <button type="submit" name="registro" class="auth-button">
                    Criar Conta
                </button>
                
                <div class="auth-link">
                    <a href="#" onclick="switchTab('login')">Já tem uma conta? Faça login</a>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        function switchTab(tab) {
            // Atualizar tabs
            document.querySelectorAll('.auth-tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.auth-form').forEach(f => f.classList.remove('active'));
            
            if (tab === 'login') {
                document.querySelector('.auth-tab:first-child').classList.add('active');
                document.getElementById('login-form').classList.add('active');
            } else {
                document.querySelector('.auth-tab:last-child').classList.add('active');
                document.getElementById('registro-form').classList.add('active');
            }
        }
        
        function togglePassword(button) {
            // Subindo para o elemento pai que contém tanto o input quanto o botão
            const parentDiv = button.parentNode;

            // Pegando o input dentro da div
            const input = parentDiv.querySelector('input');

            const currentType = input.type;

            if (currentType === 'password') {
                input.type = 'text';  // Muda para tipo texto
                button.innerHTML = '<i class="fa-solid fa-eye-slash"></i>'; // Altera o ícone
            } else {
                input.type = 'password'; // Retorna para tipo senha
                button.innerHTML = '<i class="fa-solid fa-eye"></i>'; // Retorna o ícone original
            }
        }
        
        // Validação em tempo real
        document.querySelectorAll('input[name="email"]').forEach(input => {
            input.addEventListener('input', function() {
                if (this.value && !this.value.includes('@')) {
                    this.style.borderColor = '#dc3545';
                } else {
                    this.style.borderColor = '';
                }
            });
        });
        
        document.querySelector('input[name="confirmar_senha"]')?.addEventListener('input', function() {
            const senha = document.querySelector('input[name="senha"]').value;
            if (this.value && this.value !== senha) {
                this.style.borderColor = '#dc3545';
            } else {
                this.style.borderColor = '';
            }
        });
        
        // Efeito de ripple nos botões
        document.querySelectorAll('.auth-button').forEach(button => {
            button.addEventListener('click', function(e) {
                const ripple = document.createElement('span');
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;
                
                ripple.style.cssText = `
                    position: absolute;
                    width: ${size}px;
                    height: ${size}px;
                    left: ${x}px;
                    top: ${y}px;
                    background: rgba(255, 255, 255, 0.3);
                    border-radius: 50%;
                    transform: scale(0);
                    animation: ripple 0.6s ease-out;
                    pointer-events: none;
                `;
                
                this.appendChild(ripple);
                
                setTimeout(() => {
                    ripple.remove();
                }, 600);
            });
        });
        
        // Adicionar keyframe para ripple
        const style = document.createElement('style');
        style.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(2);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    </script>
    
    <!-- Animações e Interatividade -->
    <script src="animations.js"></script>
</body>
</html>

