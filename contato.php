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
    $telefone = trim($_POST['telefone'] ?? '');
    $assunto = trim($_POST['assunto'] ?? '');
    $mensagem = trim($_POST['mensagem'] ?? '');
    
    $erros = [];
    
    if (empty($nome)) $erros[] = "Nome é obrigatório";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $erros[] = "Email válido é obrigatório";
    if (empty($assunto)) $erros[] = "Assunto é obrigatório";
    if (empty($mensagem)) $erros[] = "Mensagem é obrigatória";
    
    if (empty($erros)) {
        // Simular envio de email (para demonstração)
        $sucesso = "Mensagem enviada com sucesso! Entraremos em contato em breve.";
        
        // Salvar mensagem em arquivo para demonstração
        $dados_mensagem = [
            'data' => date('Y-m-d H:i:s'),
            'nome' => $nome,
            'email' => $email,
            'telefone' => $telefone,
            'assunto' => $assunto,
            'mensagem' => $mensagem
        ];
        
        $arquivo_mensagens = 'mensagens_contato.json';
        $mensagens_existentes = [];
        
        if (file_exists($arquivo_mensagens)) {
            $mensagens_existentes = json_decode(file_get_contents($arquivo_mensagens), true) ?: [];
        }
        
        $mensagens_existentes[] = $dados_mensagem;
        file_put_contents($arquivo_mensagens, json_encode($mensagens_existentes, JSON_PRETTY_PRINT));
        
        // Limpar campos após envio
        $nome = $email = $telefone = $assunto = $mensagem = '';
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
    <link rel="stylesheet" href="components.css">
    <?php include 'header.php'; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Montserrat:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        .contact-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: var(--spacing-xl) var(--spacing-lg);
        }
        
        .page-header {
            text-align: center;
            margin-bottom: var(--spacing-3xl);
            padding: var(--spacing-3xl) 0;
            background: linear-gradient(135deg, rgba(233, 69, 96, 0.1) 0%, rgba(15, 52, 96, 0.1) 100%);
            border-radius: var(--border-radius-xl);
            position: relative;
            overflow: hidden;
        }
        
        .page-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 80%, rgba(233, 69, 96, 0.05) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(15, 52, 96, 0.05) 0%, transparent 50%);
            pointer-events: none;
        }
        
        .page-title {
            font-family: 'Montserrat', sans-serif;
            font-size: clamp(2rem, 5vw, 3rem);
            font-weight: 800;
            margin-bottom: var(--spacing-lg);
            background: linear-gradient(135deg, var(--text-light), var(--text-muted));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            position: relative;
            z-index: 1;
        }
        
        .page-subtitle {
            font-size: 1.2rem;
            color: var(--text-muted);
            max-width: 600px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }
        
        .contact-content {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: var(--spacing-3xl);
            margin-bottom: var(--spacing-3xl);
        }
        
        .contact-form-section {
            background: var(--glass-bg);
            backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid var(--glass-border);
            border-radius: var(--border-radius-xl);
            padding: var(--spacing-3xl);
            transition: all var(--transition-normal);
            position: relative;
            overflow: hidden;
        }
        
        .contact-form-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.03), transparent);
            transition: left var(--transition-slow);
            pointer-events: none;
        }
        
        .contact-form-section:hover::before {
            left: 100%;
        }
        
        .contact-form-section:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-xl);
            border-color: rgba(255, 255, 255, 0.2);
        }
        
        .form-title {
            font-family: 'Montserrat', sans-serif;
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--text-light);
            margin-bottom: var(--spacing-xl);
            display: flex;
            align-items: center;
            gap: var(--spacing-md);
        }
        
        .form-title::after {
            content: '';
            flex: 1;
            height: 2px;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            border-radius: var(--border-radius-full);
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: var(--spacing-lg);
            margin-bottom: var(--spacing-lg);
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
            gap: var(--spacing-sm);
        }
        
        .form-group.full-width {
            grid-column: 1 / -1;
        }
        
        .form-label {
            color: var(--text-light);
            font-weight: 600;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: var(--spacing-xs);
        }
        
        .form-label .required {
            color: var(--primary-color);
        }
        
        .form-select option{
            color: black;
        }

        .form-input, .form-textarea, .form-select {
            padding: var(--spacing-md);
            background: rgba(255, 255, 255, 0.08);
            border: 2px solid var(--glass-border);
            border-radius: var(--border-radius-md);
            color: var(--text-light);
            font-size: 1rem;
            transition: all var(--transition-normal);
            backdrop-filter: blur(10px);
            font-family: inherit;
        }
        
        .form-input:focus, .form-textarea:focus, .form-select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(233, 69, 96, 0.2);
            background: rgba(255, 255, 255, 0.12);
            transform: translateY(-2px);
        }
        
        .form-input::placeholder, .form-textarea::placeholder {
            color: var(--text-dark);
        }
        
        .form-textarea {
            resize: vertical;
            min-height: 120px;
        }
        
        .btn-submit {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: white;
            border: none;
            padding: var(--spacing-lg) var(--spacing-3xl);
            border-radius: var(--border-radius-md);
            font-weight: 600;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all var(--transition-normal);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: var(--spacing-sm);
            width: 100%;
            margin-top: var(--spacing-lg);
        }
        
        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-lg);
        }
        
        .btn-submit:active {
            transform: translateY(-1px);
        }
        
        .contact-info-section {
            display: flex;
            flex-direction: column;
            gap: var(--spacing-xl);
        }
        
        .contact-info-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid var(--glass-border);
            border-radius: var(--border-radius-xl);
            padding: var(--spacing-xl);
            transition: all var(--transition-normal);
        }
        
        .contact-info-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
            border-color: rgba(255, 255, 255, 0.2);
        }
        
        .info-title {
            font-family: 'Montserrat', sans-serif;
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--text-light);
            margin-bottom: var(--spacing-lg);
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
        }
        
        .contact-item {
            display: flex;
            align-items: flex-start;
            gap: var(--spacing-md);
            margin-bottom: var(--spacing-lg);
            padding: var(--spacing-md);
            background: rgba(255, 255, 255, 0.05);
            border-radius: var(--border-radius-md);
            transition: all var(--transition-normal);
        }
        
        .contact-item:hover {
            background: rgba(255, 255, 255, 0.08);
            transform: translateX(4px);
        }
        
        .contact-icon {
            font-size: 1.5rem;
            flex-shrink: 0;
            margin-top: 2px;
        }
        
        .contact-details {
            flex: 1;
        }
        
        .contact-label {
            font-weight: 600;
            color: var(--text-light);
            margin-bottom: var(--spacing-xs);
        }
        
        .contact-value {
            color: var(--text-muted);
            font-size: 0.95rem;
            line-height: 1.4;
        }
        
        .hours-card {
            background: linear-gradient(135deg, rgba(233, 69, 96, 0.1), rgba(15, 52, 96, 0.1));
            border: 1px solid var(--glass-border);
            border-radius: var(--border-radius-lg);
            padding: var(--spacing-xl);
        }
        
        .hours-title {
            font-family: 'Montserrat', sans-serif;
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--text-light);
            margin-bottom: var(--spacing-lg);
            text-align: center;
        }
        
        .hours-list {
            display: flex;
            flex-direction: column;
            gap: var(--spacing-sm);
        }
        
        .hours-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: var(--spacing-sm) 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .hours-item:last-child {
            border-bottom: none;
        }
        
        .hours-day {
            font-weight: 500;
            color: var(--text-light);
        }
        
        .hours-time {
            color: var(--text-muted);
            font-size: 0.9rem;
        }
        
        .alert {
            padding: var(--spacing-lg);
            border-radius: var(--border-radius-md);
            margin-bottom: var(--spacing-lg);
            border: 1px solid;
            backdrop-filter: blur(10px);
        }
        
        .alert-success {
            background: rgba(40, 167, 69, 0.1);
            border-color: rgba(40, 167, 69, 0.3);
            color: #28a745;
        }
        
        .alert-error {
            background: rgba(220, 53, 69, 0.1);
            border-color: rgba(220, 53, 69, 0.3);
            color: #dc3545;
        }
        
        .social-links {
            display: flex;
            gap: var(--spacing-md);
            justify-content: center;
            margin-top: var(--spacing-lg);
        }
        
        .social-link {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 50px;
            height: 50px;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 50%;
            color: var(--text-light);
            text-decoration: none;
            font-size: 1.2rem;
            transition: all var(--transition-normal);
        }

        .social-link[title="Facebook"]:hover {
            background-color: #1877F2;
            color: white;
        }
        
        .social-link[title="Instagram"]:hover {
            background: radial-gradient(circle at 30% 107%, #fdf497 0%, #fdf497 5%, #fd5949 45%, #d6249f 60%, #285AEB 90%);
            color: white;
        }

        .social-link[title="Twitter"]:hover {
            background-color: #000000; /* Preto para o X */
            color: white;
        }

        .social-link[title="LinkedIn"]:hover {
            background-color: #0A66C2;
            color: white;
        }

        .social-link:hover {
            background: var(--primary-color);
            border-color: var(--primary-color);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }
        
        .map-section {
            background: var(--glass-bg);
            backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid var(--glass-border);
            border-radius: var(--border-radius-xl);
            padding: var(--spacing-xl);
            text-align: center;
        }
        
        .map-placeholder {
            width: 100%;
            height: 300px;
            background: linear-gradient(135deg, var(--card-bg), var(--dark-bg));
            border-radius: var(--border-radius-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
            font-size: 1.1rem;
            margin-bottom: var(--spacing-lg);
        }
        
        @media (max-width: 768px) {
            .contact-content {
                grid-template-columns: 1fr;
                gap: var(--spacing-xl);
            }
            
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .contact-info-section {
                order: -1;
            }
        }
    </style>
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
                            <li><a href="login.php" class="btn-login">Login</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <a href="carrinho.php" class="cart-icon">
                    <i class="fa-solid fa-cart-shopping"></i> Carrinho (<?php echo array_sum($_SESSION['cart']); ?>)
                </a>
            </div>
        </div>
    </header>

    <main class="contact-container">
        <div class="page-header">
            <h1 class="page-title">Entre em Contato</h1>
            <p class="page-subtitle">Estamos aqui para ajudar! Entre em contato conosco e responderemos o mais breve possível</p>
        </div>

        <?php if (!empty($erros)): ?>
            <div class="alert alert-error">
                <strong>❌ Erro(s) encontrado(s):</strong>
                <ul style="margin: 0.5rem 0 0 1rem;">
                    <?php foreach ($erros as $erro): ?>
                        <li><?php echo htmlspecialchars($erro); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (isset($sucesso)): ?>
            <div class="alert alert-success">
                <strong>✅ <?php echo htmlspecialchars($sucesso); ?></strong>
            </div>
        <?php endif; ?>

        <div class="contact-content">
            <div class="contact-form-section">
                <h2 class="form-title">
                    <i class="fa-solid fa-envelope"></i> Envie sua Mensagem
                </h2>
                
                <form method="POST" id="contactForm">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="nome" class="form-label">
                                <i class="fa-solid fa-user"></i> Nome <span class="required">*</span>
                            </label>
                            <input type="text" id="nome" name="nome" class="form-input" 
                                   placeholder="Seu nome completo" required
                                   value="<?php echo htmlspecialchars($nome ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="email" class="form-label">
                                <i class="fa-solid fa-envelope"></i> Email <span class="required">*</span>
                            </label>
                            <input type="email" id="email" name="email" class="form-input" 
                                   placeholder="seu@email.com" required
                                   value="<?php echo htmlspecialchars($email ?? ''); ?>">
                        </div>
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="telefone" class="form-label">
                                <i class="fa-solid fa-phone"></i> Telefone
                            </label>
                            <input type="tel" id="telefone" name="telefone" class="form-input" 
                                   placeholder="(11) 99999-9999"
                                   value="<?php echo htmlspecialchars($telefone ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="assunto" class="form-label">
                                <i class="fa-solid fa-clipboard-list"></i> Assunto <span class="required">*</span>
                            </label>
                            <select id="assunto" name="assunto" class="form-select"  required>
                                <option value="">Selecione um assunto</option>
                                <option value="Dúvida sobre produto" <?php echo (isset($assunto) && $assunto === 'Dúvida sobre produto') ? 'selected' : ''; ?>>Dúvida sobre produto</option>
                                <option value="Suporte técnico" <?php echo (isset($assunto) && $assunto === 'Suporte técnico') ? 'selected' : ''; ?>>Suporte técnico</option>
                                <option value="Pedido/Entrega" <?php echo (isset($assunto) && $assunto === 'Pedido/Entrega') ? 'selected' : ''; ?>>Pedido/Entrega</option>
                                <option value="Reclamação" <?php echo (isset($assunto) && $assunto === 'Reclamação') ? 'selected' : ''; ?>>Reclamação</option>
                                <option value="Sugestão" <?php echo (isset($assunto) && $assunto === 'Sugestão') ? 'selected' : ''; ?>>Sugestão</option>
                                <option value="Outros" <?php echo (isset($assunto) && $assunto === 'Outros') ? 'selected' : ''; ?>>Outros</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group full-width">
                        <label for="mensagem" class="form-label">
                            <i class="fa-solid fa-message"></i> Mensagem <span class="required">*</span>
                        </label>
                        <textarea id="mensagem" name="mensagem" class="form-textarea" 
                                  placeholder="Descreva sua dúvida, sugestão ou comentário..." required><?php echo htmlspecialchars($mensagem ?? ''); ?></textarea>
                    </div>

                    <button type="submit" name="enviar_mensagem" class="btn-submit">
                        <span><i class="fa-solid fa-envelope"></i></span>
                        Enviar Mensagem
                    </button>
                </form>
            </div>

            <div class="contact-info-section">
                <div class="contact-info-card">
                    <h3 class="info-title">
                        <i class="fa-solid fa-phone"></i> Informações de Contato
                    </h3>
                    
                    <div class="contact-item">
                        <span class="contact-icon"><i class="fa-solid fa-envelope"></i></span>
                        <div class="contact-details">
                            <div class="contact-label">Email</div>
                            <div class="contact-value">contato@ecommerceproject.com<br>suporte@ecommerceproject.com</div>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <span class="contact-icon"><i class="fa-solid fa-phone"></i></span>
                        <div class="contact-details">
                            <div class="contact-label">Telefone</div>
                            <div class="contact-value">(11) 9999-9999<br>WhatsApp: (11) 8888-8888</div>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <span class="contact-icon"><i class="fa-solid fa-location-dot"></i></span>
                        <div class="contact-details">
                            <div class="contact-label">Endereço</div>
                            <div class="contact-value">Av. Paulista, 1000<br>São Paulo, SP - 01310-100<br>Brasil</div>
                        </div>
                    </div>
                    
                    <div class="social-links">
                        <a href="#" class="social-link" title="Facebook" target="_blank"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-link" title="Instagram" target="_blank"><i class="fa-brands fa-instagram"></i></a>
                        <a href="#" class="social-link" title="Twitter" target="_blank"><i class="fa-brands fa-x-twitter"></i></a>
                        <a href="#" class="social-link" title="LinkedIn" target="_blank"><i class="fa-brands fa-linkedin"></i></a>
                    </div>
                </div>

                <div class="contact-info-card">
                    <div class="hours-card">
                        <h3 class="hours-title"><i class="fa-solid fa-clock"></i> Horário de Atendimento</h3>
                        <div class="hours-list">
                            <div class="hours-item">
                                <span class="hours-day">Segunda a Sexta</span>
                                <span class="hours-time">9h às 18h</span>
                            </div>
                            <div class="hours-item">
                                <span class="hours-day">Sábado</span>
                                <span class="hours-time">9h às 14h</span>
                            </div>
                            <div class="hours-item">
                                <span class="hours-day">Domingo</span>
                                <span class="hours-time">Fechado</span>
                            </div>
                            <div class="hours-item">
                                <span class="hours-day">Feriados</span>
                                <span class="hours-time">Fechado</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="map-section">
            <h3 class="info-title"><i class="fa-solid fa-map"></i> Nossa Localização</h3>
            <div class="map-placeholder">
                <i class="fa-solid fa-location-dot"></i> Mapa interativo em breve
            </div>
            <p style="color: var(--text-muted); text-align: center;">
                Estamos localizados no coração de São Paulo, com fácil acesso por transporte público e estacionamento disponível.
            </p>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 E-commerce Project. Todos os direitos reservados.</p>
            <p>Desenvolvido por <a href="https://dexo-mu.vercel.app/" class="dexo-credit">Dexo</a></p>
        </div>
    </footer>

    <!-- Chatbot -->
    <div class="chatbot-container">
        <button class="chatbot-toggle"><i class="fa-solid fa-comment-nodes"></i></button>
        <div class="chatbot-window">
            <div class="chatbot-header">
                <h4><i class="fa-solid fa-robot"></i> Assistente Virtual</h4>
                <button class="chatbot-close">×</button>
            </div>
            <div class="chatbot-messages">
                <div style="color: #ffffff; margin-bottom: 1rem;">
                    Olá! Posso ajudá-lo com informações de contato ou dúvidas. Como posso ajudar?
                </div>
            </div>
            <div class="chatbot-input-container">
                <input type="text" class="chatbot-input" placeholder="Digite sua mensagem...">
                <button class="chatbot-send">➤</button>
            </div>
        </div>
    </div>

    <script>
        // Validação do formulário em tempo real
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('contactForm');
            const inputs = form.querySelectorAll('input, select, textarea');
            
            inputs.forEach(input => {
                input.addEventListener('blur', validateField);
                input.addEventListener('input', clearErrors);
            });
            
            function validateField(e) {
                const field = e.target;
                const value = field.value.trim();
                
                // Remover erros anteriores
                clearFieldError(field);
                
                // Validações específicas
                if (field.hasAttribute('required') && !value) {
                    showFieldError(field, 'Este campo é obrigatório');
                    return;
                }
                
                if (field.type === 'email' && value && !isValidEmail(value)) {
                    showFieldError(field, 'Email inválido');
                    return;
                }
                
                if (field.name === 'telefone' && value && !isValidPhone(value)) {
                    showFieldError(field, 'Telefone inválido');
                    return;
                }
                
                // Campo válido
                showFieldSuccess(field);
            }
            
            function clearErrors(e) {
                clearFieldError(e.target);
            }
            
            function showFieldError(field, message) {
                field.style.borderColor = '#dc3545';
                field.style.background = 'rgba(220, 53, 69, 0.1)';
                
                let errorDiv = field.parentNode.querySelector('.field-error');
                if (!errorDiv) {
                    errorDiv = document.createElement('div');
                    errorDiv.className = 'field-error';
                    errorDiv.style.color = '#dc3545';
                    errorDiv.style.fontSize = '0.85rem';
                    errorDiv.style.marginTop = '0.25rem';
                    field.parentNode.appendChild(errorDiv);
                }
                errorDiv.textContent = message;
            }
            
            function showFieldSuccess(field) {
                field.style.borderColor = '#28a745';
                field.style.background = 'rgba(40, 167, 69, 0.1)';
            }
            
            function clearFieldError(field) {
                field.style.borderColor = '';
                field.style.background = '';
                
                const errorDiv = field.parentNode.querySelector('.field-error');
                if (errorDiv) {
                    errorDiv.remove();
                }
            }
            
            function isValidEmail(email) {
                return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
            }
            
            function isValidPhone(phone) {
                return /^[\(\)\d\s\-\+]{10,}$/.test(phone);
            }
            
            // Animação de envio do formulário
            form.addEventListener('submit', function(e) {
                const submitBtn = form.querySelector('.btn-submit');
                submitBtn.innerHTML = '<span>⏳</span> Enviando...';
                submitBtn.disabled = true;
            });
        });
        
        // Animação de entrada dos elementos
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.animation = 'fadeInUp 0.6s ease-out forwards';
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });
        
        document.querySelectorAll('.contact-form-section, .contact-info-card, .map-section').forEach((el, index) => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(30px)';
            el.style.animationDelay = `${index * 0.2}s`;
            observer.observe(el);
        });
    </script>
    
    <script src="animations.js"></script>
</body>
</html>

