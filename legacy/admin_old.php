<?php
session_start();
require_once 'config.php';

// Verificar se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $categoria = $_POST['categoria'] ?? '';
    $preco = $_POST['preco'] ?? '';
    $descricao = $_POST['descricao'] ?? '';
    
    // Validar dados
    if (!empty($nome) && !empty($categoria) && !empty($preco) && !empty($descricao)) {
        // Adicionar produto ao banco de dados
        if (adicionarProduto($nome, $categoria, $preco, $descricao)) {
            $mensagem = "Produto adicionado com sucesso!";
            $tipo_mensagem = "success";
        } else {
            $mensagem = "Erro ao adicionar produto. Tente novamente.";
            $tipo_mensagem = "error";
        }
    } else {
        $mensagem = "Por favor, preencha todos os campos.";
        $tipo_mensagem = "error";
    }
}

// Obter todos os produtos para exibir na lista
$produtos = obterTodosProdutos();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo - E-commerce Project</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .admin-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 20px;
        }
        
        .admin-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .admin-sections {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            margin-bottom: 3rem;
        }
        
        .admin-section {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            padding: 2rem;
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .admin-section h2 {
            margin-bottom: 1.5rem;
            color: #e94560;
            border-bottom: 2px solid #e94560;
            padding-bottom: 0.5rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .products-list {
            max-height: 400px;
            overflow-y: auto;
        }
        
        .product-item {
            background: rgba(255, 255, 255, 0.05);
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 8px;
            border-left: 3px solid #e94560;
        }
        
        .product-item h4 {
            color: #ffffff;
            margin-bottom: 0.5rem;
        }
        
        .product-item .product-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            font-size: 0.9rem;
            color: #cccccc;
        }
        
        .back-to-site {
            text-align: center;
            margin-top: 2rem;
        }
        
        @media (max-width: 768px) {
            .admin-sections {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">Admin Panel</div>
                <nav>
                    <ul>
                        <li><a href="index.php">🏠 Voltar ao Site</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <main>
        <div class="admin-container">
            <div class="admin-header">
                <h1>Painel Administrativo</h1>
                <p>Gerencie os produtos da sua loja</p>
            </div>

            <?php if (isset($mensagem)): ?>
                <div class="alert alert-<?php echo $tipo_mensagem; ?>">
                    <?php echo htmlspecialchars($mensagem); ?>
                </div>
            <?php endif; ?>

            <div class="admin-sections">
                <!-- Seção de Adicionar Produto -->
                <div class="admin-section">
                    <h2>📦 Adicionar Novo Produto</h2>
                    <form method="POST" action="admin.php">
                        <div class="form-group">
                            <label for="nome">Nome do Produto *</label>
                            <input type="text" id="nome" name="nome" required placeholder="Ex: Smartphone Samsung Galaxy">
                        </div>

                        <div class="form-group">
                            <label for="categoria">Categoria *</label>
                            <select id="categoria" name="categoria" required>
                                <option value="">Selecione uma categoria</option>
                                <option value="Smartphones">Smartphones</option>
                                <option value="Notebooks">Notebooks</option>
                                <option value="Tablets">Tablets</option>
                                <option value="Acessórios">Acessórios</option>
                                <option value="Games">Games</option>
                                <option value="Audio">Audio</option>
                                <option value="Computadores">Computadores</option>
                                <option value="Periféricos">Periféricos</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="preco">Preço (R$) *</label>
                            <input type="number" id="preco" name="preco" step="0.01" min="0" required placeholder="0.00">
                        </div>

                        <div class="form-group">
                            <label for="descricao">Descrição *</label>
                            <textarea id="descricao" name="descricao" rows="4" required placeholder="Descreva as características do produto..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-success">✅ Adicionar Produto</button>
                    </form>
                </div>

                <!-- Seção de Lista de Produtos -->
                <div class="admin-section">
                    <h2>📋 Produtos Cadastrados (<?php echo count($produtos); ?>)</h2>
                    <div class="products-list">
                        <?php if (empty($produtos)): ?>
                            <p style="text-align: center; color: #cccccc; padding: 2rem;">
                                Nenhum produto cadastrado ainda.
                            </p>
                        <?php else: ?>
                            <?php foreach ($produtos as $produto): ?>
                                <div class="product-item">
                                    <h4><?php echo htmlspecialchars($produto['nome']); ?></h4>
                                    <div class="product-info">
                                        <span><strong>Categoria:</strong> <?php echo htmlspecialchars($produto['categoria']); ?></span>
                                        <span><strong>Preço:</strong> <?php echo formatarPreco($produto['preco']); ?></span>
                                    </div>
                                    <p style="margin-top: 0.5rem; color: #cccccc; font-size: 0.9rem;">
                                        <?php echo htmlspecialchars($produto['descricao']); ?>
                                    </p>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="back-to-site">
                <a href="index.php" class="btn btn-primary">🏠 Voltar para a Loja</a>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 E-commerce Project - Painel Administrativo. | Dexo</p>
        </div>
    </footer>

    <script>
        // Auto-hide alert messages after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-20px)';
                    setTimeout(function() {
                        alert.remove();
                    }, 300);
                }, 5000);
            });
        });

        // Format price input
        document.getElementById('preco').addEventListener('input', function(e) {
            let value = e.target.value;
            if (value && !isNaN(value)) {
                e.target.value = parseFloat(value).toFixed(2);
            }
        });
    </script>
</body>
</html>

