<?php
session_start();
require_once 'config.php';
require_once 'db_connect.php';

// Verificar se está logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

$usuarioId = $_SESSION['usuario_id'];
$carrinhoId = obterCarrinhoUsuario($usuarioId);

// Processar ações no carrinho
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['remover_item'])) {
        $produtoId = (int)$_POST['produto_id'];
        if (removerDoCarrinho($carrinhoId, $produtoId)) {
            $mensagem = "Item removido do carrinho!";
        } else {
            $erro = "Erro ao remover item do carrinho.";
        }
    } 
    elseif (isset($_POST['atualizar_quantidade'])) {
        $produtoId = (int)$_POST['produto_id'];
        $novaQuantidade = (int)$_POST['quantidade'];
        
        if (atualizarQuantidadeCarrinho($carrinhoId, $produtoId, $novaQuantidade)) {
            $mensagem = "Quantidade atualizada!";
        } else {
            $erro = "Erro ao atualizar quantidade.";
        }
    } 
    elseif (isset($_POST['limpar_carrinho'])) {
        if (limparCarrinho($carrinhoId)) {
            $mensagem = "Carrinho limpo!";
        } else {
            $erro = "Erro ao limpar carrinho.";
        }
    }
}

// Obter itens do carrinho e calcular total
$itensCarrinho = obterItensCarrinho($carrinhoId);
$total = calcularTotalCarrinho($carrinhoId);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrinho de Compras - E-commerce Project</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Montserrat:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<style>
    /* Carrinho de Compras */
.cart-table {
    background-color: white;
    border-radius: var(--border-radius-md);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
    margin-bottom: 2rem;
}

.cart-table table {
    width: 100%;
    border-collapse: collapse;
}

.cart-table th, .cart-table td {
    color: black;
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid white;
}

.cart-table th {
    background-color: black;
    font-weight: 600;
}

.cart-product-info {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.cart-product-image {
    width: 80px;
    height: 80px;
    object-fit: contain;
    border-radius: var(--border-radius-sm);
    background-color: black;
}

.cart-product-category {
    font-size: 0.85rem;
    color: var(--text-muted);
    margin-top: 0.25rem;
}

.cart-quantity-input {
    width: 60px;
    padding: 0.5rem;
    border: 1px solid black;
    border-radius: var(--border-radius-sm);
    text-align: center;
}

.cart-summary {
    background-color: black;
    border-radius: var(--border-radius-md);
    box-shadow: var(--shadow-sm);
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
}

.cart-total {
    font-size: 1.5rem;
    margin-bottom: 1.5rem;
    font-weight: 600;
    color: var(--primary-color);
}

.cart-actions {
    display: flex;
    gap: 1rem;
}

.btn-sm {
    padding: 0.5rem 1rem;
    font-size: 0.9rem;
}
</style>
<body>
    <?php include 'header.php'; ?>

    <main>
        <div class="container">
            <div class="breadcrumb">
                <a href="index.php" class="breadcrumb-item">Início</a>
                <span class="breadcrumb-separator">›</span>
                <span class="breadcrumb-item active">Carrinho</span>
            </div>

            <h1>Carrinho de Compras</h1>
            
            <?php if (isset($mensagem)): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($mensagem); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($erro)): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($erro); ?>
                </div>
            <?php endif; ?>
            
            <?php if (empty($itensCarrinho)): ?>
                <div class="empty-state">
                    <div class="empty-icon"><i class="fa-solid fa-cart-arrow-down"></i></div>
                    <h4 class="empty-title">Seu carrinho está vazio</h4>
                    <p class="empty-description">Adicione alguns produtos para continuar comprando!</p>
                    <a href="produtos.php" class="btn btn-primary">Continuar Comprando</a>
                </div>
            <?php else: ?>
                <div class="cart-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Produto</th>
                                <th>Preço Unitário</th>
                                <th>Quantidade</th>
                                <th>Subtotal</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($itensCarrinho as $item): ?>
                                <tr>
                                    <td>
                                        <div class="cart-product-info">
                                            <?php if (!empty($item['imagem_url'])): ?>
                                                <img src="<?php echo htmlspecialchars($item['imagem_url']); ?>" alt="<?php echo htmlspecialchars($item['nome']); ?>" class="cart-product-image">
                                            <?php endif; ?>
                                            <div>
                                                <strong><?php echo htmlspecialchars($item['nome']); ?></strong>
                                                <div class="cart-product-category"><?php echo htmlspecialchars($item['categoria']); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>R$ <?php echo number_format($item['preco'], 2, ',', '.'); ?></td>
                                    <td>
                                        <form method="POST">
                                            <input type="hidden" name="produto_id" value="<?php echo $item['produto_id']; ?>">
                                            <input type="number" name="quantidade" value="<?php echo $item['quantidade']; ?>" min="1" max="10" class="cart-quantity-input">
                                            <button type="submit" name="atualizar_quantidade" class="btn btn-sm">
                                                Atualizar
                                            </button>
                                        </form>
                                    </td>
                                    <td>R$ <?php echo number_format($item['preco'] * $item['quantidade'], 2, ',', '.'); ?></td>
                                    <td>
                                        <form method="POST">
                                            <input type="hidden" name="produto_id" value="<?php echo $item['produto_id']; ?>">
                                            <button type="submit" name="remover_item" class="btn btn-danger btn-sm">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="cart-summary">
                    <div class="cart-total">
                        <strong>Total:</strong> R$ <?php echo number_format($total, 2, ',', '.'); ?>
                    </div>
                    
                    <div class="cart-actions">
                        <form method="POST">
                            <button type="submit" name="limpar_carrinho" class="btn btn-danger">
                                Limpar Carrinho
                            </button>
                        </form>
                        
                        <a href="produtos.php" class="btn">Continuar Comprando</a>
                        
                        <a href="checkout.php" class="btn btn-primary">Finalizar Compra</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 E-commerce Project. Todos os direitos reservados.</p>
            <p>Desenvolvido por <a href="https://dexo-mu.vercel.app/" class="dexo-credit">Dexo</a></p>
        </div>
    </footer>
</body>
</html>