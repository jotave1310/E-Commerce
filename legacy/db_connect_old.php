<?php
/**
 * Arquivo de conexão com o banco de dados MySQL
 * E-commerce Project
 */

// Configurações do banco de dados
$db_host = 'localhost';
$db_name = 'ecommerce_db';
$db_user = 'ecommerce_user';
$db_pass = 'password';

try {
    // Criar conexão PDO
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    
    // Configurar PDO para lançar exceções em caso de erro
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Configurar PDO para retornar arrays associativos
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}

/**
 * Função para obter um produto pelo ID
 */
function obterProduto($id) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT p.*, c.nome as categoria_nome FROM produtos p 
                              LEFT JOIN categorias c ON p.categoria_id = c.id 
                              WHERE p.id = ?");
        $stmt->execute([$id]);
        $produto = $stmt->fetch();
        
        if ($produto) {
            // Manter compatibilidade com o código existente
            $produto['categoria'] = $produto['categoria_nome'];
        }
        
        return $produto;
    } catch (PDOException $e) {
        error_log("Erro ao obter produto: " . $e->getMessage());
        return null;
    }
}

/**
 * Função para obter todos os produtos
 */
function obterTodosProdutos() {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT p.*, c.nome as categoria_nome FROM produtos p 
                              LEFT JOIN categorias c ON p.categoria_id = c.id 
                              ORDER BY p.id");
        $stmt->execute();
        $produtos = $stmt->fetchAll();
        
        // Manter compatibilidade com o código existente
        foreach ($produtos as &$produto) {
            $produto['categoria'] = $produto['categoria_nome'];
        }
        
        return $produtos;
    } catch (PDOException $e) {
        error_log("Erro ao obter produtos: " . $e->getMessage());
        return [];
    }
}

/**
 * Função para obter produtos em destaque (primeiros 4)
 */
function obterProdutosDestaque($limite = 4) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT p.*, c.nome as categoria_nome FROM produtos p 
                              LEFT JOIN categorias c ON p.categoria_id = c.id 
                              ORDER BY p.id LIMIT :limite");
        $stmt->bindValue(':limite', (int)$limite, PDO::PARAM_INT);
        $stmt->execute();
        $produtos = $stmt->fetchAll();
        
        // Manter compatibilidade com o código existente
        foreach ($produtos as &$produto) {
            $produto['categoria'] = $produto['categoria_nome'];
        }
        
        return $produtos;
    } catch (PDOException $e) {
        error_log("Erro ao obter produtos em destaque: " . $e->getMessage());
        return [];
    }
}

/**
 * Função para calcular o total do carrinho
 */
function calcularTotalCarrinho($carrinho) {
    global $pdo;
    $total = 0;
    
    if (empty($carrinho)) {
        return $total;
    }
    
    try {
        $ids = array_keys($carrinho);
        $placeholders = str_repeat('?,', count($ids) - 1) . '?';
        
        $stmt = $pdo->prepare("SELECT id, preco FROM produtos WHERE id IN ($placeholders)");
        $stmt->execute($ids);
        $produtos = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        
        foreach ($carrinho as $produtoId => $quantidade) {
            if (isset($produtos[$produtoId])) {
                $total += $produtos[$produtoId] * $quantidade;
            }
        }
        
        return $total;
    } catch (PDOException $e) {
        error_log("Erro ao calcular total do carrinho: " . $e->getMessage());
        return 0;
    }
}

/**
 * Função para formatar preço (mantida para compatibilidade)
 */
function formatarPreco($preco) {
    return 'R$ ' . number_format($preco, 2, ',', '.');
}

/**
 * Função para salvar pedido no banco de dados
 */
function salvarPedido($dadosCliente, $carrinho, $total) {
    global $pdo;
    
    try {
        $pdo->beginTransaction();
        
        // Inserir pedido
        $stmt = $pdo->prepare("INSERT INTO pedidos (usuario_id, status, total, endereco_entrega, cidade_entrega, cep_entrega) 
                              VALUES (NULL, 'Pendente', ?, ?, ?, ?)");
            $stmt->execute([
                $total,
                $dadosCliente["endereco"] ?? null,
                $dadosCliente["cidade"] ?? null,
                $dadosCliente["cep"] ?? null
            ]);
        
        $pedidoId = $pdo->lastInsertId();
        
        // Inserir itens do pedido
        $stmt = $pdo->prepare("INSERT INTO itens_pedido (pedido_id, produto_id, quantidade, preco_unitario) 
                              VALUES (?, ?, ?, ?)");
        
        foreach ($carrinho as $produtoId => $quantidade) {
            $produto = obterProduto($produtoId);
            if ($produto) {
                $stmt->execute([$pedidoId, $produtoId, $quantidade, $produto['preco']]);
            }
        }
        
        $pdo->commit();
        return $pedidoId;
        
    } catch (PDOException $e) {
        $pdo->rollBack();
        error_log("Erro ao salvar pedido: " . $e->getMessage());
        return false;
    }
}

/**
 * Função para obter todas as categorias
 */
function obterCategorias() {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM categorias ORDER BY nome");
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Erro ao obter categorias: " . $e->getMessage());
        return [];
    }
}

/**
 * Função para adicionar um novo produto
 */
function adicionarProduto($nome, $categoria, $preco, $descricao) {
    global $pdo;
    
    try {
        // Verificar se a categoria existe, se não, criar
        $stmt = $pdo->prepare("SELECT id FROM categorias WHERE nome = ?");
        $stmt->execute([$categoria]);
        $categoriaExistente = $stmt->fetch();
        
        if (!$categoriaExistente) {
            // Criar nova categoria
            $stmt = $pdo->prepare("INSERT INTO categorias (nome, descricao) VALUES (?, ?)");
            $stmt->execute([$categoria, "Categoria: " . $categoria]);
            $categoriaId = $pdo->lastInsertId();
        } else {
            $categoriaId = $categoriaExistente['id'];
        }
        
        // Inserir o produto
        $stmt = $pdo->prepare("INSERT INTO produtos (nome, categoria_id, preco, descricao, estoque, ativo) 
                              VALUES (?, ?, ?, ?, 100, 1)");
        $stmt->execute([$nome, $categoriaId, $preco, $descricao]);
        
        return true;
        
    } catch (PDOException $e) {
        error_log("Erro ao adicionar produto: " . $e->getMessage());
        return false;
    }
}
?>


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
            return $usuario;
        }
        
        return false;
        
    } catch (PDOException $e) {
        error_log("Erro ao autenticar usuário: " . $e->getMessage());
        return false;
    }
}

/**
 * Função para criar novo usuário
 */
function criarUsuario($nome, $email, $senha, $telefone = null) {
    global $pdo;
    
    try {
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, telefone, data_registro) 
                              VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$nome, $email, $senhaHash, $telefone]);
        
        return $pdo->lastInsertId();
        
    } catch (PDOException $e) {
        error_log("Erro ao criar usuário: " . $e->getMessage());
        return false;
    }
}

/**
 * Função para verificar se email já existe
 */
function verificarEmailExiste($email) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch() !== false;
        
    } catch (PDOException $e) {
        error_log("Erro ao verificar email: " . $e->getMessage());
        return false;
    }
}

/**
 * Função para atualizar último login
 */
function atualizarUltimoLogin($usuarioId) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("UPDATE usuarios SET ultimo_login = NOW() WHERE id = ?");
        $stmt->execute([$usuarioId]);
        return true;
        
    } catch (PDOException $e) {
        error_log("Erro ao atualizar último login: " . $e->getMessage());
        return false;
    }
}

/**
 * Função para obter dados do usuário
 */
function obterUsuario($usuarioId) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT id, nome, email, telefone, endereco, cidade, cep, 
                              data_registro, ultimo_login, tipo_usuario 
                              FROM usuarios WHERE id = ? AND ativo = 1");
        $stmt->execute([$usuarioId]);
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


?>

