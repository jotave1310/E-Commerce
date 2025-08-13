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
    <title>Produtos - E-commerce Project</title>
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
            <h1>Catálogo de Produtos</h1>

            <div class="products-grid">
                <?php
                // Obter todos os produtos do banco de dados
                $todosProdutos = obterTodosProdutos();
                
                foreach ($todosProdutos as $produto): ?>
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
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 E-commerce Project. Todos os direitos reservados. | Dexo</p>
        </div>
    </footer>
</body>
</html>

