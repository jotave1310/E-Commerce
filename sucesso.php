<?php
session_start();
$numeroPedido = $_GET['pedido'] ?? '';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido Finalizado - E-commerce Project</title>
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
                <span class="breadcrumb-item active">Pedido Finalizado</span>
            </div>

            <div class="success-container">
                <div class="success-icon">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                
                <h1>Pedido Realizado com Sucesso!</h1>
                
                <p>Obrigado por comprar conosco. Seu pedido foi registrado e está sendo processado.</p>
                
                <?php if ($numeroPedido): ?>
                    <div class="order-number">
                        Número do Pedido: <strong><?php echo htmlspecialchars($numeroPedido); ?></strong>
                    </div>
                <?php endif; ?>
                
                <div class="success-actions">
                    <a href="index.php" class="btn btn-outline">
                        <i class="fa-solid fa-house"></i> Voltar à Página Inicial
                    </a>
                    <a href="produtos.php" class="btn btn-primary">
                        <i class="fa-solid fa-bag-shopping"></i> Continuar Comprando
                    </a>
                </div>
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>