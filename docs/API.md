# Documentação da API - E-commerce Project

Esta documentação descreve as funções, estruturas de dados e fluxos de trabalho do E-commerce Project, agora integrando um banco de dados MySQL.

## 📋 Índice

- [Estrutura de Dados](#estrutura-de-dados)
- [Funções Principais](#funções-principais)
- [Fluxo de Sessões](#fluxo-de-sessões)
- [Validações](#validações)
- [Tratamento de Erros](#tratamento-de-erros)
- [Exemplos de Uso](#exemplos-de-uso)
- [Considerações de Segurança](#considerações-de-segurança)
- [Métricas e Logs](#métricas-e-logs)

## 🗃️ Estrutura de Dados

### Produto (Tabela `produtos`)

Estrutura de um produto conforme armazenado no banco de dados:

| Coluna        | Tipo de Dados     | Descrição                                   |
|---------------|-------------------|---------------------------------------------|
| `id`          | INT               | Identificador único do produto              |
| `nome`        | VARCHAR(255)      | Nome do produto                             |
| `descricao`   | TEXT              | Descrição detalhada do produto              |
| `preco`       | DECIMAL(10, 2)    | Preço do produto                            |
| `estoque`     | INT               | Quantidade em estoque                       |
| `categoria_id`| INT               | ID da categoria do produto (FK para `categorias`) |
| `imagem_url`  | VARCHAR(255)      | URL da imagem do produto (opcional)         |
| `data_criacao`| TIMESTAMP         | Data e hora de criação do registro          |
| `data_atualizacao`| TIMESTAMP     | Última atualização do registro              |

### Carrinho de Compras (Sessão PHP)

O carrinho ainda é armazenado na sessão PHP, mas agora os detalhes dos produtos são buscados do banco de dados.

```php
$_SESSION["cart"] = [
    produto_id => quantidade,
    // Exemplo:
    1 => 2,  // Produto ID 1, quantidade 2
    3 => 1,  // Produto ID 3, quantidade 1
];
```

### Dados do Cliente (Checkout)

Os dados do cliente são coletados via formulário e usados para criar um pedido na tabela `pedidos`.

| Campo         | Tipo de Dados     | Descrição                               |
|---------------|-------------------|-----------------------------------------|
| `nome`        | string            | Nome completo do cliente                |
| `email`       | string            | Email válido do cliente                 |
| `telefone`    | string            | Telefone de contato                     |
| `endereco`    | string            | Endereço de entrega                     |
| `cidade`      | string            | Cidade                                  |
| `cep`         | string            | CEP                                     |

### Pedido (Tabela `pedidos`)

| Coluna        | Tipo de Dados     | Descrição                                   |
|---------------|-------------------|---------------------------------------------|
| `id`          | INT               | Identificador único do pedido               |
| `usuario_id`  | INT               | ID do usuário que fez o pedido (FK para `usuarios`, NULL para convidados) |
| `data_pedido` | TIMESTAMP         | Data e hora do pedido                       |
| `status`      | VARCHAR(50)       | Status do pedido (ex: Pendente, Concluído)  |
| `total`       | DECIMAL(10, 2)    | Valor total do pedido                       |
| `endereco_entrega`| VARCHAR(255)  | Endereço de entrega do pedido               |
| `cidade_entrega`| VARCHAR(100)    | Cidade de entrega                           |
| `cep_entrega` | VARCHAR(10)       | CEP de entrega                              |

### Item do Pedido (Tabela `itens_pedido`)

| Coluna        | Tipo de Dados     | Descrição                                   |
|---------------|-------------------|---------------------------------------------|
| `id`          | INT               | Identificador único do item do pedido       |
| `pedido_id`   | INT               | ID do pedido ao qual o item pertence (FK para `pedidos`) |
| `produto_id`  | INT               | ID do produto incluído no pedido (FK para `produtos`) |
| `quantidade`  | INT               | Quantidade do produto no pedido             |
| `preco_unitario`| DECIMAL(10, 2)  | Preço do produto no momento da compra       |

## 🔧 Funções Principais (em `db_connect.php`)

### `obterProduto($id)`

Busca um produto específico pelo ID no banco de dados.

**Parâmetros**:
- `$id` (int): ID do produto a ser buscado

**Retorno**:
- `array|null`: Array com dados do produto ou `null` se não encontrado

**Exemplo**:
```php
$produto = obterProduto(1);
if ($produto) {
    echo $produto["nome"]; // "Smartphone Samsung Galaxy"
} else {
    echo "Produto não encontrado";
}
```

**Implementação (simplificada)**:
```php
function obterProduto($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT p.*, c.nome as categoria_nome FROM produtos p LEFT JOIN categorias c ON p.categoria_id = c.id WHERE p.id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}
```

### `obterTodosProdutos()`

Retorna todos os produtos disponíveis no banco de dados.

**Parâmetros**: Nenhum

**Retorno**:
- `array`: Array de arrays, onde cada sub-array representa um produto.

**Exemplo**:
```php
$todosProdutos = obterTodosProdutos();
foreach ($todosProdutos as $produto) {
    echo $produto["nome"] . "<br>";
}
```

**Implementação (simplificada)**:
```php
function obterTodosProdutos() {
    global $pdo;
    $stmt = $pdo->prepare("SELECT p.*, c.nome as categoria_nome FROM produtos p LEFT JOIN categorias c ON p.categoria_id = c.id ORDER BY p.id");
    $stmt->execute();
    return $stmt->fetchAll();
}
```

### `obterProdutosDestaque($limite = 4)`

Retorna um número limitado de produtos para exibição em destaque.

**Parâmetros**:
- `$limite` (int): Número máximo de produtos a serem retornados (padrão: 4).

**Retorno**:
- `array`: Array de arrays, representando os produtos em destaque.

**Exemplo**:
```php
$produtosDestaque = obterProdutosDestaque(2);
foreach ($produtosDestaque as $produto) {
    echo $produto["nome"] . "<br>";
}
```

**Implementação (simplificada)**:
```php
function obterProdutosDestaque($limite = 4) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT p.*, c.nome as categoria_nome FROM produtos p LEFT JOIN categorias c ON p.categoria_id = c.id ORDER BY p.id LIMIT ?");
    $stmt->execute([$limite]);
    return $stmt->fetchAll();
}
```

### `calcularTotalCarrinho($carrinho)`

Calcula o valor total dos produtos no carrinho, buscando os preços atualizados do banco de dados.

**Parâmetros**:
- `$carrinho` (array): Array do carrinho no formato `[produto_id => quantidade]`

**Retorno**:
- `float`: Valor total do carrinho.

**Exemplo**:
```php
$carrinho = [1 => 2, 3 => 1];
$total = calcularTotalCarrinho($carrinho);
echo formatarPreco($total);
```

**Implementação (simplificada)**:
```php
function calcularTotalCarrinho($carrinho) {
    global $pdo;
    $total = 0;
    if (empty($carrinho)) return $total;
    $ids = array_keys($carrinho);
    $placeholders = str_repeat("?, ", count($ids) - 1) . "?";
    $stmt = $pdo->prepare("SELECT id, preco FROM produtos WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $produtos = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    foreach ($carrinho as $produtoId => $quantidade) {
        if (isset($produtos[$produtoId])) {
            $total += $produtos[$produtoId] * $quantidade;
        }
    }
    return $total;
}
```

### `formatarPreco($preco)`

Formata um valor numérico para o padrão monetário brasileiro.

**Parâmetros**:
- `$preco` (float): Valor a ser formatado

**Retorno**:
- `string`: Preço formatado com "R$" e separadores brasileiros

**Exemplo**:
```php
echo formatarPreco(1234.56); // "R$ 1.234,56"
```

**Implementação**:
```php
function formatarPreco($preco) {
    return 'R$ ' . number_format($preco, 2, ',', '.');
}
```

### `salvarPedido($dadosCliente, $carrinho, $total)`

Salva um novo pedido e seus itens no banco de dados.

**Parâmetros**:
- `$dadosCliente` (array): Array associativo com os dados do cliente (nome, email, telefone, endereco, cidade, cep).
- `$carrinho` (array): Array do carrinho no formato `[produto_id => quantidade]`.
- `$total` (float): Valor total do pedido.

**Retorno**:
- `int|false`: ID do pedido inserido ou `false` em caso de erro.

**Exemplo**:
```php
$dados = ["nome" => "Teste", "email" => "teste@teste.com", ...];
$carrinho = [1 => 1];
$total = 100.00;
$pedidoId = salvarPedido($dados, $carrinho, $total);
if ($pedidoId) {
    echo "Pedido #" . $pedidoId . " salvo com sucesso!";
}
```

**Implementação (simplificada)**:
```php
function salvarPedido($dadosCliente, $carrinho, $total) {
    global $pdo;
    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("INSERT INTO pedidos (usuario_id, status, total, endereco_entrega, cidade_entrega, cep_entrega) VALUES (NULL, 'Pendente', ?, ?, ?, ?)");
        $stmt->execute([$total, $dadosCliente["endereco"], $dadosCliente["cidade"], $dadosCliente["cep"]]);
        $pedidoId = $pdo->lastInsertId();
        $stmt = $pdo->prepare("INSERT INTO itens_pedido (pedido_id, produto_id, quantidade, preco_unitario) VALUES (?, ?, ?, ?)");
        foreach ($carrinho as $produtoId => $quantidade) {
            $produto = obterProduto($produtoId);
            if ($produto) {
                $stmt->execute([$pedidoId, $produtoId, $quantidade, $produto["preco"]]);
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
```

## 🔄 Fluxo de Sessões

### Inicialização da Sessão

Todas as páginas iniciam com:

```php
session_start();

// Inicializar carrinho se não existir
if (!isset($_SESSION["cart"])) {
    $_SESSION["cart"] = array();
}
```

### Adição de Produto ao Carrinho

**Fluxo**:
1. Usuário clica em "Adicionar ao Carrinho"
2. Formulário POST é enviado com `produto_id` e `quantidade`
3. Sistema valida os dados (verificando existência do produto no DB via `obterProduto`)
4. Produto é adicionado ou quantidade é incrementada na sessão.
5. Usuário recebe feedback visual.

**Código (simplificado)**:
```php
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["adicionar_carrinho"])) {
    $produtoId = (int)$_POST["produto_id"];
    $quantidade = isset($_POST["quantidade"]) ? (int)$_POST["quantidade"] : 1;
    
    if ($quantidade > 0 && obterProduto($produtoId)) { // Valida com o DB
        if (isset($_SESSION["cart"][$produtoId])) {
            $_SESSION["cart"][$produtoId] += $quantidade;
        } else {
            $_SESSION["cart"][$produtoId] = $quantidade;
        }
        $mensagem = "Produto adicionado ao carrinho com sucesso!";
    }
}
```

### Remoção de Produto do Carrinho

```php
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["remover_item"])) {
    $produtoId = (int)$_POST["produto_id"];
    unset($_SESSION["cart"][$produtoId]);
    $mensagem = "Item removido do carrinho!";
}
```

### Atualização de Quantidade

```php
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["atualizar_quantidade"])) {
    $produtoId = (int)$_POST["produto_id"];
    $novaQuantidade = (int)$_POST["quantidade"];
    
    if ($novaQuantidade > 0) {
        $_SESSION["cart"][$produtoId] = $novaQuantidade;
    } else {
        unset($_SESSION["cart"][$produtoId]);
    }
}
```

## ✅ Validações

### Validação de Produto

```php
function validarProduto($produtoId) {
    return is_numeric($produtoId) && obterProduto((int)$produtoId) !== null;
}
```

### Validação de Quantidade

```php
function validarQuantidade($quantidade) {
    return is_numeric($quantidade) && (int)$quantidade > 0 && (int)$quantidade <= 10;
}
```

### Validação de Dados do Cliente

```php
function validarDadosCliente($dados) {
    $erros = [];
    
    if (empty(trim($dados["nome"]))) {
        $erros[] = "Nome é obrigatório";
    }
    
    if (empty(trim($dados["email"])) || !filter_var($dados["email"], FILTER_VALIDATE_EMAIL)) {
        $erros[] = "Email válido é obrigatório";
    }
    
    if (empty(trim($dados["telefone"]))) {
        $erros[] = "Telefone é obrigatório";
    }
    
    if (empty(trim($dados["endereco"]))) {
        $erros[] = "Endereço é obrigatório";
    }
    
    if (empty(trim($dados["cidade"]))) {
        $erros[] = "Cidade é obrigatória";
    }
    
    if (empty(trim($dados["cep"]))) {
        $erros[] = "CEP é obrigatório";
    }
    
    return $erros;
}
```

## 🚨 Tratamento de Erros

### Redirecionamentos de Segurança

```php
// Verificar se produto existe antes de exibir detalhes
if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: index.php");
    exit;
}

$produto = obterProduto((int)$_GET["id"]);
if (!$produto) {
    header("Location: index.php");
    exit;
}
```

### Validação de Carrinho Vazio

```php
// Redirecionar se carrinho estiver vazio no checkout
if (empty($_SESSION["cart"])) {
    header("Location: carrinho.php");
    exit;
}
```

### Sanitização de Dados

```php
// Sempre usar htmlspecialchars para output
echo htmlspecialchars($produto["nome"]);

// Sempre usar trim para input
$nome = trim($_POST["nome"] ?? "");
```

## 📝 Exemplos de Uso

### Exemplo 1: Listar Produtos com Preços Formatados (index.php, produtos.php)

```php
require_once 'config.php';

// Para index.php (produtos em destaque)
$produtosDestaque = obterProdutosDestaque(4);
foreach ($produtosDestaque as $produto) {
    echo "<div class='produto'>";
    echo "<h3>" . htmlspecialchars($produto["nome"]) . "</h3>";
    echo "<p>Preço: " . formatarPreco($produto["preco"]) . "</p>";
    echo "</div>";
}

// Para produtos.php (todos os produtos)
$todosProdutos = obterTodosProdutos();
foreach ($todosProdutos as $produto) {
    echo "<div class='produto'>";
    echo "<h3>" . htmlspecialchars($produto["nome"]) . "</h3>";
    echo "<p>Preço: " . formatarPreco($produto["preco"]) . "</p>";
    echo "</div>";
}
```

### Exemplo 2: Exibir Conteúdo do Carrinho (carrinho.php)

```php
session_start();
require_once 'config.php';

if (!empty($_SESSION["cart"])) {
    $total = 0;
    
    foreach ($_SESSION["cart"] as $produtoId => $quantidade) {
        $produto = obterProduto($produtoId); // Busca do DB
        if ($produto) {
            $subtotal = $produto["preco"] * $quantidade;
            $total += $subtotal;
            
            echo "<tr>";
            echo "<td>" . htmlspecialchars($produto["nome"]) . "</td>";
            echo "<td>" . formatarPreco($produto["preco"]) . "</td>";
            echo "<td>" . $quantidade . "</td>";
            echo "<td>" . formatarPreco($subtotal) . "</td>";
            echo "</tr>";
        }
    }
    
    echo "<tr><td colspan='3'><strong>Total:</strong></td>";
    echo "<td><strong>" . formatarPreco($total) . "</strong></td></tr>";
} else {
    echo "<p>Carrinho vazio</p>";
}
```

### Exemplo 3: Processar Checkout (checkout.php)

```php
session_start();
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["finalizar_compra"])) {
    $dadosCliente = [
        "nome" => trim($_POST["nome"] ?? ""),
        "email" => trim($_POST["email"] ?? ""),
        "telefone" => trim($_POST["telefone"] ?? ""),
        "endereco" => trim($_POST["endereco"] ?? ""),
        "cidade" => trim($_POST["cidade"] ?? ""),
        "cep" => trim($_POST["cep"] ?? "")
    ];
    
    $erros = validarDadosCliente($dadosCliente);
    
    if (empty($erros)) {
        $total = calcularTotalCarrinho($_SESSION["cart"]);
        $pedidoId = salvarPedido($dadosCliente, $_SESSION["cart"], $total); // Salva no DB
        
        if ($pedidoId) {
            $_SESSION["cart"] = array();
            $numeroPedido = "PED" . str_pad($pedidoId, 6, "0", STR_PAD_LEFT);
            header("Location: sucesso.php?pedido=" . $numeroPedido);
            exit;
        } else {
            $mensagem = "Erro ao processar pedido. Tente novamente.";
        }
    }
}
```

## 🔐 Considerações de Segurança

### Sanitização de Input

```php
// Sempre validar e sanitizar dados de entrada
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
$email = filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL);
```

### Prevenção de XSS

```php
// Sempre escapar output HTML
echo htmlspecialchars($userInput, ENT_QUOTES, "UTF-8");
```

### Validação de Sessão

```php
// Regenerar ID de sessão periodicamente
if (!isset($_SESSION["last_regeneration"])) {
    session_regenerate_id(true);
    $_SESSION["last_regeneration"] = time();
} elseif (time() - $_SESSION["last_regeneration"] > 300) {
    session_regenerate_id(true);
    $_SESSION["last_regeneration"] = time();
}
```

## 📊 Métricas e Logs

### Log de Ações do Carrinho

```php
function logCarrinhoAction($action, $produtoId, $quantidade = null) {
    $logEntry = date("Y-m-d H:i:s") . " - $action - Produto: $produtoId";
    if ($quantidade) {
        $logEntry .= " - Quantidade: $quantidade";
    }
    $logEntry .= " - Session: " . session_id() . "\n";
    
    file_put_contents("logs/carrinho.log", $logEntry, FILE_APPEND | LOCK_EX);
}

// Uso:
logCarrinhoAction("ADD", $produtoId, $quantidade);
logCarrinhoAction("REMOVE", $produtoId);
```

### Estatísticas Básicas

```php
function getCarrinhoStats() {
    if (empty($_SESSION["cart"])) {
        return ["itens" => 0, "total" => 0, "produtos_unicos" => 0];
    }
    
    $stats = [
        "itens" => array_sum($_SESSION["cart"]),
        "total" => calcularTotalCarrinho($_SESSION["cart"]),
        "produtos_unicos" => count($_SESSION["cart"])
    ];
    
    return $stats;
}
```

---

**Última atualização**: Agosto 2025

