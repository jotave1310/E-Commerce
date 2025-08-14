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
$usuario_id = $_SESSION['usuario_id'];

try {
    $carrinho_id = obterCarrinhoUsuario($usuario_id);
    if (!$carrinho_id) {
        echo json_encode(['success' => false, 'message' => 'Carrinho não encontrado']);
        exit();
    }

    $stmt = $pdo->prepare("DELETE FROM carrinho_itens WHERE carrinho_id = ? AND produto_id = ?");
    $stmt->execute([$carrinho_id, $produto_id]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Item removido com sucesso']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Item não encontrado']);
    }

} catch (PDOException $e) {
    error_log("Erro ao remover item: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
}
?>
