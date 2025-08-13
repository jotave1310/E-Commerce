<?php
header('Content-Type: application/json');

// Receber a mensagem do usuário
$input = json_decode(file_get_contents('php://input'), true);
$mensagem = strtolower(trim($input['mensagem'] ?? ''));

// Respostas do chatbot
$respostas = [
    // Saudações
    'oi' => 'Olá! 👋 Bem-vindo à nossa loja de tecnologia! Como posso ajudá-lo hoje?',
    'ola' => 'Olá! 👋 Bem-vindo à nossa loja de tecnologia! Como posso ajudá-lo hoje?',
    'bom dia' => 'Bom dia! ☀️ Como posso ajudá-lo hoje?',
    'boa tarde' => 'Boa tarde! 🌅 Como posso ajudá-lo hoje?',
    'boa noite' => 'Boa noite! 🌙 Como posso ajudá-lo hoje?',
    
    // Produtos
    'produtos' => 'Temos uma grande variedade de produtos tecnológicos! 📱💻 Smartphones, notebooks, tablets, acessórios e muito mais. Que tipo de produto você está procurando?',
    'smartphone' => 'Temos excelentes smartphones! 📱 Confira nossa seção de produtos para ver os modelos disponíveis com os melhores preços.',
    'notebook' => 'Nossos notebooks são perfeitos para trabalho e estudos! 💻 Visite nossa página de produtos para ver as opções disponíveis.',
    'tablet' => 'Tablets são ótimos para entretenimento e produtividade! 📱 Confira nossa seleção na página de produtos.',
    'preço' => 'Nossos preços são muito competitivos! 💰 Você pode ver o preço de cada produto na página de detalhes. Temos opções para todos os orçamentos.',
    'preco' => 'Nossos preços são muito competitivos! 💰 Você pode ver o preço de cada produto na página de detalhes. Temos opções para todos os orçamentos.',
    
    // Compras
    'como comprar' => 'É muito fácil comprar conosco! 🛒 1) Navegue pelos produtos 2) Adicione ao carrinho 3) Finalize a compra com seus dados. Simples assim!',
    'carrinho' => 'Para adicionar produtos ao carrinho, clique em "Ver Detalhes" no produto desejado e depois em "Adicionar ao Carrinho". 🛒',
    'pagamento' => 'Aceitamos diversas formas de pagamento! 💳 Cartão de crédito, débito, PIX e boleto bancário.',
    'entrega' => 'Fazemos entregas para todo o Brasil! 🚚 O prazo varia de 3 a 10 dias úteis dependendo da sua região.',
    'frete' => 'O frete é calculado automaticamente no checkout baseado no seu CEP. 📦 Oferecemos frete grátis para compras acima de R$ 200!',
    
    // Suporte
    'ajuda' => 'Estou aqui para ajudar! 🤝 Você pode me perguntar sobre produtos, como comprar, formas de pagamento, entrega ou qualquer dúvida sobre nossa loja.',
    'suporte' => 'Nossa equipe de suporte está sempre disponível! 📞 Você pode entrar em contato através da página de contato ou continuar conversando comigo.',
    'contato' => 'Você pode nos contatar através da página "Contato" do site ou continuar conversando comigo aqui! 📧',
    'telefone' => 'Nosso telefone de contato está disponível na página "Contato" do site. 📞',
    'email' => 'Nosso email de contato está disponível na página "Contato" do site. 📧',
    
    // Sobre a loja
    'sobre' => 'Somos uma loja especializada em tecnologia! 🏪 Oferecemos produtos de qualidade com os melhores preços e atendimento excepcional.',
    'empresa' => 'Somos uma empresa focada em trazer a melhor tecnologia para você! 🚀 Visite nossa página "Sobre" para saber mais.',
    'qualidade' => 'Todos os nossos produtos passam por rigoroso controle de qualidade! ✅ Trabalhamos apenas com marcas confiáveis.',
    
    // Categorias
    'categoria' => 'Temos várias categorias: Smartphones, Notebooks, Tablets, Acessórios, Games, Audio, Computadores e Periféricos! 📂',
    'categorias' => 'Temos várias categorias: Smartphones, Notebooks, Tablets, Acessórios, Games, Audio, Computadores e Periféricos! 📂',
    'games' => 'Temos uma seção dedicada a games! 🎮 Consoles, jogos e acessórios para gamers.',
    'audio' => 'Nossa seção de audio tem fones, caixas de som e equipamentos de som! 🎵',
    'acessorios' => 'Temos diversos acessórios para seus dispositivos! 🔌 Cabos, capas, carregadores e muito mais.',
    
    // Despedidas
    'tchau' => 'Até logo! 👋 Volte sempre que precisar de ajuda. Boa compra!',
    'obrigado' => 'Por nada! 😊 Estou sempre aqui para ajudar. Boa compra!',
    'obrigada' => 'Por nada! 😊 Estou sempre aqui para ajudar. Boa compra!',
    'valeu' => 'Disponha! 😊 Qualquer dúvida, é só chamar!',
];

// Buscar resposta
$resposta = '';

// Verificar correspondência exata primeiro
if (isset($respostas[$mensagem])) {
    $resposta = $respostas[$mensagem];
} else {
    // Buscar por palavras-chave
    foreach ($respostas as $palavra => $resp) {
        if (strpos($mensagem, $palavra) !== false) {
            $resposta = $resp;
            break;
        }
    }
}

// Resposta padrão se não encontrar correspondência
if (empty($resposta)) {
    $respostas_padrao = [
        'Desculpe, não entendi sua pergunta. 🤔 Você pode me perguntar sobre produtos, como comprar, formas de pagamento, entrega ou qualquer dúvida sobre nossa loja!',
        'Hmm, não tenho certeza sobre isso. 🤷‍♂️ Que tal perguntar sobre nossos produtos, preços ou como fazer uma compra?',
        'Não consegui entender. 😅 Posso ajudar com informações sobre produtos, compras, pagamento, entrega ou suporte!',
        'Essa pergunta é nova para mim! 🤖 Experimente perguntar sobre smartphones, notebooks, como comprar ou formas de pagamento.',
    ];
    $resposta = $respostas_padrao[array_rand($respostas_padrao)];
}

// Retornar resposta em JSON
echo json_encode([
    'resposta' => $resposta,
    'timestamp' => date('H:i')
]);
?>

