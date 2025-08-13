<?php
session_start();
require_once 'db_connect.php';

$response = ['success' => false, 'message' => 'Erro desconhecido', 'total_itens' => 0];

if (!isset($_SESSION['usuario_id'])) {
    $response['message'] = 'Você precisa estar logado para adicionar itens ao carrinho.';
    echo json_encode($response);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $produtoId = isset($_POST['produto_id']) ? intval($_POST['produto_id']) : 0;
    $quantidade = isset($_POST['quantidade']) ? intval($_POST['quantidade']) : 1;

    if ($produtoId <= 0) {
        $response['message'] = 'ID do produto inválido.';
        echo json_encode($response);
        exit();
    }

    // Verificar se o produto existe e está ativo
    $produto = obterProdutoPorId($produtoId);
    if (!$produto || $produto['ativo'] != 1) {
        $response['message'] = 'Produto não encontrado ou indisponível.';
        echo json_encode($response);
        exit();
    }

    $usuarioId = $_SESSION['usuario_id'];
    $carrinhoId = obterCarrinhoUsuario($usuarioId);

    if (adicionarAoCarrinho($carrinhoId, $produtoId, $quantidade)) {
        // Obter o total de itens no carrinho para atualizar o contador
        $itensCarrinho = obterItensCarrinho($carrinhoId);
        $totalItens = 0;
        foreach ($itensCarrinho as $item) {
            $totalItens += $item['quantidade'];
        }

        $response['success'] = true;
        $response['message'] = 'Produto adicionado ao carrinho com sucesso!';
        $response['total_itens'] = $totalItens;
    } else {
        $response['message'] = 'Erro ao adicionar o produto ao carrinho.';
    }
} else {
    $response['message'] = 'Método de requisição inválido.';
}

echo json_encode($response);
?>