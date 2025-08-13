<?php
session_start();
require_once 'config.php';

// Inicializar carrinho se não existir
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

// Verificar se o ID do produto foi fornecido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$produtoId = (int)$_GET['id'];
$produto = obterProduto($produtoId);

// Se o produto não existir, redirecionar para a página inicial
if (!$produto) {
    header('Location: index.php');
    exit;
}

// Processar adição ao carrinho
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['adicionar_carrinho'])) {
    $quantidade = isset($_POST['quantidade']) ? (int)$_POST['quantidade'] : 1;
    
    if ($quantidade > 0) {
        if (isset($_SESSION['cart'][$produtoId])) {
            $_SESSION['cart'][$produtoId] += $quantidade;
        } else {
            $_SESSION['cart'][$produtoId] = $quantidade;
        }
        
        $mensagem = "Produto adicionado ao carrinho com sucesso!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($produto['nome']); ?> - E-commerce Project</title>
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
            <?php if (isset($mensagem)): ?>
                <div style="background-color: #d4edda; color: #155724; padding: 1rem; border-radius: 5px; margin-bottom: 2rem;">
                    <?php echo htmlspecialchars($mensagem); ?>
                </div>
            <?php endif; ?>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; margin-top: 2rem;">
                <div>
                    <div class="product-image" style="height: 400px; font-size: 1.2rem;">
                        Imagem do Produto<br>
                        <?php echo htmlspecialchars($produto['nome']); ?>
                    </div>
                </div>
                
                <div style="background-color: white; padding: 2rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    <h1 style="color: #2c3e50; margin-bottom: 1rem;"><?php echo htmlspecialchars($produto['nome']); ?></h1>
                    
                    <div style="background-color: #ecf0f1; padding: 0.5rem; border-radius: 5px; margin-bottom: 1rem;">
                        <strong>Categoria:</strong> <?php echo htmlspecialchars($produto['categoria']); ?>
                    </div>
                    
                    <div class="product-price" style="font-size: 2rem; margin-bottom: 1rem;">
                        <?php echo formatarPreco($produto['preco']); ?>
                    </div>
                    
                    <p style="margin-bottom: 2rem; line-height: 1.6;">
                        <?php echo htmlspecialchars($produto['descricao']); ?>
                    </p>
                    
                    <form method="POST" style="margin-bottom: 2rem;">
                        <div style="margin-bottom: 1rem;">
                            <label for="quantidade" style="display: block; margin-bottom: 0.5rem; font-weight: bold;">Quantidade:</label>
                            <input type="number" id="quantidade" name="quantidade" value="1" min="1" max="10" 
                                   style="padding: 0.5rem; border: 1px solid #ddd; border-radius: 5px; width: 80px;">
                        </div>
                        
                        <button type="submit" name="adicionar_carrinho" class="btn btn-success" style="width: 100%; padding: 1rem; font-size: 1.1rem;">
                            🛒 Adicionar ao Carrinho
                        </button>
                    </form>
                    
                    <a href="index.php" class="btn" style="width: 100%; text-align: center; display: block; padding: 1rem;">
                        ← Voltar para a Loja
                    </a>
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

