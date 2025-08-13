<?php
session_start();
require_once 'config.php';

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $confirmar_senha = $_POST['confirmar_senha'] ?? '';
    $telefone = trim($_POST['telefone'] ?? '');
    $endereco = trim($_POST['endereco'] ?? '');
    $cidade = trim($_POST['cidade'] ?? '');
    $cep = trim($_POST['cep'] ?? '');
    
    // Validações
    if (empty($nome) || empty($email) || empty($senha)) {
        $erro = 'Nome, email e senha são obrigatórios.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'Email inválido.';
    } elseif (strlen($senha) < 6) {
        $erro = 'A senha deve ter pelo menos 6 caracteres.';
    } elseif ($senha !== $confirmar_senha) {
        $erro = 'As senhas não coincidem.';
    } else {
        // Verificar se o email já existe
        if (emailJaExiste($email)) {
            $erro = 'Este email já está cadastrado.';
        } else {
            // Registrar usuário
            if (registrarUsuario($nome, $email, $senha, $telefone, $endereco, $cidade, $cep)) {
                $sucesso = 'Usuário registrado com sucesso! Você pode fazer login agora.';
                // Limpar campos
                $nome = $email = $telefone = $endereco = $cidade = $cep = '';
            } else {
                $erro = 'Erro ao registrar usuário. Tente novamente.';
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
    <?php include 'header.php'; ?>
    <title>Registro - E-commerce Project</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 0;
        }
        
        .auth-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border-radius: var(--border-radius);
            border: 1px solid var(--glass-border);
            padding: 3rem;
            width: 100%;
            max-width: 500px;
            box-shadow: var(--shadow-heavy);
            position: relative;
            overflow: hidden;
        }
        
        .auth-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--primary-color), var(--accent-color));
        }
        
        .auth-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .auth-header h1 {
            font-size: 2.2rem;
            margin-bottom: 0.5rem;
            background: linear-gradient(45deg, var(--text-light), var(--primary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .auth-header p {
            color: var(--text-muted);
            font-size: 1rem;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        
        .auth-links {
            text-align: center;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid var(--glass-border);
        }
        
        .auth-links a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            transition: all var(--transition-medium);
        }
        
        .auth-links a:hover {
            color: var(--accent-color);
            text-shadow: 0 0 10px rgba(233, 69, 96, 0.5);
        }
        
        .password-strength {
            margin-top: 0.5rem;
            font-size: 0.8rem;
        }
        
        .strength-weak { color: #dc3545; }
        .strength-medium { color: #ffc107; }
        .strength-strong { color: #28a745; }
        
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .auth-card {
                margin: 1rem;
                padding: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Preloader -->
    <div class="preloader" id="preloader">
        <div class="spinner"></div>
    </div>

    <div class="auth-container">
        <div class="auth-card fade-in">
            <div class="auth-header">
                <h1>Criar Conta</h1>
                <p>Junte-se à nossa comunidade de tecnologia</p>
            </div>

            <?php if ($erro): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($erro); ?>
                </div>
            <?php endif; ?>

            <?php if ($sucesso): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($sucesso); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="registro.php" id="registroForm">
                <div class="form-group">
                    <label for="nome">Nome Completo *</label>
                    <input type="text" id="nome" name="nome" required 
                           value="<?php echo htmlspecialchars($nome ?? ''); ?>"
                           placeholder="Seu nome completo">
                </div>

                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" required 
                           value="<?php echo htmlspecialchars($email ?? ''); ?>"
                           placeholder="seu@email.com">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="senha">Senha *</label>
                        <input type="password" id="senha" name="senha" required 
                               placeholder="Mínimo 6 caracteres" minlength="6">
                        <div class="password-strength" id="passwordStrength"></div>
                    </div>

                    <div class="form-group">
                        <label for="confirmar_senha">Confirmar Senha *</label>
                        <input type="password" id="confirmar_senha" name="confirmar_senha" required 
                               placeholder="Confirme sua senha">
                    </div>
                </div>

                <div class="form-group">
                    <label for="telefone">Telefone</label>
                    <input type="tel" id="telefone" name="telefone" 
                           value="<?php echo htmlspecialchars($telefone ?? ''); ?>"
                           placeholder="(11) 99999-9999">
                </div>

                <div class="form-group">
                    <label for="endereco">Endereço</label>
                    <input type="text" id="endereco" name="endereco" 
                           value="<?php echo htmlspecialchars($endereco ?? ''); ?>"
                           placeholder="Rua, número, complemento">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="cidade">Cidade</label>
                        <input type="text" id="cidade" name="cidade" 
                               value="<?php echo htmlspecialchars($cidade ?? ''); ?>"
                               placeholder="Sua cidade">
                    </div>

                    <div class="form-group">
                        <label for="cep">CEP</label>
                        <input type="text" id="cep" name="cep" 
                               value="<?php echo htmlspecialchars($cep ?? ''); ?>"
                               placeholder="00000-000" maxlength="9">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">
                    Criar Conta
                </button>
            </form>

            <div class="auth-links">
                <p>Já tem uma conta? <a href="login.php">Fazer Login</a></p>
                <p><a href="index.php">← Voltar para a loja</a></p>
            </div>
        </div>
    </div>

    <footer>
        <div class="container">
            <p>&copy; 2025 E-commerce Project. Todos os direitos reservados.</p>
            <p>Desenvolvido por <a href="https://dexo-mu.vercel.app/" class="dexo-credit">Dexo</a></p>
        </div>
    </footer>

    <script>
        // Remover preloader
        window.addEventListener('load', function() {
            document.getElementById('preloader').classList.add('hidden');
        });

        // Validação de força da senha
        document.getElementById('senha').addEventListener('input', function() {
            const senha = this.value;
            const strengthDiv = document.getElementById('passwordStrength');
            
            if (senha.length === 0) {
                strengthDiv.textContent = '';
                return;
            }
            
            let strength = 0;
            if (senha.length >= 6) strength++;
            if (/[A-Z]/.test(senha)) strength++;
            if (/[0-9]/.test(senha)) strength++;
            if (/[^A-Za-z0-9]/.test(senha)) strength++;
            
            if (strength <= 1) {
                strengthDiv.textContent = 'Senha fraca';
                strengthDiv.className = 'password-strength strength-weak';
            } else if (strength <= 2) {
                strengthDiv.textContent = 'Senha média';
                strengthDiv.className = 'password-strength strength-medium';
            } else {
                strengthDiv.textContent = 'Senha forte';
                strengthDiv.className = 'password-strength strength-strong';
            }
        });

        // Validação de confirmação de senha
        document.getElementById('confirmar_senha').addEventListener('input', function() {
            const senha = document.getElementById('senha').value;
            const confirmar = this.value;
            
            if (confirmar && senha !== confirmar) {
                this.setCustomValidity('As senhas não coincidem');
            } else {
                this.setCustomValidity('');
            }
        });

        // Máscara para CEP
        document.getElementById('cep').addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            if (value.length >= 5) {
                value = value.replace(/^(\d{5})(\d)/, '$1-$2');
            }
            this.value = value;
        });

        // Máscara para telefone
        document.getElementById('telefone').addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            if (value.length >= 10) {
                value = value.replace(/^(\d{2})(\d{4,5})(\d{4})$/, '($1) $2-$3');
            } else if (value.length >= 6) {
                value = value.replace(/^(\d{2})(\d{4})/, '($1) $2');
            } else if (value.length >= 2) {
                value = value.replace(/^(\d{2})/, '($1) ');
            }
            this.value = value;
        });

        // Animação de entrada
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                document.querySelector('.auth-card').style.opacity = '1';
                document.querySelector('.auth-card').style.transform = 'translateY(0)';
            }, 100);
        });
    </script>
</body>
</html>

