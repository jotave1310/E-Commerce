<?php
session_start();
require_once 'db_connect.php'; // Certifique-se que está incluindo o arquivo correto

// Verificar se está logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php?redirect=checkout.php');
    exit();
}

$usuarioId = $_SESSION['usuario_id'];
$carrinhoId = obterCarrinhoUsuario($usuarioId);
$itensCarrinho = obterItensCarrinho($carrinhoId);
$total = calcularTotalCarrinho($carrinhoId);

// Se o carrinho estiver vazio, redirecionar
if (empty($itensCarrinho)) {
    header('Location: carrinho.php');
    exit();
}

// Obter informações do usuário
$usuario = obterUsuarioPorId($usuarioId);

// Processar o pedido
$erros = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['finalizar_compra'])) {
    // Validar dados
    $nome = trim($_POST['nome'] ?? $usuario['nome']);
    $telefone = trim($_POST['telefone'] ?? $usuario['telefone']);
    $endereco = trim($_POST['endereco'] ?? $usuario['endereco']);
    $cidade = trim($_POST['cidade'] ?? $usuario['cidade']);
    $cep = trim($_POST['cep'] ?? $usuario['cep']);
    $email = trim($_POST['email'] ?? $usuario['email']);
    
    if (empty($nome)) $erros[] = "Nome é obrigatório";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $erros[] = "Email válido é obrigatório";
    if (empty($telefone)) $erros[] = "Telefone é obrigatório";
    if (empty($endereco)) $erros[] = "Endereço é obrigatório";
    if (empty($cidade)) $erros[] = "Cidade é obrigatória";
    if (empty($cep)) $erros[] = "CEP é obrigatório";
    
    if (empty($erros)) {
        // Iniciar transação
        global $pdo;
        $pdo->beginTransaction();
        
        try {
            // Criar pedido
            $stmt = $pdo->prepare("
                INSERT INTO pedidos (usuario_id, total, status, endereco_entrega, data_pedido, data_atualizacao)
                VALUES (?, ?, 'pendente', ?, NOW(), NOW())
            ");
            $stmt->execute([$usuarioId, $total, $endereco]);
            $pedidoId = $pdo->lastInsertId();
            
            // Adicionar itens do pedido
            foreach ($itensCarrinho as $item) {
                $stmt = $pdo->prepare("
                    INSERT INTO itens_pedido (pedido_id, produto_id, quantidade, preco_unitario)
                    VALUES (?, ?, ?, ?)
                ");
                $stmt->execute([$pedidoId, $item['produto_id'], $item['quantidade'], $item['preco']]);
            }
            
            // Limpar carrinho
            limparCarrinho($carrinhoId);
            
            // Commit
            $pdo->commit();
            
            // Redirecionar para sucesso
            $numeroPedido = 'PED' . str_pad($pedidoId, 6, '0', STR_PAD_LEFT);
            header('Location: sucesso.php?pedido=' . $numeroPedido);
            $stmt = $pdo->prepare("UPDATE produtos SET estoque = estoque - ? WHERE id = ?");
            $stmt->execute([$item['quantidade'], $item['produto_id']]);
            $pdo->commit();
            exit();
            
        } catch (Exception $e) {
            $pdo->rollBack();
            $erro = "Erro ao processar pedido: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finalizar Compra - E-commerce Project</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Montserrat:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
    <?php include 'header.php'; ?>

    <main>
        <div class="container">
            <div class="breadcrumb">
                <a href="index.php" class="breadcrumb-item">Início</a>
                <span class="breadcrumb-separator">›</span>
                <span class="breadcrumb-item active">Finalizar Compra</span>
            </div>

            <h1>Finalizar Compra</h1>
            
            <?php if (!empty($erro)): ?>
                <div class="alert alert-error">
                    <strong>Erro:</strong> <?php echo htmlspecialchars($erro); ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($erros)): ?>
                <div class="alert alert-error">
                    <strong>Erros encontrados:</strong>
                    <ul>
                        <?php foreach ($erros as $err): ?>
                            <li><?php echo htmlspecialchars($err); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="checkout-grid">
                <div class="checkout-form">
                    <div class="feature-card">
                        <h2 class="feature-title">Dados para Entrega</h2>
                        <form method="POST">
                            <div class="form-group floating-label">
                                <input type="text" id="nome" name="nome" placeholder=" " required 
                                       value="<?php echo htmlspecialchars($_POST['nome'] ?? $usuario['nome']); ?>">
                                <label for="nome">Nome Completo *</label>
                            </div>
                            
                            <div class="form-group floating-label">
                                <input type="email" id="email" name="email" placeholder=" " required 
                                       value="<?php echo htmlspecialchars($_POST['email'] ?? $usuario['email']); ?>">
                                <label for="email">Email *</label>
                            </div>
                            
                            <div class="form-group floating-label">
                                <input type="tel" id="telefone" name="telefone" placeholder=" " required 
                                       value="<?php echo htmlspecialchars($_POST['telefone'] ?? $usuario['telefone']); ?>">
                                <label for="telefone">Telefone *</label>
                            </div>
                            
                            <div class="form-group floating-label">
                                <input type="text" id="endereco" name="endereco" placeholder=" " required 
                                       value="<?php echo htmlspecialchars($_POST['endereco'] ?? $usuario['endereco']); ?>">
                                <label for="endereco">Endereço Completo *</label>
                            </div>
                            
                            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem;">
                                <div class="form-group floating-label">
                                    <input type="text" id="cidade" name="cidade" placeholder=" " required 
                                           value="<?php echo htmlspecialchars($_POST['cidade'] ?? $usuario['cidade']); ?>">
                                    <label for="cidade">Cidade *</label>
                                </div>
                                
                                <div class="form-group floating-label">
                                    <input type="text" id="cep" name="cep" placeholder=" " required 
                                           value="<?php echo htmlspecialchars($_POST['cep'] ?? $usuario['cep']); ?>">
                                    <label for="cep">CEP *</label>
                                </div>
                            </div>
                            
                            <button type="submit" name="finalizar_compra" class="btn btn-primary btn-block" style="margin-top: 1.5rem;">
                                🛒 Finalizar Compra
                            </button>
                        </form>
                    </div>
                </div>
                
                <div class="checkout-summary">
                    <div class="feature-card">
                        <h2 class="feature-title">Resumo do Pedido</h2>
                        <div class="order-summary">
                            <?php foreach ($itensCarrinho as $item): ?>
                                <div class="order-item">
                                    <div class="order-item-info">
                                        <div class="order-item-name"><?php echo htmlspecialchars($item['nome']); ?></div>
                                        <div class="order-item-quantity">Quantidade: <?php echo $item['quantidade']; ?></div>
                                    </div>
                                    <div class="order-item-price">R$ <?php echo number_format($item['preco'] * $item['quantidade'], 2, ',', '.'); ?></div>
                                </div>
                            <?php endforeach; ?>
                            
                            <div class="order-total">
                                <div>Total:</div>
                                <div class="total-price">R$ <?php echo number_format($total, 2, ',', '.'); ?></div>
                            </div>
                        </div>
                    </div>
                    
                    <a href="carrinho.php" class="btn btn-outline" style="width: 100%; text-align: center; margin-top: 1rem;">
                        ← Voltar ao Carrinho
                    </a>
                </div>
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>