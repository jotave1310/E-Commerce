<?php
require_once 'db_connect.php';

echo "<h1>Teste de Conexão com Banco de Dados</h1>";

try {
    // Testar conexão
    echo "<p>✅ Conexão com banco estabelecida com sucesso!</p>";
    
    // Testar função de obter produtos
    $produtos = obterProdutosDestaque();
    echo "<p>📦 Produtos em destaque encontrados: " . count($produtos) . "</p>";
    
    // Testar função de autenticação
    $usuario = autenticarUsuario('admin@teste.com', '123456');
    if ($usuario) {
        echo "<p>🔑 Login de admin funcionando: " . $usuario['nome'] . "</p>";
    } else {
        echo "<p>❌ Erro no login de admin</p>";
    }
    
    $usuario2 = autenticarUsuario('user@teste.com', '123456');
    if ($usuario2) {
        echo "<p>🔑 Login de usuário funcionando: " . $usuario2['nome'] . "</p>";
    } else {
        echo "<p>❌ Erro no login de usuário</p>";
    }
    
    // Testar categorias
    $categorias = obterCategorias();
    echo "<p>📂 Categorias encontradas: " . count($categorias) . "</p>";
    
    echo "<h2>Estrutura do Banco:</h2>";
    echo "<ul>";
    foreach ($categorias as $cat) {
        echo "<li>" . $cat['nome'] . "</li>";
    }
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<p>❌ Erro: " . $e->getMessage() . "</p>";
}
?>

