<?php
session_start();
require_once 'config.php';
require_once 'db_connect.php';

// Verificar se está logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

$usuario = obterUsuario($_SESSION['usuario_id']);
if (!$usuario) {
    session_destroy();
    header('Location: login.php');
    exit();
}

$erro = '';
$sucesso = '';

// Processar atualização de perfil
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['atualizar_perfil'])) {
    $nome = trim($_POST['nome']);
    $telefone = trim($_POST['telefone']);
    $endereco = trim($_POST['endereco']);
    $cidade = trim($_POST['cidade']);
    $cep = trim($_POST['cep']);
    
    if (empty($nome)) {
        $erro = 'Nome é obrigatório.';
    } else {
        if (atualizarPerfilUsuario($_SESSION['usuario_id'], $nome, $telefone, $endereco, $cidade, $cep)) {
            $sucesso = 'Perfil atualizado com sucesso!';
            $usuario = obterUsuario($_SESSION['usuario_id']); // Recarregar dados
            $_SESSION['usuario_nome'] = $usuario['nome']; // Atualizar sessão
        } else {
            $erro = 'Erro ao atualizar perfil.';
        }
    }
}

// Processar alteração de senha
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['alterar_senha'])) {
    $senhaAtual = $_POST['senha_atual'];
    $novaSenha = $_POST['nova_senha'];
    $confirmarSenha = $_POST['confirmar_nova_senha'];
    
    if (empty($senhaAtual) || empty($novaSenha) || empty($confirmarSenha)) {
        $erro = 'Todos os campos de senha são obrigatórios.';
    } elseif ($novaSenha !== $confirmarSenha) {
        $erro = 'A nova senha e confirmação não coincidem.';
    } elseif (strlen($novaSenha) < 6) {
        $erro = 'A nova senha deve ter pelo menos 6 caracteres.';
    } else {
        if (alterarSenhaUsuario($_SESSION['usuario_id'], $senhaAtual, $novaSenha)) {
            $sucesso = 'Senha alterada com sucesso!';
        } else {
            $erro = 'Senha atual incorreta.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Perfil - E-commerce Project</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="components.css">
    <?php include 'header.php'; ?>
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <a href="index.php" class="logo">E-commerce Project</a>
                <nav>
                    <ul>
                        <li><a href="index.php">Início</a></li>
                        <li><a href="produtos.php">Produtos</a></li>
                        <li><a href="sobre.php">Sobre</a></li>
                        <li><a href="contato.php">Contato</a></li>
                        <?php if (isset($_SESSION['usuario_id'])): ?>
                            <li><a href="perfil.php">Meu Perfil</a></li>
                            <?php if ($_SESSION['usuario_tipo'] == 'admin'): ?>
                                <li><a href="admin.php">Admin</a></li>
                            <?php endif; ?>
                            <li><a href="logout.php">Sair</a></li>
                        <?php else: ?>
                            <li><a href="login.php">Login</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <a href="carrinho.php" class="cart-icon">
                    <i class="fa-solid fa-cart-shopping"></i> Carrinho (<?php echo count($_SESSION['cart'] ?? []); ?>)
                </a>
            </div>
        </div>
    </header>

    <main>
        <div class="container">
            <div class="breadcrumb">
                <a href="index.php" class="breadcrumb-item">Início</a>
                <span class="breadcrumb-separator">›</span>
                <span class="breadcrumb-item active">Meu Perfil</span>
            </div>

            <h1 class="fade-in">Meu Perfil</h1>
            
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

            <div class="tabs">
                <div class="tab-nav">
                    <button class="tab-button active" onclick="switchTab('dados')">Dados Pessoais</button>
                    <button class="tab-button" onclick="switchTab('senha')">Alterar Senha</button>
                    <button class="tab-button" onclick="switchTab('pedidos')">Meus Pedidos</button>
                </div>

                <!-- Tab Dados Pessoais -->
                <div class="tab-content active" id="dados-tab">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fa-solid fa-user"></i></div>
                        <h3 class="feature-title">Informações Pessoais</h3>
                        
                        <form method="POST" style="margin-top: 2rem;">
                            <div class="form-group floating-label">
                                <input type="text" name="nome" value="<?php echo htmlspecialchars($usuario['nome']); ?>" placeholder=" " required>
                                <label>Nome Completo</label>
                            </div>
                            
                            <div class="form-group floating-label">
                                <input type="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" placeholder=" " disabled>
                                <label>Email (não pode ser alterado)</label>
                            </div>
                            
                            <div class="form-group floating-label">
                                <input type="tel" name="telefone" value="<?php echo htmlspecialchars($usuario['telefone'] ?? ''); ?>" placeholder=" ">
                                <label>Telefone</label>
                            </div>
                            
                            <div class="form-group floating-label">
                                <input type="text" name="endereco" value="<?php echo htmlspecialchars($usuario['endereco'] ?? ''); ?>" placeholder=" ">
                                <label>Endereço</label>
                            </div>
                            
                            <div class="form-group floating-label">
                                <input type="text" name="cidade" value="<?php echo htmlspecialchars($usuario['cidade'] ?? ''); ?>" placeholder=" ">
                                <label>Cidade</label>
                            </div>
                            
                            <div class="form-group floating-label">
                                <input type="text" name="cep" value="<?php echo htmlspecialchars($usuario['cep'] ?? ''); ?>" placeholder=" ">
                                <label>CEP</label>
                            </div>
                            
                            <button type="submit" name="atualizar_perfil" class="btn btn-primary">
                                Atualizar Perfil
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Tab Alterar Senha -->
                <div class="tab-content" id="senha-tab">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fa-solid fa-lock"></i></div>
                        <h3 class="feature-title">Alterar Senha</h3>
                        
                        <form method="POST" style="margin-top: 2rem;">
                            <div class="form-group floating-label">
                                <input type="password" name="senha_atual" placeholder=" " required>
                                <label>Senha Atual</label>
                            </div>
                            
                            <div class="form-group floating-label">
                                <input type="password" name="nova_senha" placeholder=" " required>
                                <label>Nova Senha</label>
                            </div>
                            
                            <div class="form-group floating-label">
                                <input type="password" name="confirmar_nova_senha" placeholder=" " required>
                                <label>Confirmar Nova Senha</label>
                            </div>
                            
                            <button type="submit" name="alterar_senha" class="btn btn-primary">
                                Alterar Senha
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Tab Meus Pedidos -->
                <div class="tab-content" id="pedidos-tab">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fa-solid fa-box-open"></i></div>
                        <h3 class="feature-title">Histórico de Pedidos</h3>
                        
                        <?php
                        // Obter pedidos do usuário
                        $pedidos = obterPedidosUsuario($_SESSION['usuario_id']);
                        if (empty($pedidos)):
                        ?>
                            <div class="empty-state">
                                <div class="empty-icon"><i class="fa-solid fa-shop-slash"></i></div>
                                <h4 class="empty-title">Nenhum pedido encontrado</h4>
                                <p class="empty-description">Você ainda não fez nenhum pedido. Que tal dar uma olhada em nossos produtos?</p>
                                <a href="produtos.php" class="btn btn-primary">Ver Produtos</a>
                            </div>
                        <?php else: ?>
                            <div class="orders-list" style="margin-top: 2rem;">
                                <?php foreach ($pedidos as $pedido): ?>
                                    <div class="order-card" style="background: var(--glass-bg); padding: 1.5rem; border-radius: var(--border-radius-md); margin-bottom: 1rem; border: 1px solid var(--glass-border);">
                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                                            <div>
                                                <strong>Pedido #<?php echo $pedido['id']; ?></strong>
                                                <div style="color: var(--text-muted); font-size: 0.9rem;">
                                                    <?php echo date('d/m/Y H:i', strtotime($pedido['data_pedido'])); ?>
                                                </div>
                                            </div>
                                            <div style="text-align: right;">
                                                <div class="badge badge-primary"><?php echo ucfirst($pedido['status']); ?></div>
                                                <div style="font-weight: 600; color: var(--primary-color); margin-top: 0.5rem;">
                                                    R$ <?php echo number_format($pedido['total'], 2, ',', '.'); ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div style="color: var(--text-muted); font-size: 0.9rem;">
                                            <strong>Entrega:</strong> <?php echo htmlspecialchars($pedido['endereco_entrega']); ?>, <?php echo htmlspecialchars($pedido['cidade_entrega']); ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Estatísticas do usuário -->
            <div class="stats-container" style="margin-top: 3rem;">
                <div class="stat-card">
                    <span class="stat-number"><?php echo date('d/m/Y', strtotime($usuario['data_registro'])); ?></span>
                    <span class="stat-label">Membro desde</span>
                </div>
                
                <div class="stat-card">
                    <span class="stat-number"><?php echo count($pedidos ?? []); ?></span>
                    <span class="stat-label">Pedidos realizados</span>
                </div>
                
                <div class="stat-card">
                    <span class="stat-number"><?php echo $usuario['ultimo_login'] ? date('d/m/Y', strtotime($usuario['ultimo_login'])) : 'Nunca'; ?></span>
                    <span class="stat-label">Último acesso</span>
                </div>
                
                <div class="stat-card">
                    <span class="stat-number"><?php echo ucfirst($usuario['tipo_usuario']); ?></span>
                    <span class="stat-label">Tipo de conta</span>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2024 E-commerce Project. Todos os direitos reservados.</p>
            <p>Desenvolvido por <a href="https://dexo-mu.vercel.app/" class="dexo-credit">Dexo</a></p>
        </div>
    </footer>

    <script>
        function switchTab(tabName) {
            // Remover classe active de todos os botões e conteúdos
            document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
            
            // Adicionar classe active ao botão clicado
            event.target.classList.add('active');
            
            // Mostrar conteúdo correspondente
            document.getElementById(tabName + '-tab').classList.add('active');
        }
        
        // Máscara para CEP
        document.querySelector('input[name="cep"]').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length <= 8) {
                value = value.replace(/(\d{5})(\d)/, '$1-$2');
                e.target.value = value;
            }
        });
        
        // Máscara para telefone
        document.querySelector('input[name="telefone"]').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length <= 11) {
                if (value.length <= 10) {
                    value = value.replace(/(\d{2})(\d{4})(\d)/, '($1) $2-$3');
                } else {
                    value = value.replace(/(\d{2})(\d{5})(\d)/, '($1) $2-$3');
                }
                e.target.value = value;
            }
        });
    </script>
    
    <!-- Animações e Interatividade -->
    <script src="animations.js"></script>
</body>
</html>

