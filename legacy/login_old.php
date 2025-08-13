<?php
session_start();
require_once 'config.php';
require_once 'db_connect.php';

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
        $usuario = autenticarUsuario($email, $senha);
        if ($usuario) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            $_SESSION['usuario_email'] = $usuario['email'];
            $_SESSION['usuario_tipo'] = $usuario['tipo_usuario'];
            
            // Atualizar último login
            atualizarUltimoLogin($usuario['id']);
            
            // Redirecionar baseado no tipo de usuário
            if ($usuario['tipo_usuario'] == 'admin') {
                header('Location: admin.php');
            } else {
                header('Location: index.php');
            }
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
        // Verificar se email já existe
        if (verificarEmailExiste($email)) {
            $erro = 'Este email já está cadastrado.';
        } else {
            // Criar usuário
            $usuario_id = criarUsuario($nome, $email, $senha, $telefone);
            if ($usuario_id) {
                $sucesso = 'Conta criada com sucesso! Faça login para continuar.';
            } else {
                $erro = 'Erro ao criar conta. Tente novamente.';
            }
        }
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
                radial-gradient(circle at 20% 80%, rgba(233, 69, 96, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(15, 52, 96, 0.1) 0%, transparent 50%);
            animation: backgroundShift 20s ease-in-out infinite;
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
            color: var(--text-light);
            font-size: 1rem;
            transition: all var(--transition-normal);
            backdrop-filter: blur(10px);
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
        
        .auth-divider {
            text-align: center;
            margin: var(--spacing-xl) 0;
            position: relative;
            color: var(--text-muted);
            font-size: 0.9rem;
        }
        
        .auth-divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: var(--glass-border);
        }
        
        .auth-divider span {
            background: var(--dark-bg);
            padding: 0 var(--spacing-lg);
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
        }
        
        .back-to-site:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(-5px);
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
                    <button type="button" class="password-toggle" onclick="togglePassword(this)">👁️</button>
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
                    <button type="button" class="password-toggle" onclick="togglePassword(this)">👁️</button>
                </div>
                
                <div class="form-group">
                    <input type="password" class="form-input" name="confirmar_senha" placeholder=" " required>
                    <label class="form-label">Confirmar Senha</label>
                    <button type="button" class="password-toggle" onclick="togglePassword(this)">👁️</button>
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
            const input = button.previousElementSibling;
            if (input.type === 'password') {
                input.type = 'text';
                button.textContent = '🙈';
            } else {
                input.type = 'password';
                button.textContent = '👁️';
            }
        }
        
        // Adicionar efeitos de animação aos inputs
        document.querySelectorAll('.form-input').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentNode.classList.add('focused');
            });
            
            input.addEventListener('blur', function() {
                if (!this.value) {
                    this.parentNode.classList.remove('focused');
                }
            });
        });
        
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
        
        document.querySelector('input[name="confirmar_senha"]').addEventListener('input', function() {
            const senha = document.querySelector('input[name="senha"]').value;
            if (this.value && this.value !== senha) {
                this.style.borderColor = '#dc3545';
            } else {
                this.style.borderColor = '';
            }
        });
    </script>
    
    <!-- Animações e Interatividade -->
    <script src="animations.js"></script>
</body>
</html>

