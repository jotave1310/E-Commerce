<?php
session_start();
require_once 'config.php';

// Inicializar carrinho se não existir
if (!isset($_SESSION['cart'])        // Processar dados do formulário
        $dadosCliente = [
            'nome' => trim($_POST['nome'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'telefone' => trim($_POST['telefone'] ?? ''),
            'endereco' => trim($_POST['endereco'] ?? ''),
            'cidade' => trim($_POST['cidade'] ?? ''),
            'cep' => trim($_POST['cep'] ?? '')
        ];
        
        // Validar dados
        $erros = [];
        if (empty($dadosCliente['nome'])) $erros[] = "Nome é obrigatório";
        if (empty($dadosCliente['email']) || !filter_var($dadosCliente['email'], FILTER_VALIDATE_EMAIL)) {
            $erros[] = "Email válido é obrigatório";
        }
        if (empty($dadosCliente['telefone'])) $erros[] = "Telefone é obrigatório";
        if (empty($dadosCliente['endereco'])) $erros[] = "Endereço é obrigatório";
        if (empty($dadosCliente['cidade'])) $erros[] = "Cidade é obrigatória";
        if (empty($dadosCliente['cep'])) $erros[] = "CEP é obrigatório";
        
        if (empty($erros)) {
            // Calcular total
            $total = calcularTotalCarrinho($_SESSION['cart']);
            
            // Salvar pedido no banco de dados
            $pedidoId = salvarPedido($dadosCliente, $_SESSION['cart'], $total);
            
            if ($pedidoId) {
                // Limpar carrinho
                $_SESSION['cart'] = array();
                
                // Redirecionar para página de sucesso
                $numeroPedido = 'PED' . str_pad($pedidoId, 6, '0', STR_PAD_LEFT);
                header('Location: sucesso.php?pedido=' . $numeroPedido);
                exit;
            } else {
                $mensagem = "Erro ao processar pedido. Tente novamente.";
            }
        }
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include 'header.php'; ?>
    <title>Finalizar Compra - E-commerce Project</title>
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
                    <i class="fa-solid fa-cart-shopping"></i> Carrinho (<?php echo count($_SESSION['cart']); ?>)
                </a>
            </div>
        </div>
    </header>

    <main>
        <div class="container">
            <h1 style="color: #2c3e50; margin-bottom: 2rem;">Finalizar Compra</h1>

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

            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 3rem;">
                <!-- Formulário de dados -->
                <div style="background-color: white; padding: 2rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    <h2 style="margin-bottom: 1.5rem; color: #2c3e50;">Dados para Entrega</h2>
                    
                    <form method="POST">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                            <div>
                                <label for="nome" style="display: block; margin-bottom: 0.5rem; font-weight: bold;">Nome Completo *</label>
                                <input type="text" id="nome" name="nome" required 
                                       value="<?php echo htmlspecialchars($_POST['nome'] ?? ''); ?>"
                                       style="width: 100%; padding: 0.7rem; border: 1px solid #ddd; border-radius: 5px;">
                            </div>
                            <div>
                                <label for="email" style="display: block; margin-bottom: 0.5rem; font-weight: bold;">Email *</label>
                                <input type="email" id="email" name="email" required 
                                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                                       style="width: 100%; padding: 0.7rem; border: 1px solid #ddd; border-radius: 5px;">
                            </div>
                        </div>

                        <div style="margin-bottom: 1rem;">
                            <label for="telefone" style="display: block; margin-bottom: 0.5rem; font-weight: bold;">Telefone *</label>
                            <input type="tel" id="telefone" name="telefone" required 
                                   value="<?php echo htmlspecialchars($_POST['telefone'] ?? ''); ?>"
                                   style="width: 100%; padding: 0.7rem; border: 1px solid #ddd; border-radius: 5px;">
                        </div>

                        <div style="margin-bottom: 1rem;">
                            <label for="endereco" style="display: block; margin-bottom: 0.5rem; font-weight: bold;">Endereço Completo *</label>
                            <input type="text" id="endereco" name="endereco" required 
                                   value="<?php echo htmlspecialchars($_POST['endereco'] ?? ''); ?>"
                                   style="width: 100%; padding: 0.7rem; border: 1px solid #ddd; border-radius: 5px;">
                        </div>

                        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem; margin-bottom: 2rem;">
                            <div>
                                <label for="cidade" style="display: block; margin-bottom: 0.5rem; font-weight: bold;">Cidade *</label>
                                <input type="text" id="cidade" name="cidade" required 
                                       value="<?php echo htmlspecialchars($_POST['cidade'] ?? ''); ?>"
                                       style="width: 100%; padding: 0.7rem; border: 1px solid #ddd; border-radius: 5px;">
                            </div>
                            <div>
                                <label for="cep" style="display: block; margin-bottom: 0.5rem; font-weight: bold;">CEP *</label>
                                <input type="text" id="cep" name="cep" required 
                                       value="<?php echo htmlspecialchars($_POST['cep'] ?? ''); ?>"
                                       style="width: 100%; padding: 0.7rem; border: 1px solid #ddd; border-radius: 5px;">
                            </div>
                        </div>

                        <button type="submit" name="finalizar_compra" class="btn btn-success" style="width: 100%; padding: 1rem; font-size: 1.1rem;">
                            🛒 Finalizar Compra
                        </button>
                    </form>
                </div>

                <!-- Resumo do pedido -->
                <div style="background-color: white; padding: 2rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); height: fit-content;">
                    <h2 style="margin-bottom: 1.5rem; color: #2c3e50;">Resumo do Pedido</h2>
                    
                    <?php foreach ($_SESSION['cart'] as $produtoId => $quantidade): ?>
                        <?php $produto = obterProduto($produtoId); ?>
                        <?php if ($produto): ?>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid #ecf0f1;">
                                <div>
                                    <strong><?php echo htmlspecialchars($produto['nome']); ?></strong><br>
                                    <small>Quantidade: <?php echo $quantidade; ?></small>
                                </div>
                                <div style="text-align: right;">
                                    <?php echo formatarPreco($produto['preco'] * $quantidade); ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    
                    <div style="border-top: 2px solid #2c3e50; padding-top: 1rem; margin-top: 1rem;">
                        <div style="display: flex; justify-content: space-between; font-size: 1.2rem; font-weight: bold;">
                            <span>Total:</span>
                            <span style="color: #e74c3c;"><?php echo formatarPreco($total); ?></span>
                        </div>
                    </div>
                    
                    <div style="margin-top: 1.5rem;">
                        <a href="carrinho.php" class="btn" style="width: 100%; text-align: center; display: block;">
                            ← Voltar ao Carrinho
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 E-commerce Project. Todos os direitos reservados.</p>
        </div>
    </footer>
</body>
</html>

