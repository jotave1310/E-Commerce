<?php
session_start();
require_once 'config.php';

// Inicializar carrinho se não existir
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-commerce Project - Loja Online</title>
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
                        <?php if (isset($_SESSION['usuario_id'])): ?>
                            <li><a href="perfil.php">Olá, <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?>!</a></li>
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
                    🛒 Carrinho (<?php echo count($_SESSION['cart']); ?>)
                </a>
            </div>
        </div>
    </header>

    <main>
        <div class="container">
            <section class="hero">
                <h1>Tecnologia de Ponta ao Seu Alcance</h1>
                <p>Descubra os melhores produtos tecnológicos com preços imbatíveis!</p>
            </section>

            <section class="featured-products">
                <h2>Produtos em Destaque</h2>
                <div class="products-grid">
                <?php
                // Obter produtos em destaque do banco de dados
                $produtosDestaque = obterProdutosDestaque(4);
                
                foreach ($produtosDestaque as $produto): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <div class="placeholder-imagem">Imagem do Produto</div>
                        </div>
                        <div class="product-category"><?php echo htmlspecialchars($produto['categoria']); ?></div>
                        <h3><?php echo htmlspecialchars($produto['nome']); ?></h3>
                        <p class="product-price"><?php echo formatarPreco($produto['preco']); ?></p>
                        <p class="product-description"><?php echo htmlspecialchars($produto['descricao']); ?></p>
                        <a href="produto.php?id=<?php echo $produto['id']; ?>" class="btn btn-primary">Ver Detalhes</a>
                    </div>
                <?php endforeach; ?>
                </div>
            </section>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 E-commerce Project. Todos os direitos reservados. | Dexo</p>
        </div>
    </footer>

    <!-- Chatbot -->
    <div class="chatbot-container">
        <button class="chatbot-toggle" onclick="toggleChatbot()">💬</button>
        <div class="chatbot-window" id="chatbotWindow">
            <div class="chatbot-header">
                <h3>🤖 Assistente Virtual</h3>
                <button class="chatbot-close" onclick="toggleChatbot()">×</button>
            </div>
            <div class="chatbot-messages" id="chatbotMessages">
                <div class="message bot">
                    Olá! 👋 Sou seu assistente virtual. Como posso ajudá-lo hoje?
                    <div class="message-time" id="initialTime"></div>
                </div>
            </div>
            <div class="chatbot-input">
                <input type="text" id="chatbotInput" placeholder="Digite sua mensagem..." onkeypress="handleKeyPress(event)">
                <button class="chatbot-send" onclick="sendMessage()">➤</button>
            </div>
        </div>
    </div>

    <script>
        // Chatbot functionality
        function toggleChatbot() {
            const window = document.getElementById('chatbotWindow');
            window.classList.toggle('active');
            
            if (window.classList.contains('active')) {
                document.getElementById('chatbotInput').focus();
                // Set initial time
                document.getElementById('initialTime').textContent = new Date().toLocaleTimeString('pt-BR', {hour: '2-digit', minute: '2-digit'});
            }
        }

        function handleKeyPress(event) {
            if (event.key === 'Enter') {
                sendMessage();
            }
        }

        async function sendMessage() {
            const input = document.getElementById('chatbotInput');
            const messages = document.getElementById('chatbotMessages');
            const message = input.value.trim();
            
            if (!message) return;
            
            // Add user message
            const userMessage = document.createElement('div');
            userMessage.className = 'message user';
            userMessage.innerHTML = `
                ${message}
                <div class="message-time">${new Date().toLocaleTimeString('pt-BR', {hour: '2-digit', minute: '2-digit'})}</div>
            `;
            messages.appendChild(userMessage);
            
            // Clear input
            input.value = '';
            
            // Show typing indicator
            const typingIndicator = document.createElement('div');
            typingIndicator.className = 'typing-indicator';
            typingIndicator.innerHTML = `
                Digitando...
                <div class="typing-dots">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            `;
            messages.appendChild(typingIndicator);
            
            // Scroll to bottom
            messages.scrollTop = messages.scrollHeight;
            
            try {
                // Send message to chatbot
                const response = await fetch('chatbot.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ mensagem: message })
                });
                
                const data = await response.json();
                
                // Remove typing indicator
                messages.removeChild(typingIndicator);
                
                // Add bot response
                const botMessage = document.createElement('div');
                botMessage.className = 'message bot';
                botMessage.innerHTML = `
                    ${data.resposta}
                    <div class="message-time">${data.timestamp}</div>
                `;
                messages.appendChild(botMessage);
                
            } catch (error) {
                // Remove typing indicator
                messages.removeChild(typingIndicator);
                
                // Add error message
                const errorMessage = document.createElement('div');
                errorMessage.className = 'message bot';
                errorMessage.innerHTML = `
                    Desculpe, ocorreu um erro. Tente novamente! 😅
                    <div class="message-time">${new Date().toLocaleTimeString('pt-BR', {hour: '2-digit', minute: '2-digit'})}</div>
                `;
                messages.appendChild(errorMessage);
            }
            
            // Scroll to bottom
            messages.scrollTop = messages.scrollHeight;
        }
    </script>
    
    <!-- Animações e Interatividade -->
    <script src="animations.js"></script>
</body>
</html>

