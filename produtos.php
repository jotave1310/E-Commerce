<?php
session_start();
require_once 'db_connect.php';

// Inicializar carrinho
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Obter categorias para o filtro
$categorias = obterCategorias();

// Parâmetros de filtro com valores padrão
$busca = $_GET['busca'] ?? '';
$categoria_id = $_GET['categoria'] ?? '';
$preco_min = isset($_GET['preco_min']) && is_numeric($_GET['preco_min']) ? floatval($_GET['preco_min']) : null;
$preco_max = isset($_GET['preco_max']) && is_numeric($_GET['preco_max']) ? floatval($_GET['preco_max']) : null;

// Ajustar valores vazios para null
if ($preco_min === 0.0) $preco_min = null;
if ($preco_max === 0.0) $preco_max = null;

// Buscar produtos com filtros aplicados
$produtos = buscarProdutos($busca, $categoria_id, $preco_min, $preco_max);

// Função para formatar preço
function formatarPreco($preco) {
    return 'R$ ' . number_format($preco, 2, ',', '.');
}

// Debug: Exibir parâmetros de filtro (remover em produção)
// echo "<pre>Filtros aplicados:\n";
// echo "Busca: " . htmlspecialchars($busca) . "\n";
// echo "Categoria ID: " . htmlspecialchars($categoria_id) . "\n";
// echo "Preço min: " . ($preco_min ?? 'null') . "\n";
// echo "Preço max: " . ($preco_max ?? 'null') . "\n";
// echo "Produtos encontrados: " . count($produtos) . "</pre>";
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produtos - E-commerce Project</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="components.css">
    <?php include 'header.php'; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css"> 
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Montserrat:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        .products-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: var(--spacing-xl) var(--spacing-lg);
        }
        
        .page-header {
            text-align: center;
            margin-bottom: var(--spacing-3xl);
            padding: var(--spacing-3xl) 0;
            background: linear-gradient(135deg, rgba(233, 69, 96, 0.1) 0%, rgba(15, 52, 96, 0.1) 100%);
            border-radius: var(--border-radius-xl);
            position: relative;
            overflow: hidden;
        }
        
        .page-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 80%, rgba(233, 69, 96, 0.05) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(15, 52, 96, 0.05) 0%, transparent 50%);
            pointer-events: none;
        }
        
        .page-title {
            font-family: 'Montserrat', sans-serif;
            font-size: clamp(2rem, 5vw, 3rem);
            font-weight: 800;
            margin-bottom: var(--spacing-lg);
            background: linear-gradient(135deg, var(--text-light), var(--text-muted));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            position: relative;
            z-index: 1;
        }
        
        .page-subtitle {
            font-size: 1.2rem;
            color: var(--text-muted);
            max-width: 600px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }
        
        .filters-section {
            background: var(--glass-bg);
            backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid var(--glass-border);
            border-radius: var(--border-radius-xl);
            padding: var(--spacing-xl);
            margin-bottom: var(--spacing-3xl);
            transition: all var(--transition-normal);
        }
        
        .filters-section:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }
        
        .filters-title {
            font-family: 'Montserrat', sans-serif;
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--text-light);
            margin-bottom: var(--spacing-lg);
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
        }
        
        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: var(--spacing-lg);
            margin-bottom: var(--spacing-lg);
        }
        
        .filter-group {
            display: flex;
            flex-direction: column;
            gap: var(--spacing-sm);
        }
        
        .filter-label {
            color: var(--text-light);
            font-weight: 500;
            font-size: 0.9rem;
        }

        .filter-input, .filter-select {
            padding: var(--spacing-md);
            background: rgba(255, 255, 255, 0.08);
            border: 2px solid var(--glass-border);
            border-radius: var(--border-radius-md);
            color: var(--text-light);
            font-size: 0.95rem;
            transition: all var(--transition-normal);
            backdrop-filter: blur(10px);
        }

        .filter-select option{
            color: black;
        }
        
        .filter-input:focus, .filter-select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(233, 69, 96, 0.2);
            background: rgba(255, 255, 255, 0.12);
            transform: translateY(-2px);
        }
        
        .filter-input::placeholder {
            color: var(--text-dark);
        }
        
        .filter-actions {
            display: flex;
            gap: var(--spacing-md);
            justify-content: center;
        }
        
        .btn-filter {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: white;
            border: none;
            padding: var(--spacing-md) var(--spacing-xl);
            border-radius: var(--border-radius-md);
            font-weight: 600;
            cursor: pointer;
            transition: all var(--transition-normal);
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-filter:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }
        
        .btn-clear {
            background: transparent;
            color: var(--text-light);
            border: 2px solid var(--glass-border);
            padding: var(--spacing-md) var(--spacing-xl);
            border-radius: var(--border-radius-md);
            font-weight: 600;
            cursor: pointer;
            transition: all var(--transition-normal);
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-clear:hover {
            background: var(--glass-bg);
            border-color: var(--primary-color);
            transform: translateY(-2px);
        }
        
        .products-stats {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: var(--spacing-xl);
            padding: var(--spacing-lg);
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--border-radius-lg);
        }
        
        .products-count {
            color: var(--text-light);
            font-weight: 500;
        }
        
        .sort-select {
            padding: var(--spacing-sm) var(--spacing-md);
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid var(--glass-border);
            border-radius: var(--border-radius-md);
            color: var(--text-light);
            cursor: pointer;
        }
        
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: var(--spacing-xl);
            margin-bottom: var(--spacing-3xl);
        }
        
        .product-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid var(--glass-border);
            border-radius: var(--border-radius-xl);
            padding: var(--spacing-xl);
            transition: all var(--transition-normal);
            position: relative;
            overflow: hidden;
            cursor: pointer;
        }
        
        .product-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.05), transparent);
            transition: left var(--transition-slow);
            pointer-events: none;
        }
        
        .product-card:hover::before {
            left: 100%;
        }
        
        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-xl);
            border-color: rgba(255, 255, 255, 0.2);
        }
        
        .product-image {
            width: 100%;
            aspect-ratio: 1/1; /* Mantém proporção quadrada (ajuste conforme necessidade) */
            background: linear-gradient(135deg, var(--card-bg), var(--dark-bg));
            border-radius: var(--border-radius-lg);
            margin-bottom: var(--spacing-lg);
            position: relative;
            overflow: hidden;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover; /* Mantém toda a imagem visível */
            object-position: center;
            transition: transform var(--transition-normal);
        }

        /* Opcional: Efeito hover suave */
        .product-card:hover .product-image img {
            transform: scale(1.05);
        }
                
        .product-category {
            display: inline-block;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: white;
            padding: var(--spacing-xs) var(--spacing-md);
            border-radius: var(--border-radius-full);
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: var(--spacing-md);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .product-title {
            font-family: 'Montserrat', sans-serif;
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--text-light);
            margin-bottom: var(--spacing-sm);
            line-height: 1.3;
        }
        
        .product-price {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: var(--spacing-md);
        }
        
        .product-description {
            color: var(--text-muted);
            font-size: 0.9rem;
            margin-bottom: var(--spacing-lg);
            line-height: 1.5;
        }
        
        .product-stock {
            color: var(--success-color);
            font-size: 0.85rem;
            margin-bottom: var(--spacing-md);
            font-weight: 500;
        }
        
        .product-stock.low {
            color: var(--warning-color);
        }
        
        .product-stock.out {
            color: var(--danger-color);
        }
        
        .no-products {
            text-align: center;
            padding: var(--spacing-3xl);
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--border-radius-xl);
            color: var(--text-muted);
        }
        
        .no-products-icon {
            font-size: 4rem;
            margin-bottom: var(--spacing-lg);
            opacity: 0.5;
            color: white;
        }
        
        .no-products-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-light);
            margin-bottom: var(--spacing-md);
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            gap: var(--spacing-md);
            margin-top: var(--spacing-3xl);
        }
        
        .pagination-btn {
            padding: var(--spacing-md) var(--spacing-lg);
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: var(--border-radius-md);
            color: var(--text-light);
            text-decoration: none;
            transition: all var(--transition-normal);
        }
        
        .pagination-btn:hover {
            background: var(--primary-color);
            border-color: var(--primary-color);
            transform: translateY(-2px);
        }
        
        .pagination-btn.active {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        @media (max-width: 768px) {
            .filters-grid {
                grid-template-columns: 1fr;
            }
            
            .products-grid {
                grid-template-columns: 1fr;
            }
            
            .products-stats {
                flex-direction: column;
                gap: var(--spacing-md);
                text-align: center;
            }
            
            .filter-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <a href="index.php" class="logo">E-commerce Project</a>
                <nav>
                    <ul>
                        <li><a href="index.php">Início</a></li>
                        <li><a href="produtos.php" class="active">Produtos</a></li>
                        <li><a href="sobre.php">Sobre</a></li>
                        <li><a href="contato.php">Contato</a></li>
                        <?php if (isset($_SESSION['usuario_id'])): ?>
                            <li><a href="perfil.php">Meu Perfil</a></li>
                            <?php if ($_SESSION['usuario_tipo'] == 'admin'): ?>
                                <li><a href="admin.php">Admin</a></li>
                            <?php endif; ?>
                            <li><a href="logout.php">Sair</a></li>
                        <?php else: ?>
                            <li><a href="login.php" class="btn-login">Login</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <a href="carrinho.php" class="cart-icon">
                    <i class="fa-solid fa-cart-shopping"></i> Carrinho (<?php echo array_sum($_SESSION['cart']); ?>)
                </a>
            </div>
        </div>
    </header>

    <main class="products-container">
        <div class="page-header">
            <h1 class="page-title">Catálogo de Produtos</h1>
            <p class="page-subtitle">Descubra nossa seleção completa de produtos tecnológicos</p>
        </div>

        <div class="filters-section">
            <h2 class="filters-title">Filtros de Busca</h2>
            <form method="GET" action="">
                <div class="filters-grid">
                    <div class="filter-group">
                        <label class="filter-label">Buscar Produto</label>
                        <input type="text" name="busca" class="filter-input" 
                               placeholder="Digite o nome do produto..." 
                               value="<?= htmlspecialchars($busca) ?>">
                    </div>
                    
                    <div class="filter-group">
                        <label class="filter-label">Categoria</label>
                        <select name="categoria" class="filter-select">
                            <option value="">Todas as categorias</option>
                            <?php foreach ($categorias as $categoria): ?>
                                <option value="<?= $categoria['id'] ?>" 
                                    <?= $categoria_id == $categoria['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($categoria['nome']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label class="filter-label">Preço Mínimo (R$)</label>
                        <input type="number" name="preco_min" class="filter-input" 
                               placeholder="0,00" step="0.01" min="0"
                               value="<?= $preco_min !== null ? $preco_min : '' ?>">
                    </div>
                    
                    <div class="filter-group">
                        <label class="filter-label">Preço Máximo (R$)</label>
                        <input type="number" name="preco_max" class="filter-input" 
                               placeholder="999999,00" step="0.01" min="0"
                               value="<?= $preco_max !== null ? $preco_max : '' ?>">
                    </div>
                </div>
                
                <div class="filter-actions">
                    <button type="submit" class="btn-filter"><i class="fa-solid fa-magnifying-glass"></i> Aplicar Filtros</button>
                    <a href="produtos.php" class="btn-clear"><i class="fa-solid fa-trash"></i> Limpar Filtros</a>
                </div>
            </form>
        </div>

        <div class="products-stats">
            <div class="products-count">
                <strong><?= count($produtos) ?></strong> produto(s) encontrado(s)
                <?php if (!empty($categoria_id) || !empty($busca) || $preco_min !== null || $preco_max !== null): ?>
                    com os filtros aplicados
                <?php endif; ?>
            </div>
            <select class="sort-select" onchange="sortProducts(this.value)">
                <option value="nome">Ordenar por Nome</option>
                <option value="preco_asc">Menor Preço</option>
                <option value="preco_desc">Maior Preço</option>
                <option value="categoria">Categoria</option>
            </select>
        </div>

        <?php if (empty($produtos)): ?>
            <div class="no-products">
                <div class="no-products-icon" style="color: white;"><i class="fa-solid fa-box-open"></i></div>
                <h3 class="no-products-title">Nenhum produto encontrado</h3>
                <p>Tente ajustar os filtros de busca ou navegue por todas as categorias.</p>
                <a href="produtos.php" class="btn btn-primary" style="margin-top: 1rem;">
                    Ver Todos os Produtos
                </a>
            </div>
        <?php else: ?>
            <div class="products-grid" id="products-grid">
                <?php foreach ($produtos as $produto): ?>
                    <div class="product-card" 
                         data-nome="<?= htmlspecialchars($produto['nome']) ?>" 
                         data-preco="<?= $produto['preco'] ?>" 
                         data-categoria="<?= htmlspecialchars($produto['categoria_nome']) ?>">
                        
                        <div class="product-image">
                            <?php if (!empty($produto['imagem_url'])): ?>
                                <img src="<?= htmlspecialchars($produto['imagem_url']) ?>" 
                                     alt="<?= htmlspecialchars($produto['nome']) ?>">
                            <?php else: ?>
                                <span>Imagem do Produto</span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="product-category">
                            <?= htmlspecialchars($produto['categoria_nome']) ?>
                        </div>
                        
                        <h3 class="product-title">
                            <?= htmlspecialchars($produto['nome']) ?>
                        </h3>
                        
                        <div class="product-price">
                            <?= formatarPreco($produto['preco']) ?>
                        </div>
                        
                        <p class="product-description">
                            <?= htmlspecialchars($produto['descricao_curta']) ?>
                        </p>
                        
                        <div class="product-stock <?= $produto['estoque'] <= 5 ? ($produto['estoque'] == 0 ? 'out' : 'low') : '' ?>">
                            <?php if ($produto['estoque'] == 0): ?>
                                <i class="fa-solid fa-x"></i> Fora de estoque
                            <?php elseif ($produto['estoque'] <= 5): ?>
                                <i class="fa-solid fa-triangle-exclamation"></i> Últimas <?= $produto['estoque'] ?> unidades
                            <?php else: ?>
                                <i class="fa-solid fa-check"></i> Em estoque (<?= $produto['estoque'] ?> unidades)
                            <?php endif; ?>
                        </div>
                        
                        <a href="produto.php?id=<?= $produto['id'] ?>" class="btn btn-primary">
                            Ver Detalhes
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 E-commerce Project. Todos os direitos reservados.</p>
            <p>Desenvolvido por <a href="https://dexo-mu.vercel.app/" class="dexo-credit">Dexo</a></p>
        </div>
    </footer>

    <script>
        // Função para ordenar produtos
        function sortProducts(sortBy) {
            const grid = document.getElementById('products-grid');
            const cards = Array.from(grid.querySelectorAll('.product-card'));
            
            cards.sort((a, b) => {
                const aData = a.dataset;
                const bData = b.dataset;
                
                switch(sortBy) {
                    case 'nome':
                        return aData.nome.localeCompare(bData.nome);
                    case 'preco_asc':
                        return parseFloat(aData.preco) - parseFloat(bData.preco);
                    case 'preco_desc':
                        return parseFloat(bData.preco) - parseFloat(aData.preco);
                    case 'categoria':
                        return aData.categoria.localeCompare(bData.categoria);
                    default:
                        return 0;
                }
            });
            
            // Remover todos os cards
            while (grid.firstChild) {
                grid.removeChild(grid.firstChild);
            }
            
            // Adicionar cards ordenados
            cards.forEach(card => {
                grid.appendChild(card);
            });
        }
        
        // Ativar animações ao carregar a página
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.product-card');
            cards.forEach((card, index) => {
                card.style.animationDelay = `${index * 0.1}s`;
                card.classList.add('animate-in');
            });
        });
    </script>
</body>
</html>

