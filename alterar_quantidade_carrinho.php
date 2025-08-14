<?php
session_start();
require_once 'db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuário não logado']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
$produto_id = $input['produto_id'] ?? 0;
$mudanca = $input['mudanca'] ?? 0;
$usuario_id = $_SESSION['usuario_id'];

try {
    $carrinho_id = obterCarrinhoUsuario($usuario_id);
    if (!$carrinho_id) {
        echo json_encode(['success' => false, 'message' => 'Carrinho não encontrado']);
        exit();
    }

    // Obter quantidade atual
    $stmt = $pdo->prepare("SELECT quantidade FROM carrinho_itens WHERE carrinho_id = ? AND produto_id = ?");
    $stmt->execute([$carrinho_id, $produto_id]);
    $item = $stmt->fetch();

    if (!$item) {
        echo json_encode(['success' => false, 'message' => 'Item não encontrado no carrinho']);
        exit();
    }

    $nova_quantidade = $item['quantidade'] + $mudanca;

    if ($nova_quantidade <= 0) {
        // Remover item se quantidade for 0 ou menor
        $stmt = $pdo->prepare("DELETE FROM carrinho_itens WHERE carrinho_id = ? AND produto_id = ?");
        $stmt->execute([$carrinho_id, $produto_id]);
    } else {
        // Atualizar quantidade
        $stmt = $pdo->prepare("UPDATE carrinho_itens SET quantidade = ? WHERE carrinho_id = ? AND produto_id = ?");
        $stmt->execute([$nova_quantidade, $carrinho_id, $produto_id]);
    }

    echo json_encode(['success' => true, 'message' => 'Quantidade alterada com sucesso']);

} catch (PDOException $e) {
    error_log("Erro ao alterar quantidade: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
}
?>
