<?php
session_start();
require_once 'db_connect.php';

// Verificar se está logado
if (!isset($_SESSION['usuario_id'])) {
    die('Você precisa estar logado para ver este debug.');
}

$usuarioId = $_SESSION['usuario_id'];
echo "<h2>Debug do Carrinho - Usuário ID: $usuarioId</h2>";

// 1. Verificar se existe carrinho para o usuário
echo "<h3>1. Verificando carrinho do usuário:</h3>";
$carrinhoId = obterCarrinhoUsuario($usuarioId);
echo "Carrinho ID: " . ($carrinhoId ? $carrinhoId : 'NÃO ENCONTRADO') . "<br>";

if (!$carrinhoId) {
    die('Erro: Carrinho não encontrado para o usuário.');
}

// 2. Verificar itens na tabela carrinho_itens
echo "<h3>2. Itens na tabela carrinho_itens:</h3>";
try {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM carrinho_itens WHERE carrinho_id = ?");
    $stmt->execute([$carrinhoId]);
    $itensRaw = $stmt->fetchAll();
    
    if (empty($itensRaw)) {
        echo "NENHUM ITEM encontrado na tabela carrinho_itens para carrinho_id = $carrinhoId<br>";
    } else {
        echo "Itens encontrados:<br>";
        foreach ($itensRaw as $item) {
            echo "- Produto ID: {$item['produto_id']}, Quantidade: {$item['quantidade']}<br>";
        }
    }
} catch (Exception $e) {
    echo "Erro ao consultar carrinho_itens: " . $e->getMessage() . "<br>";
}

// 3. Verificar se os produtos existem e estão ativos
echo "<h3>3. Verificando produtos:</h3>";
if (!empty($itensRaw)) {
    foreach ($itensRaw as $item) {
        $produto = obterProdutoPorId($item['produto_id']);
        if ($produto) {
            echo "- Produto ID {$item['produto_id']}: {$produto['nome']} (Ativo: " . ($produto['ativo'] ? 'SIM' : 'NÃO') . ")<br>";
        } else {
            echo "- Produto ID {$item['produto_id']}: NÃO ENCONTRADO ou INATIVO<br>";
        }
    }
}

// 4. Testar a função obterItensCarrinho diretamente
echo "<h3>4. Testando função obterItensCarrinho():</h3>";
$itensCarrinho = obterItensCarrinho($carrinhoId);
if (empty($itensCarrinho)) {
    echo "NENHUM ITEM retornado pela função obterItensCarrinho()<br>";
} else {
    echo "Itens retornados pela função:<br>";
    foreach ($itensCarrinho as $item) {
        echo "- {$item['nome']} (Quantidade: {$item['quantidade']}, Preço: R$ {$item['preco']})<br>";
    }
}

// 5. Executar a query da função manualmente para debug
echo "<h3>5. Executando query manualmente:</h3>";
try {
    $stmt = $pdo->prepare("
        SELECT ci.produto_id, ci.quantidade, p.nome, p.preco, p.imagem_url, c.nome as categoria 
        FROM carrinho_itens ci
        JOIN produtos p ON ci.produto_id = p.id
        LEFT JOIN categorias c ON p.categoria_id = c.id
        WHERE ci.carrinho_id = ? AND p.ativo = 1
    ");
    $stmt->execute([$carrinhoId]);
    $resultados = $stmt->fetchAll();
    
    if (empty($resultados)) {
        echo "NENHUM RESULTADO da query manual<br>";
        
        // Verificar se é problema do JOIN
        echo "<h4>Verificando JOIN com produtos:</h4>";
        $stmt2 = $pdo->prepare("
            SELECT ci.*, p.nome, p.ativo 
            FROM carrinho_itens ci
            LEFT JOIN produtos p ON ci.produto_id = p.id
            WHERE ci.carrinho_id = ?
        ");
        $stmt2->execute([$carrinhoId]);
        $debug = $stmt2->fetchAll();
        
        foreach ($debug as $d) {
            echo "- Carrinho item: produto_id={$d['produto_id']}, produto_nome=" . ($d['nome'] ?? 'NULL') . ", produto_ativo=" . ($d['ativo'] ?? 'NULL') . "<br>";
        }
    } else {
        echo "Resultados da query manual:<br>";
        foreach ($resultados as $item) {
            echo "- {$item['nome']} (Quantidade: {$item['quantidade']})<br>";
        }
    }
} catch (Exception $e) {
    echo "Erro na query manual: " . $e->getMessage() . "<br>";
}

echo "<h3>6. Informações da sessão:</h3>";
echo "Usuário logado: " . ($_SESSION['usuario_id'] ?? 'NÃO') . "<br>";
echo "Nome do usuário: " . ($_SESSION['usuario_nome'] ?? 'NÃO DEFINIDO') . "<br>";
?>
