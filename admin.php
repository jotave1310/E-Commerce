<?php
session_start();
require_once 'db_connect.php';

// Verificar se o usuário é admin
if (!isset($_SESSION['usuario_id']) || !verificarAdmin($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$mensagem = '';
$tipo_mensagem = '';

// Obter categorias para o formulário
$categorias = obterCategorias();

// Obter produtos do banco de dados
$produtos = obterTodosProdutos();

// Processar formulário de adição de produto
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $categoria_id = intval($_POST['categoria'] ?? 0);
    $preco = floatval($_POST['preco'] ?? 0);
    $descricao_curta = trim($_POST['descricao_curta'] ?? '');
    $descricao_longa = trim($_POST['descricao_longa'] ?? '');
    $estoque = intval($_POST['estoque'] ?? 0);
    $destaque = isset($_POST['destaque']) ? 1 : 0;
    
    // Processar especificações técnicas
    $especificacoes = [];
    if (!empty($_POST['spec_key'])) {
        foreach ($_POST['spec_key'] as $index => $key) {
            $key = trim($key);
            $value = trim($_POST['spec_value'][$index] ?? '');
            
            if (!empty($key) && !empty($value)) {
                $especificacoes[$key] = $value;
            }
        }
    }
    $especificacoes_json = json_encode($especificacoes);
    
    // Validar dados
    if (empty($nome) || $categoria_id <= 0 || $preco <= 0 || empty($descricao_curta) || $estoque < 0) {
        $mensagem = "Por favor, preencha todos os campos obrigatórios corretamente.";
        $tipo_mensagem = "error";
    } else {
        try {
            // Primeiro adiciona o produto sem imagem para obter o ID
            $produto_id = adicionarProduto(
                $nome, 
                $descricao_curta, 
                $descricao_longa, 
                $preco, 
                $categoria_id, 
                $estoque, 
                '', // Imagem inicialmente vazia
                $especificacoes_json, // Especificações JSON
                $destaque
            );
            
            if ($produto_id) {
                // Array para armazenar caminhos das imagens
                $imagens = [];
                
                // Processar upload das 4 imagens
                for ($i = 1; $i <= 4; $i++) {
                    $field_name = "imagem_$i";
                    
                    if (isset($_FILES[$field_name]) && $_FILES[$field_name]['error'] !== UPLOAD_ERR_NO_FILE) {
                        $uploadResult = uploadImagemProduto($_FILES[$field_name], $produto_id, $nome, $i);
                        
                        if ($uploadResult['success']) {
                            $imagens[$i] = $uploadResult['filepath'];
                        } else {
                            $mensagem .= $uploadResult['message'] . " ";
                            $tipo_mensagem = "error";
                        }
                    }
                }
                
                // Se temos pelo menos 1 imagem (a capa)
                if (!empty($imagens)) {
                    // A primeira imagem é sempre a capa
                    $imagem_capa = $imagens[1] ?? '';
                    
                    // Criar JSON com todas as imagens
                    $imagens_json = json_encode($imagens);
                    
                    // Atualizar o produto com os caminhos das imagens
                    $stmt = $pdo->prepare("UPDATE produtos SET imagem_url = ?, imagens_extras = ? WHERE id = ?");
                    $stmt->execute([$imagem_capa, $imagens_json, $produto_id]);
                }
                
                if (empty($mensagem)) {
                    $mensagem = "Produto adicionado com sucesso!";
                    $tipo_mensagem = "success";
                    
                    // Recarregar produtos para mostrar o novo
                    $produtos = obterTodosProdutos();
                }
            } else {
                $mensagem = "Erro ao adicionar produto no banco de dados.";
                $tipo_mensagem = "error";
            }
        } catch (Exception $e) {
            $mensagem = "Erro: " . $e->getMessage();
            $tipo_mensagem = "error";
        }
    }
}

// Função para fazer upload de imagem e criar pasta
function uploadImagemProduto($arquivo, $produto_id, $nome_produto, $numero_imagem) {
    // Criar nome de pasta segura
    $nome_pasta = preg_replace('/[^a-z0-9]+/', '_', strtolower($nome_produto)) . $produto_id;
    $uploadDir = 'uploads/produtos/' . $nome_pasta . '/';
    
    // Criar diretório se não existir
    if (!file_exists($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            return ['success' => false, 'message' => 'Erro ao criar diretório para o produto.'];
        }
    }
    
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $maxSize = 5 * 1024 * 1024; // 5MB
    
    if (!isset($arquivo) || $arquivo['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Erro no upload do arquivo.'];
    }
    
    if (!in_array($arquivo['type'], $allowedTypes)) {
        return ['success' => false, 'message' => 'Tipo de arquivo não permitido. Use JPG, PNG, GIF ou WebP.'];
    }
    
    if ($arquivo['size'] > $maxSize) {
        return ['success' => false, 'message' => 'Arquivo muito grande. Máximo 5MB.'];
    }
    
    $extension = pathinfo($arquivo['name'], PATHINFO_EXTENSION);
    $filename = "img_{$numero_imagem}_" . uniqid() . '.' . $extension;
    $filepath = $uploadDir . $filename;
    
    if (move_uploaded_file($arquivo['tmp_name'], $filepath)) {
        return ['success' => true, 'filepath' => $filepath];
    } else {
        return ['success' => false, 'message' => 'Erro ao salvar o arquivo.'];
    }
}

// Obter estatísticas
$stats_produtos = obterEstatisticasProdutos();
$stats_usuarios = obterEstatisticasUsuarios();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo - E-commerce Project</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="components.css">
    <?php include 'header.php'; ?>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Montserrat:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        .admin-container {
            max-width: 1400px;
            margin: 2rem auto;
            padding: 0 var(--spacing-lg);
        }
        
        .admin-header {
            text-align: center;
            margin-bottom: var(--spacing-3xl);
            padding: var(--spacing-xl) 0;
        }
        
        .admin-title {
            font-family: 'Montserrat', sans-serif;
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: var(--spacing-md);
        }
        
        .admin-subtitle {
            color: var(--text-muted);
            font-size: 1.1rem;
        }
        
        .admin-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: var(--spacing-3xl);
            margin-bottom: var(--spacing-3xl);
        }
        
        .admin-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid var(--glass-border);
            border-radius: var(--border-radius-xl);
            padding: var(--spacing-3xl);
            transition: all var(--transition-normal);
        }
        
        .admin-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-xl);
            border-color: rgba(255, 255, 255, 0.2);
        }
        
        .card-title {
            font-family: 'Montserrat', sans-serif;
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-light);
            margin-bottom: var(--spacing-xl);
            display: flex;
            align-items: center;
            gap: var(--spacing-md);
        }
        
        .card-icon {
            font-size: 1.8rem;
        }
        
        .form-group {
            margin-bottom: var(--spacing-xl);
        }
        
        .form-label {
            display: block;
            color: var(--text-light);
            font-weight: 500;
            margin-bottom: var(--spacing-sm);
            font-size: 0.95rem;
        }
        
        .form-input, .form-textarea, .form-select {
            width: 100%;
            padding: var(--spacing-lg);
            background: rgba(255, 255, 255, 0.08);
            border: 2px solid var(--glass-border);
            border-radius: var(--border-radius-md);
            color: var(--text-light);
            font-size: 1rem;
            transition: all var(--transition-normal);
            backdrop-filter: blur(10px);
        }
        
        .form-input:focus, .form-textarea:focus, .form-select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(233, 69, 96, 0.2);
            background: rgba(255, 255, 255, 0.12);
            transform: translateY(-2px);
        }
        
        .form-input::placeholder, .form-textarea::placeholder {
            color: var(--text-dark);
        }
        
        .form-textarea {
            min-height: 120px;
            resize: vertical;
        }
        
        .file-upload-container {
            position: relative;
            overflow: hidden;
            display: inline-block;
            width: 100%;
        }
        
        .file-upload-input {
            position: absolute;
            left: -9999px;
        }
        
        .file-upload-label {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: var(--spacing-md);
            padding: var(--spacing-lg);
            background: rgba(255, 255, 255, 0.08);
            border: 2px dashed var(--glass-border);
            border-radius: var(--border-radius-md);
            color: var(--text-light);
            cursor: pointer;
            transition: all var(--transition-normal);
            backdrop-filter: blur(10px);
            min-height: 120px;
            flex-direction: column;
        }
        
        .file-upload-label:hover {
            border-color: var(--primary-color);
            background: rgba(255, 255, 255, 0.12);
            transform: translateY(-2px);
        }
        
        .file-upload-icon {
            font-size: 2rem;
            color: var(--primary-color);
        }
        
        .file-upload-text {
            text-align: center;
        }
        
        .file-upload-hint {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-top: var(--spacing-xs);
        }
        
        .btn-admin {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: white;
            border: none;
            padding: var(--spacing-lg) var(--spacing-2xl);
            border-radius: var(--border-radius-md);
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all var(--transition-normal);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            position: relative;
            overflow: hidden;
            width: 100%;
        }
        
        .btn-admin::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left var(--transition-slow);
        }
        
        .btn-admin:hover::before {
            left: 100%;
        }
        
        .btn-admin:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }
        
        .products-list {
            background: var(--glass-bg);
            backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid var(--glass-border);
            border-radius: var(--border-radius-xl);
            padding: var(--spacing-3xl);
            grid-column: 1 / -1;
        }
        
        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: var(--spacing-xl);
        }
        
        .products-table th,
        .products-table td {
            padding: var(--spacing-lg);
            text-align: left;
            border-bottom: 1px solid var(--glass-border);
        }
        
        .products-table th {
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-light);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }
        
        .products-table td {
            color: var(--text-muted);
        }
        
        .product-image-thumb {
            width: 50px;
            height: 50px;
            background: var(--card-bg);
            border-radius: var(--border-radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            color: var(--text-dark);
        }
        
        .price-cell {
            color: var(--primary-color);
            font-weight: 600;
        }
        
        .alert {
            padding: var(--spacing-lg);
            border-radius: var(--border-radius-md);
            margin-bottom: var(--spacing-xl);
            font-weight: 500;
            backdrop-filter: blur(10px);
        }
        
        .alert-success {
            background: rgba(40, 167, 69, 0.2);
            border: 1px solid rgba(40, 167, 69, 0.3);
            color: #28a745;
        }
        
        .alert-error {
            background: rgba(220, 53, 69, 0.2);
            border: 1px solid rgba(220, 53, 69, 0.3);
            color: #dc3545;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: var(--spacing-xl);
            margin-bottom: var(--spacing-3xl);
        }
        
        .stat-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid var(--glass-border);
            border-radius: var(--border-radius-lg);
            padding: var(--spacing-xl);
            text-align: center;
            transition: all var(--transition-normal);
        }
        
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color);
            display: block;
            margin-bottom: var(--spacing-sm);
        }
        
        .stat-label {
            color: var(--text-muted);
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        @media (max-width: 768px) {
            .admin-grid {
                grid-template-columns: 1fr;
                gap: var(--spacing-xl);
            }
            
            .admin-card {
                padding: var(--spacing-xl);
            }
            
            .products-table {
                font-size: 0.9rem;
            }
            
            .products-table th,
            .products-table td {
                padding: var(--spacing-md);
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
                        <li><a href="admin_new.php" class="active">Admin</a></li>
                        <li><a href="logout.php">Sair</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <main class="admin-container">
        <div class="admin-header">
            <h1 class="admin-title">Painel Administrativo</h1>
            <p class="admin-subtitle">Gerencie produtos e monitore o desempenho da sua loja</p>
        </div>

        <?php if (!empty($mensagem)): ?>
            <div class="alert alert-<?php echo $tipo_mensagem; ?>">
                <?php echo htmlspecialchars($mensagem); ?>
            </div>
        <?php endif; ?>

        <!-- Estatísticas -->
        <div class="stats-grid">
            <div class="stat-card">
                <span class="stat-number"><?= $stats_produtos['total_produtos'] ?? 0 ?></span>
                <span class="stat-label">Produtos Cadastrados</span>
            </div>
            <div class="stat-card">
                <span class="stat-number">R$ <?= number_format($stats_produtos['valor_total_estoque'] ?? 0, 2, ',', '.') ?></span>
                <span class="stat-label">Valor Total Estoque</span>
            </div>
            <div class="stat-card">
                <span class="stat-number"><?= $stats_produtos['produtos_destaque'] ?? 0 ?></span>
                <span class="stat-label">Produtos em Destaque</span>
            </div>
            <div class="stat-card">
                <span class="stat-number"><?= $stats_usuarios['total_usuarios'] ?? 0 ?></span>
                <span class="stat-label">Usuários Registrados</span>
            </div>
        </div>

        <div class="admin-grid">
            <!-- Formulário de Adicionar Produto -->
            <div class="admin-card">
                <h2 class="card-title">
                    <span class="card-icon">📦</span>
                    Adicionar Novo Produto
                </h2>
                
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="nome" class="form-label">Nome do Produto</label>
                        <input type="text" id="nome" name="nome" class="form-input" 
                               placeholder="Ex: Smartphone Samsung Galaxy S24" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="categoria" class="form-label">Categoria</label>
                        <select id="categoria" name="categoria" class="form-select" required>
                            <option value="">Selecione uma categoria</option>
                            <?php foreach ($categorias as $categoria): ?>
                                <option value="<?= $categoria['id'] ?>">
                                    <?= htmlspecialchars($categoria['nome']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="preco" class="form-label">Preço (R$)</label>
                        <input type="number" id="preco" name="preco" class="form-input" 
                               step="0.01" min="0" placeholder="0,00" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="estoque" class="form-label">Quantidade em Estoque</label>
                        <input type="number" id="estoque" name="estoque" class="form-input" 
                               min="0" placeholder="0" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="descricao_curta" class="form-label">Descrição Curta</label>
                        <textarea id="descricao_curta" name="descricao_curta" class="form-textarea" 
                                  placeholder="Descrição resumida para listagens..." required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="descricao_longa" class="form-label">Descrição Longa</label>
                        <textarea id="descricao_longa" name="descricao_longa" class="form-textarea" 
                                  placeholder="Descrição detalhada do produto..."></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Especificações Técnicas</label>
                        <div id="especificacoes-container">
                            <div class="spec-row" style="display: flex; gap: 10px; margin-bottom: 10px;">
                                <input type="text" name="spec_key[]" placeholder="Nome (ex: Memória RAM)" 
                                       class="form-input" style="flex: 1;">
                                <input type="text" name="spec_value[]" placeholder="Valor (ex: 8GB)" 
                                       class="form-input" style="flex: 1;">
                                <button type="button" class="btn btn-danger remove-spec" style="padding: 10px;">
                                    🗑️
                                </button>
                            </div>
                        </div>
                        <button type="button" id="add-spec" class="btn btn-secondary" style="margin-top: 10px;">
                            ➕ Adicionar Especificação
                        </button>
                    </div>
                    
                    <!-- Substituir o campo de imagem atual por: -->
                    <div class="form-group">
                        <label class="form-label">Imagens do Produto</label>
                        
                        <!-- Imagem Capa (obrigatória) -->
                        <div class="file-upload-container" style="margin-bottom: 15px;">
                            <label class="form-label">Imagem Capa (Principal)</label>
                            <input type="file" name="imagens[]" class="file-upload-input" required accept="image/*">
                            <label for="imagens[]" class="file-upload-label">
                                <span class="file-upload-icon">📷</span>
                                <div class="file-upload-text">
                                    <strong>Clique para selecionar a imagem capa</strong>
                                    <div class="file-upload-hint">JPG, PNG, GIF ou WebP (máx. 5MB)</div>
                                </div>
                            </label>
                        </div>
                        
                        <!-- Imagens Extras (opcionais) -->
                        <div class="file-upload-container" style="margin-bottom: 15px;">
                            <label class="form-label">Imagem Extra 1</label>
                            <input type="file" name="imagens[]" class="file-upload-input" accept="image/*">
                            <label for="imagens[]" class="file-upload-label">
                                <span class="file-upload-icon">📷</span>
                                <div class="file-upload-text">
                                    <strong>Clique para selecionar imagem extra</strong>
                                    <div class="file-upload-hint">Opcional</div>
                                </div>
                            </label>
                        </div>

                         <!-- Imagens Extras (opcionais) -->
                        <div class="file-upload-container" style="margin-bottom: 15px;">
                            <label class="form-label">Imagem Extra 2</label>
                            <input type="file" name="imagens[]" class="file-upload-input" accept="image/*">
                            <label for="imagens[]" class="file-upload-label">
                                <span class="file-upload-icon">📷</span>
                                <div class="file-upload-text">
                                    <strong>Clique para selecionar imagem extra</strong>
                                    <div class="file-upload-hint">Opcional</div>
                                </div>
                            </label>
                        </div>

                         <!-- Imagens Extras (opcionais) -->
                        <div class="file-upload-container" style="margin-bottom: 15px;">
                            <label class="form-label">Imagem Extra 3</label>
                            <input type="file" name="imagens[]" class="file-upload-input" accept="image/*">
                            <label for="imagens[]" class="file-upload-label">
                                <span class="file-upload-icon">📷</span>
                                <div class="file-upload-text">
                                    <strong>Clique para selecionar imagem extra</strong>
                                    <div class="file-upload-hint">Opcional</div>
                                </div>
                            </label>
                        </div>
                        
                        <!-- Repetir para Imagem Extra 2 e 3 -->
                    </div>
                    
                    <div class="form-group" style="display: flex; align-items: center; gap: var(--spacing-md);">
                        <input type="checkbox" id="destaque" name="destaque" value="1">
                        <label for="destaque" class="form-label" style="margin-bottom: 0;">Destacar este produto</label>
                    </div>
                    
                    <button type="submit" class="btn-admin">
                        Adicionar Produto
                    </button>
                </form>
            </div>

            <!-- Painel de Controle -->
            <div class="admin-card">
                <h2 class="card-title">
                    <span class="card-icon">⚙️</span>
                    Painel de Controle
                </h2>
                
                <div style="display: flex; flex-direction: column; gap: var(--spacing-lg);">
                    <button class="btn btn-secondary" onclick="window.location.href='index.php'">
                        🏠 Ver Site Principal
                    </button>
                    <button class="btn btn-secondary" onclick="window.location.href='produtos.php'">
                        📋 Ver Todos Produtos
                    </button>
                    <button class="btn btn-secondary" onclick="window.location.href='gerenciar_usuarios.php'">
                        👥 Gerenciar Usuários
                    </button>
                    <button class="btn btn-secondary" onclick="window.location.href='relatorios.php'">
                        📊 Relatórios
                    </button>
                    <button class="btn btn-secondary" onclick="window.location.href='pedidos.php'">
                        🛒 Pedidos
                    </button>
                </div>
                
                <div style="margin-top: var(--spacing-xl); padding-top: var(--spacing-xl); border-top: 1px solid var(--glass-border);">
                    <h3 style="color: var(--text-light); margin-bottom: var(--spacing-md);">Ações Rápidas</h3>
                    <div style="display: flex; flex-direction: column; gap: var(--spacing-sm);">
                        <button class="btn btn-primary" style="font-size: 0.9rem; padding: var(--spacing-sm) var(--spacing-md);">
                            🔄 Sincronizar Estoque
                        </button>
                        <button class="btn btn-primary" style="font-size: 0.9rem; padding: var(--spacing-sm) var(--spacing-md);">
                            📧 Enviar Newsletter
                        </button>
                        <button class="btn btn-primary" style="font-size: 0.9rem; padding: var(--spacing-sm) var(--spacing-md);">
                            🧹 Limpar Cache
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de Produtos -->
        <div class="products-list">
            <h2 class="card-title">
                <span class="card-icon">📋</span>
                Produtos Cadastrados (<?= count($produtos) ?>)
            </h2>
            
            <table class="products-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Imagem</th>
                        <th>Nome</th>
                        <th>Categoria</th>
                        <th>Preço</th>
                        <th>Estoque</th>
                        <th>Destaque</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($produtos as $produto): ?>
                        <tr>
                            <td><?= $produto['id'] ?></td>
                            <td>
                                <div class="product-image-thumb">
                                    <?php if (!empty($produto['imagem_url'])): ?>
                                        <img src="<?= htmlspecialchars($produto['imagem_url']) ?>" 
                                             alt="<?= htmlspecialchars($produto['nome']) ?>"
                                             style="width: 100%; height: 100%; object-fit: cover; border-radius: var(--border-radius-sm);">
                                    <?php else: ?>
                                        📷
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($produto['nome']) ?></td>
                            <td>
                                <?php 
                                    $categoria_nome = '';
                                    foreach ($categorias as $cat) {
                                        if ($cat['id'] == $produto['categoria_id']) {
                                            $categoria_nome = htmlspecialchars($cat['nome']);
                                            break;
                                        }
                                    }
                                    echo $categoria_nome;
                                ?>
                            </td>
                            <td class="price-cell">R$ <?= number_format($produto['preco'], 2, ',', '.') ?></td>
                            <td><?= $produto['estoque'] ?></td>
                            <td><?= $produto['destaque'] ? '⭐ Sim' : 'Não' ?></td>
                            <td>
                                <button class="btn btn-secondary" 
                                        onclick="window.location.href='editar_produto.php?id=<?= $produto['id'] ?>'"
                                        style="font-size: 0.8rem; padding: var(--spacing-xs) var(--spacing-sm);">
                                    ✏️ Editar
                                </button>
                                <button class="btn btn-danger" 
                                        onclick="if(confirm('Tem certeza que deseja remover este produto?')) window.location.href='remover_produto.php?id=<?= $produto['id'] ?>'"
                                        style="font-size: 0.8rem; padding: var(--spacing-xs) var(--spacing-sm);">
                                    🗑️ Remover
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 E-commerce Project. Todos os direitos reservados.</p>
            <p>Desenvolvido por <a href="https://dexo-mu.vercel.app/" class="dexo-credit">Dexo</a></p>
        </div>
    </footer>

    <script>
        // Atualizar label do upload quando arquivo for selecionado
        document.getElementById('imagem').addEventListener('change', function(e) {
            const label = document.querySelector('.file-upload-label');
            const file = e.target.files[0];
            
            if (file) {
                label.innerHTML = `
                    <span class="file-upload-icon">✅</span>
                    <div class="file-upload-text">
                        <strong>${file.name}</strong>
                        <div class="file-upload-hint">Arquivo selecionado (${(file.size / 1024 / 1024).toFixed(2)} MB)</div>
                    </div>
                `;
                label.style.borderColor = 'var(--success-color)';
                label.style.background = 'rgba(40, 167, 69, 0.1)';
            }
        });

        // Validação em tempo real do preço
        document.getElementById('preco').addEventListener('input', function(e) {
            const value = parseFloat(e.target.value);
            if (value < 0) {
                e.target.style.borderColor = 'var(--danger-color)';
            } else {
                e.target.style.borderColor = '';
            }
        });

        // Contador de caracteres para descrição
        const descricaoTextarea = document.getElementById('descricao_longa');
        const maxLength = 2000;
        
        descricaoTextarea.addEventListener('input', function(e) {
            const length = e.target.value.length;
            const remaining = maxLength - length;
            
            if (!document.querySelector('.char-counter')) {
                const counter = document.createElement('div');
                counter.className = 'char-counter';
                counter.style.cssText = `
                    font-size: 0.8rem;
                    color: var(--text-muted);
                    text-align: right;
                    margin-top: var(--spacing-xs);
                `;
                e.target.parentNode.appendChild(counter);
            }
            
            const counter = document.querySelector('.char-counter');
            counter.textContent = `${length}/${maxLength} caracteres`;
            
            if (remaining < 50) {
                counter.style.color = 'var(--warning-color)';
            } else if (remaining < 0) {
                counter.style.color = 'var(--danger-color)';
            } else {
                counter.style.color = 'var(--text-muted)';
            }
        });

        // Adicionar e remover campos de especificações
        document.addEventListener('DOMContentLoaded', function() {
            // Adicionar nova linha de especificação
            document.getElementById('add-spec').addEventListener('click', function() {
                const container = document.getElementById('especificacoes-container');
                const newRow = document.createElement('div');
                newRow.className = 'spec-row';
                newRow.style.cssText = 'display: flex; gap: 10px; margin-bottom: 10px;';
                newRow.innerHTML = `
                    <input type="text" name="spec_key[]" placeholder="Nome (ex: Memória RAM)" 
                           class="form-input" style="flex: 1;">
                    <input type="text" name="spec_value[]" placeholder="Valor (ex: 8GB)" 
                           class="form-input" style="flex: 1;">
                    <button type="button" class="btn btn-danger remove-spec" style="padding: 10px;">
                        🗑️
                    </button>
                `;
                container.appendChild(newRow);
            });

            // Remover linha de especificação
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-spec')) {
                    e.target.closest('.spec-row').remove();
                }
            });
        });
    </script>
    
    <script src="animations.js"></script>
</body>
</html>

