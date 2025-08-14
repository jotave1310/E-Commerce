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
    <?php include "header.php"; ?>
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
    border-bottom: 1px solid #f0f0f0;
}

.cart-table th {
    background-color: #f8f9fa;
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
    background-color: #f8f9fa;
    border: 1px solid #e9ecef;
}

.cart-product-category {
    font-size: 0.85rem;
    color: #6c757d;
    margin-top: 0.25rem;
}

.cart-quantity-input {
    width: 60px;
    padding: 0.5rem;
    border: 1px solid #ddd;
    border-radius: var(--border-radius-sm);
    text-align: center;
}

.cart-summary {
    background-color: #f8f9fa;
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
    color: #28a745;
}

.cart-actions {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.btn-sm {
    padding: 0.5rem 1rem;
    font-size: 0.9rem;
}

.btn-update {
    background-color: #007bff;
    color: white;
    border: none;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    cursor: pointer;
    font-size: 0.8rem;
}

.btn-update:hover {
    background-color: #0056b3;
}

/* Responsividade */
@media (max-width: 768px) {
    .cart-table {
        overflow-x: auto;
    }
    
    .cart-actions {
        flex-direction: column;
        align-items: stretch;
    }
    
    .cart-product-info {
        flex-direction: column;
        text-align: center;
    }
    
    .cart-product-image {
        width: 60px;
        height: 60px;
    }
}

.empty-state {
    text-align: center;
    padding: 3rem 1rem;
}

.empty-icon {
    font-size: 4rem;
    color: #6c757d;
    margin-bottom: 1rem;
}

.empty-title {
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
    color: #495057;
}

.empty-description {
    color: #6c757d;
    margin-bottom: 2rem;
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
                    <i class="fa-solid fa-check-circle"></i>
                    <?php echo htmlspecialchars($mensagem); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($erro)): ?>
                <div class="alert alert-error">
                    <i class="fa-solid fa-exclamation-triangle"></i>
                    <?php echo htmlspecialchars($erro); ?>
                </div>
            <?php endif; ?>
            
            <?php if (empty($itensCarrinho)): ?>
                <div class="empty-state">
                    <div class="empty-icon"><i class="fa-solid fa-cart-arrow-down"></i></div>
                    <h4 class="empty-title">Seu carrinho está vazio</h4>
                    <p class="empty-description">Adicione alguns produtos para continuar comprando!</p>
                    <a href="produtos.php" class="btn btn-primary">
                        <i class="fa-solid fa-shopping-bag"></i>
                        Continuar Comprando
                    </a>
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
                                            <img src="<?php echo htmlspecialchars($item['imagem_url']); ?>" 
                                                 alt="<?php echo htmlspecialchars($item['nome']); ?>" 
                                                 class="cart-product-image"
                                                 onerror="this.src='/placeholder.svg?height=80&width=80&text=Sem+Imagem'">
                                            <div>
                                                <strong><?php echo htmlspecialchars($item['nome']); ?></strong>
                                                <?php if (!empty($item['categoria'])): ?>
                                                    <div class="cart-product-category">
                                                        <?php echo htmlspecialchars($item['categoria']); ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td>R$ <?php echo number_format($item['preco'], 2, ',', '.'); ?></td>
                                    <td>
                                        <form method="POST" style="display: flex; align-items: center; gap: 0.5rem;">
                                            <input type="hidden" name="produto_id" value="<?php echo $item['produto_id']; ?>">
                                            <input type="number" name="quantidade" value="<?php echo $item['quantidade']; ?>" 
                                                   min="1" max="10" class="cart-quantity-input">
                                            <button type="submit" name="atualizar_quantidade" class="btn-update">
                                                <i class="fa-solid fa-sync"></i>
                                            </button>
                                        </form>
                                    </td>
                                    <td><strong>R$ <?php echo number_format($item['preco'] * $item['quantidade'], 2, ',', '.'); ?></strong></td>
                                    <td>
                                        <form method="POST" onsubmit="return confirm('Tem certeza que deseja remover este item?')">
                                            <input type="hidden" name="produto_id" value="<?php echo $item['produto_id']; ?>">
                                            <button type="submit" name="remover_item" class="btn btn-danger btn-sm">
                                                <i class="fa-solid fa-trash"></i>
                                                Remover
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
                        <i class="fa-solid fa-calculator"></i>
                        <strong>Total: R$ <?php echo number_format($total, 2, ',', '.'); ?></strong>
                    </div>
                    
                    <div class="cart-actions">
                        <form method="POST" onsubmit="return confirm('Tem certeza que deseja limpar todo o carrinho?')">
                            <button type="submit" name="limpar_carrinho" class="btn btn-danger">
                                <i class="fa-solid fa-trash-can"></i>
                                Limpar Carrinho
                            </button>
                        </form>
                        
                        <a href="produtos.php" class="btn">
                            <i class="fa-solid fa-arrow-left"></i>
                            Continuar Comprando
                        </a>
                        
                        <a href="checkout.php" class="btn btn-primary">
                            <i class="fa-solid fa-credit-card"></i>
                            Finalizar Compra
                        </a>
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

    <script>
        // Confirmação para ações destrutivas
        document.querySelectorAll('form[onsubmit]').forEach(form => {
            form.addEventListener('submit', function(e) {
                const confirmMessage = this.getAttribute('onsubmit').match(/confirm$$'([^']+)'$$/);
                if (confirmMessage && !confirm(confirmMessage[1])) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>
