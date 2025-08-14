<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

$produto_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$produto = obterProdutoPorId($produto_id);

if (!$produto) {
    header('Location: produtos.php');
    exit();
}

// Obter avaliações do produto
$avaliacoes = obterAvaliacoesProduto($produto_id);

// Obter produtos relacionados (mesma categoria, excluindo o produto atual)
$produtos_relacionados = obterProdutosRelacionados($produto['categoria_id'], $produto_id);

// Processar envio de avaliação
$avaliacao_sucesso = '';
$avaliacao_erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enviar_avaliacao'])) {
    if (!isset($_SESSION['usuario_id'])) {
        $avaliacao_erro = 'Você precisa estar logado para enviar uma avaliação.';
    } else {
        $usuario_id = $_SESSION['usuario_id'];
        $nota = isset($_POST['nota']) ? intval($_POST['nota']) : 0;
        $comentario = trim($_POST['comentario'] ?? '');

        if ($nota < 1 || $nota > 5) {
            $avaliacao_erro = 'A nota deve ser entre 1 e 5 estrelas.';
        } elseif (empty($comentario)) {
            $avaliacao_erro = 'O comentário da avaliação é obrigatório.';
        } else {
            if (adicionarAvaliacao($produto_id, $usuario_id, $nota, $comentario)) {
                $avaliacao_sucesso = 'Sua avaliação foi enviada com sucesso!';
                // Recarregar avaliações após adicionar nova
                $avaliacoes = obterAvaliacoesProduto($produto_id);
            } else {
                $avaliacao_erro = 'Erro ao enviar avaliação. Tente novamente.';
            }
        }
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
    <link rel="stylesheet" href="components.css">
    <?php include 'header.php'; ?>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Montserrat:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        .product-detail-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: var(--spacing-xl) var(--spacing-lg);
        }

        .product-detail-grid {
            display: grid;
            grid-template-columns: 1fr 1.5fr;
            gap: var(--spacing-3xl);
            margin-bottom: var(--spacing-3xl);
        }

        .product-image-gallery {
            background: var(--glass-bg);
            backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid var(--glass-border);
            border-radius: var(--border-radius-xl);
            padding: var(--spacing-xl);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .main-image {
            width: 100%;
            max-height: 400px;
            object-fit: contain;
            border-radius: var(--border-radius-lg);
            margin-bottom: var(--spacing-lg);
        }

        .thumbnail-gallery {
            display: flex;
            gap: var(--spacing-md);
            overflow-x: auto;
            padding-bottom: var(--spacing-sm);
        }

        .thumbnail {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: var(--border-radius-md);
            border: 2px solid transparent;
            cursor: pointer;
            transition: all var(--transition-normal);
        }

        .thumbnail.active, .thumbnail:hover {
            border-color: var(--primary-color);
            transform: translateY(-2px);
        }

        .product-info {
            background: var(--glass-bg);
            backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid var(--glass-border);
            border-radius: var(--border-radius-xl);
            padding: var(--spacing-3xl);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
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
            font-size: clamp(1.8rem, 4vw, 2.5rem);
            font-weight: 800;
            color: var(--text-light);
            margin-bottom: var(--spacing-md);
            line-height: 1.2;
        }

        .product-rating {
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
            margin-bottom: var(--spacing-md);
        }

        .stars {
            color: gold;
            font-size: 1.2rem;
        }

        .reviews-count {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .product-price {
            font-size: clamp(2rem, 5vw, 3rem);
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: var(--spacing-xl);
        }

        .product-short-description {
            color: var(--text-muted);
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: var(--spacing-xl);
        }

        .add-to-cart-section {
            display: flex;
            align-items: center;
            gap: var(--spacing-lg);
            margin-top: var(--spacing-xl);
            padding-top: var(--spacing-xl);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .quantity-control {
            display: flex;
            align-items: center;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid var(--glass-border);
            border-radius: var(--border-radius-md);
            overflow: hidden;
        }

        .quantity-btn {
            background: transparent;
            border: none;
            color: var(--text-light);
            padding: var(--spacing-md);
            font-size: 1.2rem;
            cursor: pointer;
            transition: background var(--transition-normal);
        }

        .quantity-btn:hover {
            background: rgba(255, 255, 255, 0.15);
        }

        .quantity-input {
            width: 60px;
            text-align: center;
            background: transparent;
            border: none;
            color: var(--text-light);
            font-size: 1rem;
            -moz-appearance: textfield;
        }

        .quantity-input::-webkit-outer-spin-button,
        .quantity-input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .btn-add-to-cart {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: white;
            border: none;
            padding: var(--spacing-md) var(--spacing-xl);
            border-radius: var(--border-radius-md);
            font-weight: 600;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all var(--transition-normal);
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
        }

        .btn-add-to-cart:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-lg);
        }

        .product-full-description-section {
            background: var(--glass-bg);
            backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid var(--glass-border);
            border-radius: var(--border-radius-xl);
            padding: var(--spacing-3xl);
            margin-bottom: var(--spacing-3xl);
        }

        .section-title {
            font-family: 'Montserrat', sans-serif;
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--text-light);
            margin-bottom: var(--spacing-xl);
            display: flex;
            align-items: center;
            gap: var(--spacing-md);
        }

        .section-title::after {
            content: '';
            flex: 1;
            height: 2px;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            border-radius: var(--border-radius-full);
        }

        .product-full-description p {
            color: var(--text-muted);
            line-height: 1.8;
            margin-bottom: var(--spacing-lg);
        }

        .product-specs table {
            width: 100%;
            border-collapse: collapse;
            margin-top: var(--spacing-xl);
        }

        .product-specs th, .product-specs td {
            padding: var(--spacing-md);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--text-light);
            text-align: left;
        }

        .product-specs th {
            background: rgba(255, 255, 255, 0.05);
            font-weight: 600;
        }

        .product-specs td {
            background: rgba(255, 255, 255, 0.02);
            color: var(--text-muted);
        }

        .reviews-section {
            background: var(--glass-bg);
            backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid var(--glass-border);
            border-radius: var(--border-radius-xl);
            padding: var(--spacing-3xl);
            margin-bottom: var(--spacing-3xl);
        }

        .review-form-section {
            margin-top: var(--spacing-xl);
            padding-top: var(--spacing-xl);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .rating-input {
            display: flex;
            gap: var(--spacing-sm);
            margin-bottom: var(--spacing-lg);
        }

        .rating-input input[type="radio"] {
            display: none;
        }

        .rating-input label {
            font-size: 2rem;
            color: var(--text-dark);
            cursor: pointer;
            transition: color var(--transition-normal);
        }

        .rating-input label:hover,
        .rating-input label:hover ~ label,
        .rating-input input[type="radio"]:checked ~ label {
            color: gold;
        }

        .review-list {
            display: flex;
            flex-direction: column;
            gap: var(--spacing-xl);
        }

        .review-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: var(--border-radius-lg);
            padding: var(--spacing-lg);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .review-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: var(--spacing-sm);
        }

        .reviewer-name {
            font-weight: 600;
            color: var(--text-light);
        }

        .review-date {
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        .review-stars {
            color: gold;
            font-size: 1rem;
            margin-bottom: var(--spacing-sm);
        }

        .review-comment {
            color: var(--text-muted);
            line-height: 1.6;
        }

        .related-products-section {
            background: var(--glass-bg);
            backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid var(--glass-border);
            border-radius: var(--border-radius-xl);
            padding: var(--spacing-3xl);
        }

        .related-products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: var(--spacing-xl);
        }
        .stock-warning {
            margin-bottom: var(--spacing-md);
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .stock-warning.out-of-stock {
            color: #ff6b6b;
        }

        .stock-warning.low-stock {
            color: #feca57;
        }

        @media (max-width: 992px) {
            .product-detail-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .add-to-cart-section {
                flex-direction: column;
                align-items: stretch;
            }

            .quantity-control {
                width: 100%;
                justify-content: center;
            }

            .btn-add-to-cart {
                width: 100%;
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
                        <li><a href="produtos.php">Produtos</a></li>
                        <li><a href="sobre.php">Sobre</a></li>
                        <li><a href="contato.php">Contato</a></li>
                        <?php if (isset($_SESSION['usuario_id'])): ?>
                            <li><a href="perfil.php">Olá, <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?>!</a></li>
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

    <main class="product-detail-container">
        <div class="product-detail-grid">
            <div class="product-image-gallery">
                <img src="<?php echo htmlspecialchars($produto['imagem_url']); ?>" alt="<?php echo htmlspecialchars($produto['nome']); ?>" class="main-image" id="mainProductImage">
                <div class="thumbnail-gallery">
                    <img src="<?php echo htmlspecialchars($produto['imagem_url']); ?>" alt="<?php echo htmlspecialchars($produto['nome']); ?>" class="thumbnail active" onclick="changeMainImage(this)">
                    <?php
                    // Adicionar mais imagens de exemplo se houver
                    // Isso seria dinâmico se houvesse uma tabela de imagens para produtos
                    $extra_images = [
                        'https://via.placeholder.com/150/FF5733/FFFFFF?text=Imagem+2',
                        'https://via.placeholder.com/150/33FF57/FFFFFF?text=Imagem+3',
                        'https://via.placeholder.com/150/3357FF/FFFFFF?text=Imagem+4'
                    ];
                    foreach ($extra_images as $img_url) {
                        echo '<img src="' . htmlspecialchars($img_url) . '" alt="' . htmlspecialchars($produto['nome']) . '" class="thumbnail" onclick="changeMainImage(this)">';
                    }
                    ?>
                </div>
            </div>

            <div class="product-info">
                <div>
                    <div class="product-category"><?php echo htmlspecialchars($produto['categoria_nome']); ?></div>
                    <h1 class="product-title"><?php echo htmlspecialchars($produto['nome']); ?></h1>
                    <div class="product-rating">
                        <span class="stars">★★★★★</span>
                        <span class="reviews-count">(<?php echo count($avaliacoes); ?> avaliações)</span>
                    </div>
                    <div class="product-price">R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></div>
                    <?php if ($produto['estoque'] <= 0): ?>
                    <div class="stock-warning" style="color: #ff6b6b; margin-bottom: var(--spacing-md);">
                        ⚠️ Este produto está temporariamente fora de estoque
                    </div>
                <?php elseif ($produto['estoque'] < 5): ?>
                    <div class="stock-warning" style="color: #feca57; margin-bottom: var(--spacing-md);">
                        ⚠️ Últimas unidades disponíveis!
                    </div>
                <?php endif; ?>
                    <p class="product-short-description"><?php echo htmlspecialchars($produto['descricao_curta']); ?></p>
                </div>

                <div class="add-to-cart-section">
                    <?php if ($produto['estoque'] > 0): ?>
                        <div class="quantity-control">
                            <button class="quantity-btn" onclick="changeQuantity(-1)">-</button>
                            <input type="number" id="quantity" value="1" min="1" max="<?php echo $produto['estoque']; ?>" class="quantity-input">
                            <button class="quantity-btn" onclick="changeQuantity(1)">+</button>
                        </div>
                        <button class="btn-add-to-cart" onclick="addToCart(<?php echo $produto['id']; ?>)">
                            <span>🛒</span> Adicionar ao Carrinho
                        </button>
                    <?php else: ?>
                        <div class="out-of-stock">
                            <span>⚠️</span> Fora de Estoque
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="product-full-description-section">
            <h2 class="section-title">📝 Descrição Detalhada</h2>
            <div class="product-full-description">
                <?php echo nl2br(htmlspecialchars($produto['descricao_longa'])); // nl2br para quebras de linha ?>
            </div>

            <h3 class="section-title" style="margin-top: var(--spacing-3xl);">📊 Especificações Técnicas</h3>
            <div class="product-specs">
                <table>
                    <thead>
                        <tr>
                            <th>Característica</th>
                            <th>Detalhe</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Exemplo de como exibir especificações. Idealmente, viriam do BD.
                        $specs = json_decode($produto['especificacoes_json'], true);
                        if ($specs) {
                            foreach ($specs as $key => $value) {
                                echo '<tr><td>' . htmlspecialchars($key) . '</td><td>' . htmlspecialchars($value) . '</td></tr>';
                            }
                        } else {
                            echo '<tr><td colspan="2">Nenhuma especificação disponível.</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="reviews-section">
            <h2 class="section-title">⭐ Avaliações de Clientes</h2>

            <?php if (!empty($avaliacao_sucesso)): ?>
                <div class="alert alert-success">
                    <strong>✅ <?php echo htmlspecialchars($avaliacao_sucesso); ?></strong>
                </div>
            <?php endif; ?>

            <?php if (!empty($avaliacao_erro)): ?>
                <div class="alert alert-error">
                    <strong>❌ <?php echo htmlspecialchars($avaliacao_erro); ?></strong>
                </div>
            <?php endif; ?>

            <div class="review-form-section">
                <h3 class="section-title" style="font-size: 1.5rem;">Deixe sua Avaliação</h3>
                <form method="POST">
                    <div class="form-group">
                        <label for="nota" class="form-label">Sua Nota:</label>
                        <div class="rating-input">
                            <input type="radio" id="star1" name="nota" value="5"><label for="star1">★</label>
                            <input type="radio" id="star2" name="nota" value="4"><label for="star2">★</label>
                            <input type="radio" id="star3" name="nota" value="3"><label for="star3">★</label>
                            <input type="radio" id="star4" name="nota" value="2"><label for="star4">★</label>
                            <input type="radio" id="star5" name="nota" value="1"><label for="star5">★</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="comentario" class="form-label">Seu Comentário:</label>
                        <textarea id="comentario" name="comentario" class="form-textarea" rows="5" placeholder="Escreva sua avaliação aqui..."></textarea>
                    </div>
                    <button type="submit" name="enviar_avaliacao" class="btn-submit">
                        <span>⭐</span> Enviar Avaliação
                    </button>
                </form>
            </div>

            <?php if (empty($avaliacoes)): ?>
                <p style="color: var(--text-muted); text-align: center; margin-top: var(--spacing-xl);">Ainda não há avaliações para este produto. Seja o primeiro a avaliar!</p>
            <?php else: ?>
                <div class="review-list">
                    <?php foreach ($avaliacoes as $avaliacao): ?>
                        <div class="review-card">
                            <div class="review-header">
                                <span class="reviewer-name"><?php echo htmlspecialchars($avaliacao['usuario_nome']); ?></span>
                                <span class="review-date"><?php echo date('d/m/Y', strtotime($avaliacao['data_avaliacao'])); ?></span>
                            </div>
                            <div class="review-stars">
                                <?php for ($i = 0; $i < $avaliacao['nota']; $i++) echo '★'; ?>
                                <?php for ($i = 0; $i < (5 - $avaliacao['nota']); $i++) echo '☆'; ?>
                            </div>
                            <p class="review-comment"><?php echo nl2br(htmlspecialchars($avaliacao['comentario'])); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($produtos_relacionados)): ?>
            <div class="related-products-section">
                <h2 class="section-title">Você Também Pode Gostar</h2>
                <div class="related-products-grid products-grid">
                    <?php foreach ($produtos_relacionados as $produto_rel): ?>
                        <div class="product-card">
                            <div class="product-image">
                                <?php if (!empty($produto_rel['imagem_url'])): ?>
                                    <img src="<?php echo htmlspecialchars($produto_rel['imagem_url']); ?>" alt="<?php echo htmlspecialchars($produto_rel['nome']); ?>">
                                <?php else: ?>
                                    <span>Imagem do Produto</span>
                                <?php endif; ?>
                            </div>
                            <div class="product-category"><?php echo htmlspecialchars($produto_rel['categoria_nome']); ?></div>
                            <h3 class="product-title"><?php echo htmlspecialchars($produto_rel['nome']); ?></h3>
                            <div class="product-price">R$ <?php echo number_format($produto_rel['preco'], 2, ',', '.'); ?></div>
                            <p class="product-description"><?php echo htmlspecialchars($produto_rel['descricao_curta']); ?></p>
                            <a href="produto.php?id=<?php echo $produto_rel['id']; ?>" class="btn btn-primary">
                                Ver Detalhes
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 E-commerce Project. Todos os direitos reservados.</p>
            <p>Desenvolvido por <a href="https://dexo-mu.vercel.app/" class="dexo-credit">Dexo</a></p>
        </div>
    </footer>

    <!-- Chatbot -->
    <div class="chatbot-container">
        <button class="chatbot-toggle">💬</button>
        <div class="chatbot-window">
            <div class="chatbot-header">
                <h4>🤖 Assistente Virtual</h4>
                <button class="chatbot-close">×</button>
            </div>
            <div class="chatbot-messages">
                <div style="color: #ffffff; margin-bottom: 1rem;">
                    Olá! 👋 Posso ajudar com informações sobre este produto. Como posso ajudar?
                </div>
            </div>
            <div class="chatbot-input-container">
                <input type="text" class="chatbot-input" placeholder="Digite sua mensagem...">
                <button class="chatbot-send">➤</button>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function changeMainImage(thumbnail) {
            document.getElementById('mainProductImage').src = thumbnail.src;
            document.querySelectorAll('.thumbnail').forEach(img => img.classList.remove('active'));
            thumbnail.classList.add('active');
        }

        function changeQuantity(change) {
            const quantityInput = document.getElementById('quantity');
            let currentQuantity = parseInt(quantityInput.value);
            let newQuantity = currentQuantity + change;
            const maxQuantity = parseInt(quantityInput.max);

            if (newQuantity < 1) {
                newQuantity = 1;
            } else if (newQuantity > maxQuantity) {
                newQuantity = maxQuantity;
            }
            quantityInput.value = newQuantity;
        }

        function adicionarAoCarrinho(produtoId, quantidade = 1) {
            const formData = new FormData();
            formData.append('produto_id', produtoId);
            formData.append('quantidade', quantidade);

            fetch('adicionar_carrinho.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Sucesso!',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false,
                        background: 'rgba(255, 255, 255, 0.1)',
                        color: '#fff'
                    });

                    document.querySelector('#contador-carrinho').textContent = data.total_itens;
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: data.message,
                        background: 'rgba(255, 255, 255, 0.1)',
                        color: '#fff'
                    });
                }
            })
            .catch(() => {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro!',
                    text: 'Não foi possível processar sua solicitação.',
                    background: 'rgba(255, 255, 255, 0.1)',
                    color: '#fff'
                });
            });
    }

        function addToCart(productId) {
            const quantity = document.getElementById('quantity').value;
            
            fetch('adicionar_carrinho.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `produto_id=${productId}&quantidade=${quantity}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Sucesso!',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false,
                        background: 'rgba(255, 255, 255, 0.1)',
                        color: '#fff'
                    });
                    
                    // Atualizar o contador do carrinho
                    const cartIcon = document.querySelector('.cart-icon');
                    cartIcon.innerHTML = `<i class="fa-solid fa-cart-shopping"></i> Carrinho (${data.total_itens})`;
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro!',
                        text: data.message,
                        background: 'rgba(255, 255, 255, 0.1)',
                        color: '#fff'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro de conexão',
                    text: 'Não foi possível conectar ao servidor',
                    background: 'rgba(255, 255, 255, 0.1)',
                    color: '#fff'
                });
            });
        }

        // Animação de entrada dos elementos
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.animation = 'fadeInUp 0.6s ease-out forwards';
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });
        
        document.querySelectorAll('.product-detail-grid > div, .product-full-description-section, .reviews-section, .related-products-section').forEach((el, index) => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(30px)';
            el.style.animationDelay = `${index * 0.2}s`;
            observer.observe(el);
        });
    </script>
    <script src="animations.js"></script>
</body>
</html>

