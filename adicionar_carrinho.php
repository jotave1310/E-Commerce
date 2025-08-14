<?php
session_start();
require_once 'db_connect.php';

$response = ['success' => false, 'message' => 'Erro desconhecido', 'total_itens' => 0];

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    $response['message'] = 'Você precisa estar logado para adicionar itens ao carrinho.';
    echo json_encode($response);
    exit();
}

// Verificar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Método de requisição inválido.';
    echo json_encode($response);
    exit();
}

// Obter dados do POST
$produtoId = isset($_POST['produto_id']) ? (int)$_POST['produto_id'] : 0;
$quantidade = isset($_POST['quantidade']) ? (int)$_POST['quantidade'] : 1;

// Validar ID do produto
if ($produtoId <= 0) {
    $response['message'] = 'ID do produto inválido.';
    echo json_encode($response);
    exit();
}

// Obter ID do usuário
$usuarioId = $_SESSION['usuario_id'];

// Obter/ criar carrinho
$carrinhoId = obterCarrinhoUsuario($usuarioId);

if (!$carrinhoId) {
    $response['message'] = 'Erro ao acessar seu carrinho.';
    echo json_encode($response);
    exit();
}

// Adicionar item ao carrinho (versão simplificada e direta)
try {
    $pdo->beginTransaction();

    // Verificar se o item já está no carrinho
    $stmt = $pdo->prepare("SELECT quantidade FROM carrinho_itens 
                          WHERE carrinho_id = :carrinho_id AND produto_id = :produto_id");
    $stmt->execute([
        ':carrinho_id' => $carrinhoId,
        ':produto_id' => $produtoId
    ]);
    $item = $stmt->fetch();

    if ($item) {
        // Atualizar quantidade existente
        $novaQuantidade = $item['quantidade'] + $quantidade;
        $stmt = $pdo->prepare("UPDATE carrinho_itens SET quantidade = :quantidade 
                              WHERE carrinho_id = :carrinho_id AND produto_id = :produto_id");
        $stmt->execute([
            ':quantidade' => $novaQuantidade,
            ':carrinho_id' => $carrinhoId,
            ':produto_id' => $produtoId
        ]);
    } else {
        // Inserir novo item
        $stmt = $pdo->prepare("INSERT INTO carrinho_itens (carrinho_id, produto_id, quantidade) 
                              VALUES (:carrinho_id, :produto_id, :quantidade)");
        $stmt->execute([
            ':carrinho_id' => $carrinhoId,
            ':produto_id' => $produtoId,
            ':quantidade' => $quantidade
        ]);
    }

    // Calcular novo total de itens
    $stmt = $pdo->prepare("SELECT SUM(quantidade) as total_itens FROM carrinho_itens 
                          WHERE carrinho_id = :carrinho_id");
    $stmt->execute([':carrinho_id' => $carrinhoId]);
    $totalItens = (int)$stmt->fetch()['total_itens'];

    $pdo->commit();

    $response['success'] = true;
    $response['message'] = 'Produto adicionado ao carrinho com sucesso!';
    $response['total_itens'] = $totalItens;

    // Atualizar sessão com o novo total
    $_SESSION['total_carrinho'] = $totalItens;

} catch (PDOException $e) {
    $pdo->rollBack();
    $response['message'] = 'Erro no banco de dados: ' . $e->getMessage();
}

echo json_encode($response);
?>