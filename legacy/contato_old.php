<?php
session_start();

// Inicializar carrinho se não existir
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

// Processar envio do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enviar_mensagem'])) {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $assunto = trim($_POST['assunto'] ?? '');
    $mensagem = trim($_POST['mensagem'] ?? '');
    
    $erros = [];
    
    if (empty($nome)) $erros[] = "Nome é obrigatório";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $erros[] = "Email válido é obrigatório";
    if (empty($assunto)) $erros[] = "Assunto é obrigatório";
    if (empty($mensagem)) $erros[] = "Mensagem é obrigatória";
    
    if (empty($erros)) {
        $sucesso = "Mensagem enviada com sucesso! Entraremos em contato em breve.";
        // Limpar campos após envio
        $nome = $email = $assunto = $mensagem = '';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contato - E-commerce Project</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">E-commerce Project</div>
                <nav>
                    <ul>
                        <li><a href="index.php">Início</a></li>
                        <li><a href="produtos.php">Produtos</a></li>
                        <li><a href="sobre.php">Sobre</a></li>
                        <li><a href="contato.php">Contato</a></li>
                    </ul>
                </nav>
                <a href="carrinho.php" class="cart-icon">
                    🛒 Carrinho (<?php echo count($_SESSION['cart']); ?>)
                </a>
            </div>
        </div>
    </header>

    <main>
        <div class="container">
            <h1 style="color: #2c3e50; margin-bottom: 2rem;">Entre em Contato</h1>

            <?php if (!empty($erros)): ?>
                <div style="background-color: #f8d7da; color: #721c24; padding: 1rem; border-radius: 5px; margin-bottom: 2rem;">
                    <strong>Erro(s) encontrado(s):</strong>
                    <ul style="margin: 0.5rem 0 0 1rem;">
                        <?php foreach ($erros as $erro): ?>
                            <li><?php echo htmlspecialchars($erro); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if (isset($sucesso)): ?>
                <div style="background-color: #d4edda; color: #155724; padding: 1rem; border-radius: 5px; margin-bottom: 2rem;">
                    <?php echo htmlspecialchars($sucesso); ?>
                </div>
            <?php endif; ?>

            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 3rem;">
                <!-- Formulário de contato -->
                <div style="background-color: white; padding: 2rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    <h2 style="margin-bottom: 1.5rem; color: #2c3e50;">Envie sua Mensagem</h2>
                    
                    <form method="POST">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                            <div>
                                <label for="nome" style="display: block; margin-bottom: 0.5rem; font-weight: bold;">Nome *</label>
                                <input type="text" id="nome" name="nome" required 
                                       value="<?php echo htmlspecialchars($nome ?? ''); ?>"
                                       style="width: 100%; padding: 0.7rem; border: 1px solid #ddd; border-radius: 5px;">
                            </div>
                            <div>
                                <label for="email" style="display: block; margin-bottom: 0.5rem; font-weight: bold;">Email *</label>
                                <input type="email" id="email" name="email" required 
                                       value="<?php echo htmlspecialchars($email ?? ''); ?>"
                                       style="width: 100%; padding: 0.7rem; border: 1px solid #ddd; border-radius: 5px;">
                            </div>
                        </div>

                        <div style="margin-bottom: 1rem;">
                            <label for="assunto" style="display: block; margin-bottom: 0.5rem; font-weight: bold;">Assunto *</label>
                            <input type="text" id="assunto" name="assunto" required 
                                   value="<?php echo htmlspecialchars($assunto ?? ''); ?>"
                                   style="width: 100%; padding: 0.7rem; border: 1px solid #ddd; border-radius: 5px;">
                        </div>

                        <div style="margin-bottom: 2rem;">
                            <label for="mensagem" style="display: block; margin-bottom: 0.5rem; font-weight: bold;">Mensagem *</label>
                            <textarea id="mensagem" name="mensagem" required rows="6"
                                      style="width: 100%; padding: 0.7rem; border: 1px solid #ddd; border-radius: 5px; resize: vertical;"><?php echo htmlspecialchars($mensagem ?? ''); ?></textarea>
                        </div>

                        <button type="submit" name="enviar_mensagem" class="btn btn-success" style="width: 100%; padding: 1rem; font-size: 1.1rem;">
                            📧 Enviar Mensagem
                        </button>
                    </form>
                </div>

                <!-- Informações de contato -->
                <div style="background-color: white; padding: 2rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); height: fit-content;">
                    <h2 style="margin-bottom: 1.5rem; color: #2c3e50;">Informações de Contato</h2>
                    
                    <div style="margin-bottom: 2rem;">
                        <div style="display: flex; align-items: center; margin-bottom: 1rem;">
                            <span style="font-size: 1.5rem; margin-right: 1rem;">📧</span>
                            <div>
                                <strong>Email:</strong><br>
                                contato@ecommerceproject.com
                            </div>
                        </div>
                        
                        <div style="display: flex; align-items: center; margin-bottom: 1rem;">
                            <span style="font-size: 1.5rem; margin-right: 1rem;">📞</span>
                            <div>
                                <strong>Telefone:</strong><br>
                                (11) 9999-9999
                            </div>
                        </div>
                        
                        <div style="display: flex; align-items: center; margin-bottom: 1rem;">
                            <span style="font-size: 1.5rem; margin-right: 1rem;">📍</span>
                            <div>
                                <strong>Endereço:</strong><br>
                                São Paulo, SP<br>
                                Brasil
                            </div>
                        </div>
                    </div>
                    
                    <div style="background-color: #ecf0f1; padding: 1.5rem; border-radius: 5px;">
                        <h3 style="margin-bottom: 1rem; color: #2c3e50;">Horário de Atendimento</h3>
                        <p style="margin: 0;">
                            <strong>Segunda a Sexta:</strong> 9h às 18h<br>
                            <strong>Sábado:</strong> 9h às 14h<br>
                            <strong>Domingo:</strong> Fechado
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 E-commerce Project. Todos os direitos reservados. | Dexo</p>
        </div>
    </footer>
</body>
</html>

