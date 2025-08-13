<?php
session_start();

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
    <title>Sobre - E-commerce Project</title>
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
            <div style="background-color: white; padding: 3rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                <h1 style="color: #2c3e50; margin-bottom: 2rem;">Sobre o E-commerce Project</h1>
                
                <div style="line-height: 1.8; font-size: 1.1rem;">
                    <p style="margin-bottom: 1.5rem;">
                        O <strong>E-commerce Project</strong> é uma demonstração de loja online desenvolvida como projeto acadêmico, 
                        utilizando as tecnologias PHP, HTML e CSS para criar uma experiência de compra completa e funcional.
                    </p>
                    
                    <h2 style="color: #2c3e50; margin: 2rem 0 1rem 0;">Nossa Missão</h2>
                    <p style="margin-bottom: 1.5rem;">
                        Demonstrar as melhores práticas de desenvolvimento web através de um e-commerce funcional, 
                        oferecendo uma interface intuitiva e uma experiência de usuário agradável.
                    </p>
                    
                    <h2 style="color: #2c3e50; margin: 2rem 0 1rem 0;">Tecnologias Utilizadas</h2>
                    <ul style="margin-bottom: 1.5rem; margin-left: 2rem;">
                        <li><strong>PHP:</strong> Linguagem de programação server-side para lógica de negócio</li>
                        <li><strong>HTML5:</strong> Estruturação semântica das páginas</li>
                        <li><strong>CSS3:</strong> Estilização e design responsivo</li>
                        <li><strong>Sessões PHP:</strong> Gerenciamento do carrinho de compras</li>
                        <li><strong>Git/GitHub:</strong> Controle de versão e colaboração</li>
                    </ul>
                    
                    <h2 style="color: #2c3e50; margin: 2rem 0 1rem 0;">Funcionalidades</h2>
                    <ul style="margin-bottom: 1.5rem; margin-left: 2rem;">
                        <li>Catálogo de produtos com detalhes</li>
                        <li>Carrinho de compras funcional</li>
                        <li>Sistema de checkout simplificado</li>
                        <li>Design responsivo para dispositivos móveis</li>
                        <li>Interface intuitiva e moderna</li>
                    </ul>
                    
                    <h2 style="color: #2c3e50; margin: 2rem 0 1rem 0;">Desenvolvimento</h2>
                    <p style="margin-bottom: 1.5rem;">
                        Este projeto foi desenvolvido seguindo as melhores práticas de programação, 
                        incluindo código limpo, estrutura organizada e documentação completa. 
                        O versionamento é feito através do Git, garantindo rastreabilidade de todas as mudanças.
                    </p>
                    
                    <div style="background-color: #ecf0f1; padding: 1.5rem; border-radius: 5px; margin-top: 2rem;">
                        <p style="margin: 0; text-align: center; font-style: italic;">
                            "Um projeto desenvolvido com dedicação para demonstrar conhecimentos em desenvolvimento web."
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

