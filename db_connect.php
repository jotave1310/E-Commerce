<?php
// Configurações do banco de dados
$host = 'localhost';
$dbname = 'ecommerce_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}

/**
 * Função para obter todos os produtos
 */
function obterTodosProdutos() {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            SELECT p.*, c.nome as categoria_nome 
            FROM produtos p 
            LEFT JOIN categorias c ON p.categoria_id = c.id 
            WHERE p.ativo = 1 
            ORDER BY p.nome
        ");
        $stmt->execute();
        return $stmt->fetchAll();
        
    } catch (PDOException $e) {
        error_log("Erro ao obter produtos: " . $e->getMessage());
        return [];
    }
}

/**
 * Função para obter produtos em destaque
 */
function obterProdutosDestaque($limite = 6) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            SELECT p.*, c.nome as categoria_nome 
            FROM produtos p 
            LEFT JOIN categorias c ON p.categoria_id = c.id 
            WHERE p.ativo = 1 AND p.destaque = 1 
            ORDER BY p.data_criacao DESC 
            LIMIT ?
        ");
        $stmt->execute([$limite]);
        return $stmt->fetchAll();
        
    } catch (PDOException $e) {
        error_log("Erro ao obter produtos em destaque: " . $e->getMessage());
        return [];
    }
}

/**
 * Função para obter produto por ID
 */
function obterProdutoPorId($id) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            SELECT p.*, c.nome as categoria_nome 
            FROM produtos p 
            LEFT JOIN categorias c ON p.categoria_id = c.id 
            WHERE p.id = ? AND p.ativo = 1
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
        
    } catch (PDOException $e) {
        error_log("Erro ao obter produto: " . $e->getMessage());
        return false;
    }
}

/**
 * Função para obter categorias
 */
function obterCategorias() {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM categorias WHERE ativo = 1 ORDER BY nome");
        $stmt->execute();
        return $stmt->fetchAll();
        
    } catch (PDOException $e) {
        error_log("Erro ao obter categorias: " . $e->getMessage());
        return [];
    }
}

/**
 * Função para adicionar produto
 */
function adicionarProduto($nome, $descricao_curta, $descricao_longa, $preco, $categoria_id, $estoque, $imagem_url = '', $especificacoes_json = '', $destaque = 0) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO produtos (nome, descricao_curta, descricao_longa, preco, categoria_id, estoque, imagem_url, especificacoes_json, destaque, ativo, data_criacao) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1, NOW())
        ");
        $stmt->execute([$nome, $descricao_curta, $descricao_longa, $preco, $categoria_id, $estoque, $imagem_url, $especificacoes_json, $destaque]);
        return $pdo->lastInsertId();
        
    } catch (PDOException $e) {
        error_log("Erro ao adicionar produto: " . $e->getMessage());
        return false;
    }
}

/**
 * Função para obter produtos relacionados
 */
function obterProdutosRelacionados($categoria_id, $produto_id_excluir, $limite = 4) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            SELECT p.*, c.nome as categoria_nome 
            FROM produtos p 
            LEFT JOIN categorias c ON p.categoria_id = c.id 
            WHERE p.categoria_id = ? AND p.id != ? AND p.ativo = 1 
            ORDER BY RAND() 
            LIMIT ?
        ");
        $stmt->execute([$categoria_id, $produto_id_excluir, $limite]);
        return $stmt->fetchAll();
        
    } catch (PDOException $e) {
        error_log("Erro ao obter produtos relacionados: " . $e->getMessage());
        return [];
    }
}

/**
 * Função para obter avaliações de um produto
 */
function obterAvaliacoesProduto($produto_id) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            SELECT a.*, u.nome as usuario_nome 
            FROM avaliacoes a 
            LEFT JOIN usuarios u ON a.usuario_id = u.id 
            WHERE a.produto_id = ? AND a.ativo = 1 
            ORDER BY a.data_avaliacao DESC
        ");
        $stmt->execute([$produto_id]);
        return $stmt->fetchAll();
        
    } catch (PDOException $e) {
        error_log("Erro ao obter avaliações: " . $e->getMessage());
        return [];
    }
}

/**
 * Função para adicionar avaliação
 */
function adicionarAvaliacao($produto_id, $usuario_id, $nota, $comentario) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO avaliacoes (produto_id, usuario_id, nota, comentario, ativo, data_avaliacao) 
            VALUES (?, ?, ?, ?, 1, NOW())
        ");
        $stmt->execute([$produto_id, $usuario_id, $nota, $comentario]);
        return true;
        
    } catch (PDOException $e) {
        error_log("Erro ao adicionar avaliação: " . $e->getMessage());
        return false;
    }
}

/**
 * Função para registrar usuário
 */
function registrarUsuario($nome, $email, $senha, $telefone = '', $endereco = '', $cidade = '', $cep = '') {
    global $pdo;
    
    try {
        // Verificar se email já existe
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            return false; // Email já existe
        }
        
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("
            INSERT INTO usuarios (nome, email, senha, telefone, endereco, cidade, cep, tipo_usuario, ativo, data_registro) 
            VALUES (?, ?, ?, ?, ?, ?, ?, 'cliente', 1, NOW())
        ");
        $stmt->execute([$nome, $email, $senhaHash, $telefone, $endereco, $cidade, $cep]);
        return $pdo->lastInsertId();
        
    } catch (PDOException $e) {
        error_log("Erro ao registrar usuário: " . $e->getMessage());
        return false;
    }
}

/**
 * Função para autenticar usuário
 */
function autenticarUsuario($email, $senha) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ? AND ativo = 1");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();
        
        if ($usuario && password_verify($senha, $usuario['senha'])) {
            // Atualizar último login
            $stmt = $pdo->prepare("UPDATE usuarios SET ultimo_login = NOW() WHERE id = ?");
            $stmt->execute([$usuario['id']]);
            
            return $usuario;
        }
        
        return false;
        
    } catch (PDOException $e) {
        error_log("Erro ao autenticar usuário: " . $e->getMessage());
        return false;
    }
}

/**
 * Função para obter usuário por ID
 */
function obterUsuario($id) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ? AND ativo = 1");
        $stmt->execute([$id]);
        return $stmt->fetch();
        
    } catch (PDOException $e) {
        error_log("Erro ao obter usuário: " . $e->getMessage());
        return false;
    }
}

/**
 * Função para atualizar perfil do usuário
 */
function atualizarPerfilUsuario($usuarioId, $nome, $telefone, $endereco, $cidade, $cep) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("UPDATE usuarios SET nome = ?, telefone = ?, endereco = ?, 
                              cidade = ?, cep = ? WHERE id = ?");
        $stmt->execute([$nome, $telefone, $endereco, $cidade, $cep, $usuarioId]);
        return true;
        
    } catch (PDOException $e) {
        error_log("Erro ao atualizar perfil: " . $e->getMessage());
        return false;
    }
}

/**
 * Função para alterar senha do usuário
 */
function alterarSenhaUsuario($usuarioId, $senhaAtual, $novaSenha) {
    global $pdo;
    
    try {
        // Verificar senha atual
        $stmt = $pdo->prepare("SELECT senha FROM usuarios WHERE id = ?");
        $stmt->execute([$usuarioId]);
        $usuario = $stmt->fetch();
        
        if (!$usuario || !password_verify($senhaAtual, $usuario['senha'])) {
            return false;
        }
        
        // Atualizar senha
        $novaSenhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE usuarios SET senha = ? WHERE id = ?");
        $stmt->execute([$novaSenhaHash, $usuarioId]);
        
        return true;
        
    } catch (PDOException $e) {
        error_log("Erro ao alterar senha: " . $e->getMessage());
        return false;
    }
}

/**
 * Função para obter estatísticas de usuários (admin)
 */
function obterEstatisticasUsuarios() {
    global $pdo;
    
    try {
        $stats = [];
        
        // Total de usuários
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM usuarios WHERE ativo = 1");
        $stmt->execute();
        $stats['total_usuarios'] = $stmt->fetch()['total'];
        
        // Usuários registrados hoje
        $stmt = $pdo->prepare("SELECT COUNT(*) as hoje FROM usuarios 
                              WHERE DATE(data_registro) = CURDATE() AND ativo = 1");
        $stmt->execute();
        $stats['usuarios_hoje'] = $stmt->fetch()['hoje'];
        
        // Usuários ativos (logaram nos últimos 30 dias)
        $stmt = $pdo->prepare("SELECT COUNT(*) as ativos FROM usuarios 
                              WHERE ultimo_login >= DATE_SUB(NOW(), INTERVAL 30 DAY) AND ativo = 1");
        $stmt->execute();
        $stats['usuarios_ativos'] = $stmt->fetch()['ativos'];
        
        return $stats;
        
    } catch (PDOException $e) {
        error_log("Erro ao obter estatísticas: " . $e->getMessage());
        return [];
    }
}

/**
 * Função para verificar se usuário é admin
 */
function verificarAdmin($usuarioId) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT tipo_usuario FROM usuarios WHERE id = ? AND ativo = 1");
        $stmt->execute([$usuarioId]);
        $usuario = $stmt->fetch();
        
        return $usuario && $usuario['tipo_usuario'] === 'admin';
        
    } catch (PDOException $e) {
        error_log("Erro ao verificar admin: " . $e->getMessage());
        return false;
    }
}

/**
 * Função para listar todos os usuários (admin)
 */
function listarUsuarios($limite = 50, $offset = 0) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT id, nome, email, telefone, data_registro, ultimo_login, 
                              tipo_usuario, ativo FROM usuarios 
                              ORDER BY data_registro DESC LIMIT ? OFFSET ?");
        $stmt->execute([$limite, $offset]);
        return $stmt->fetchAll();
        
    } catch (PDOException $e) {
        error_log("Erro ao listar usuários: " . $e->getMessage());
        return [];
    }
}

/**
 * Função para desativar usuário (admin)
 */
function desativarUsuario($usuarioId) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("UPDATE usuarios SET ativo = 0 WHERE id = ?");
        $stmt->execute([$usuarioId]);
        return true;
        
    } catch (PDOException $e) {
        error_log("Erro ao desativar usuário: " . $e->getMessage());
        return false;
    }
}

/**
 * Função para reativar usuário (admin)
 */
function reativarUsuario($usuarioId) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("UPDATE usuarios SET ativo = 1 WHERE id = ?");
        $stmt->execute([$usuarioId]);
        return true;
        
    } catch (PDOException $e) {
        error_log("Erro ao reativar usuário: " . $e->getMessage());
        return false;
    }
}

/**
 * Função para obter pedidos do usuário
 */
function obterPedidosUsuario($usuarioId, $limite = 20) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM pedidos WHERE usuario_id = ? 
                              ORDER BY data_pedido DESC LIMIT ?");
        $stmt->execute([$usuarioId, $limite]);
        return $stmt->fetchAll();
        
    } catch (PDOException $e) {
        error_log("Erro ao obter pedidos do usuário: " . $e->getMessage());
        return [];
    }
}

/**
 * Função para obter estatísticas de produtos (admin)
 */
function obterEstatisticasProdutos() {
    global $pdo;
    
    try {
        $stats = [];
        
        // Total de produtos
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM produtos WHERE ativo = 1");
        $stmt->execute();
        $stats['total_produtos'] = $stmt->fetch()['total'];
        
        // Valor total do estoque
        $stmt = $pdo->prepare("SELECT SUM(preco * estoque) as valor_total FROM produtos WHERE ativo = 1");
        $stmt->execute();
        $stats['valor_total_estoque'] = $stmt->fetch()['valor_total'] ?? 0;
        
        // Produtos em destaque
        $stmt = $pdo->prepare("SELECT COUNT(*) as destaque FROM produtos WHERE ativo = 1 AND destaque = 1");
        $stmt->execute();
        $stats['produtos_destaque'] = $stmt->fetch()['destaque'];
        
        // Produtos com estoque baixo (menos de 10)
        $stmt = $pdo->prepare("SELECT COUNT(*) as baixo_estoque FROM produtos WHERE ativo = 1 AND estoque < 10");
        $stmt->execute();
        $stats['produtos_baixo_estoque'] = $stmt->fetch()['baixo_estoque'];
        
        return $stats;
        
    } catch (PDOException $e) {
        error_log("Erro ao obter estatísticas de produtos: " . $e->getMessage());
        return [];
    }
}

/**
 * Função para atualizar produto
 */
function atualizarProduto($id, $nome, $descricao_curta, $descricao_longa, $preco, $categoria_id, $estoque, $imagem_url = '', $especificacoes_json = '', $destaque = 0) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            UPDATE produtos 
            SET nome = ?, descricao_curta = ?, descricao_longa = ?, preco = ?, categoria_id = ?, 
                estoque = ?, imagem_url = ?, especificacoes_json = ?, destaque = ?, data_atualizacao = NOW()
            WHERE id = ?
        ");
        $stmt->execute([$nome, $descricao_curta, $descricao_longa, $preco, $categoria_id, $estoque, $imagem_url, $especificacoes_json, $destaque, $id]);
        return true;
        
    } catch (PDOException $e) {
        error_log("Erro ao atualizar produto: " . $e->getMessage());
        return false;
    }
}

/**
 * Função para remover produto (desativar)
 */
function removerProduto($id) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("UPDATE produtos SET ativo = 0 WHERE id = ?");
        $stmt->execute([$id]);
        return true;
        
    } catch (PDOException $e) {
        error_log("Erro ao remover produto: " . $e->getMessage());
        return false;
    }
}

/**
 * Função para adicionar categoria
 */
function adicionarCategoria($nome, $descricao = '') {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("INSERT INTO categorias (nome, descricao, ativo, data_criacao) VALUES (?, ?, 1, NOW())");
        $stmt->execute([$nome, $descricao]);
        return $pdo->lastInsertId();
        
    } catch (PDOException $e) {
        error_log("Erro ao adicionar categoria: " . $e->getMessage());
        return false;
    }
}

/**
 * Função para buscar produtos
 */
function buscarProdutos($termo = '', $categoria_id = null, $preco_min = null, $preco_max = null) {
    global $pdo;
    
    try {
        $sql = "
            SELECT p.*, c.nome as categoria_nome 
            FROM produtos p 
            LEFT JOIN categorias c ON p.categoria_id = c.id 
            WHERE p.ativo = 1
        ";
        
        $conditions = [];
        $params = [];
        
        // Adicionar condições conforme os parâmetros
        if (!empty($termo)) {
            $conditions[] = "(p.nome LIKE ? OR p.descricao_curta LIKE ? OR p.descricao_longa LIKE ?)";
            $params[] = "%$termo%";
            $params[] = "%$termo%";
            $params[] = "%$termo%";
        }
        
        if (!empty($categoria_id)) {
            $conditions[] = "p.categoria_id = ?";
            $params[] = $categoria_id;
        }
        
        if ($preco_min !== null && $preco_min >= 0) {
            $conditions[] = "p.preco >= ?";
            $params[] = $preco_min;
        }
        
        if ($preco_max !== null && $preco_max > 0) {
            $conditions[] = "p.preco <= ?";
            $params[] = $preco_max;
        }
        
        // Combinar condições
        if (!empty($conditions)) {
            $sql .= " AND " . implode(" AND ", $conditions);
        }
        
        $sql .= " ORDER BY p.nome";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
        
    } catch (PDOException $e) {
        error_log("Erro ao buscar produtos: " . $e->getMessage());
        return [];
    }
}

/**
 * Obter carrinho do usuário (cria se não existir)
 */
function obterCarrinhoUsuario($usuarioId) {
    global $pdo;
    
    try {
        // Verificar se já existe carrinho
        $stmt = $pdo->prepare("SELECT id FROM carrinhos WHERE usuario_id = ?");
        $stmt->execute([$usuarioId]);
        $carrinho = $stmt->fetch();
        
        if (!$carrinho) {
            // Criar novo carrinho
            $stmt = $pdo->prepare("INSERT INTO carrinhos (usuario_id) VALUES (?)");
            $stmt->execute([$usuarioId]);
            return $pdo->lastInsertId();
        }
        
        return $carrinho['id'];
        
    } catch (PDOException $e) {
        error_log("Erro ao obter carrinho: " . $e->getMessage());
        return false;
    }
}

/**
 * Adicionar item ao carrinho
 */
function adicionarAoCarrinho($carrinhoId, $produtoId, $quantidade = 1) {
    global $pdo;
    
    try {
        // Verificar se o item já está no carrinho
        $stmt = $pdo->prepare("SELECT quantidade FROM carrinho_itens 
                              WHERE carrinho_id = ? AND produto_id = ?");
        $stmt->execute([$carrinhoId, $produtoId]);
        $item = $stmt->fetch();
        
        if ($item) {
            // Atualizar quantidade
            $novaQuantidade = $item['quantidade'] + $quantidade;
            $stmt = $pdo->prepare("UPDATE carrinho_itens SET quantidade = ? 
                                  WHERE carrinho_id = ? AND produto_id = ?");
            $stmt->execute([$novaQuantidade, $carrinhoId, $produtoId]);
        } else {
            // Adicionar novo item
            $stmt = $pdo->prepare("INSERT INTO carrinho_itens (carrinho_id, produto_id, quantidade) 
                                  VALUES (?, ?, ?)");
            $stmt->execute([$carrinhoId, $produtoId, $quantidade]);
        }
        
        return true;
        
    } catch (PDOException $e) {
        error_log("Erro ao adicionar ao carrinho: " . $e->getMessage());
        return false;
    }
}

/**
 * Remover item do carrinho
 */
function removerDoCarrinho($carrinhoId, $produtoId) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("DELETE FROM carrinho_itens 
                              WHERE carrinho_id = ? AND produto_id = ?");
        return $stmt->execute([$carrinhoId, $produtoId]);
        
    } catch (PDOException $e) {
        error_log("Erro ao remover do carrinho: " . $e->getMessage());
        return false;
    }
}

/**
 * Atualizar quantidade no carrinho
 */
function atualizarQuantidadeCarrinho($carrinhoId, $produtoId, $quantidade) {
    global $pdo;
    
    try {
        if ($quantidade <= 0) {
            return removerDoCarrinho($carrinhoId, $produtoId);
        }
        
        $stmt = $pdo->prepare("UPDATE carrinho_itens SET quantidade = ? 
                              WHERE carrinho_id = ? AND produto_id = ?");
        return $stmt->execute([$quantidade, $carrinhoId, $produtoId]);
        
    } catch (PDOException $e) {
        error_log("Erro ao atualizar carrinho: " . $e->getMessage());
        return false;
    }
}

/**
 * Limpar carrinho
 */
function limparCarrinho($carrinhoId) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("DELETE FROM carrinho_itens WHERE carrinho_id = ?");
        return $stmt->execute([$carrinhoId]);
        
    } catch (PDOException $e) {
        error_log("Erro ao limpar carrinho: " . $e->getMessage());
        return false;
    }
}

/**
 * Obter itens do carrinho
 */
function obterItensCarrinho($carrinhoId) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            SELECT ci.produto_id, ci.quantidade, p.nome, p.preco, p.imagem_url, c.nome as categoria 
            FROM carrinho_itens ci
            JOIN produtos p ON ci.produto_id = p.id
            LEFT JOIN categorias c ON p.categoria_id = c.id
            WHERE ci.carrinho_id = ?
        ");
        $stmt->execute([$carrinhoId]);
        return $stmt->fetchAll();
        
    } catch (PDOException $e) {
        error_log("Erro ao obter itens do carrinho: " . $e->getMessage());
        return [];
    }
}

/**
 * Calcular total do carrinho
 */
function calcularTotalCarrinho($carrinhoId) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            SELECT SUM(p.preco * ci.quantidade) as total
            FROM carrinho_itens ci
            JOIN produtos p ON ci.produto_id = p.id
            WHERE ci.carrinho_id = ?
        ");
        $stmt->execute([$carrinhoId]);
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
        
    } catch (PDOException $e) {
        error_log("Erro ao calcular total do carrinho: " . $e->getMessage());
        return 0;
    }
}

/**
 * Obter usuário por ID
 */
function obterUsuarioPorId($id) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Erro ao obter usuário: " . $e->getMessage());
        return false;
    }
}
?>

