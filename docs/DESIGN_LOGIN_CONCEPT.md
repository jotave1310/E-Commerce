# Conceito de Design e Fluxo do Sistema de Login - E-commerce Project

## 1. Aprimoramento Visual e Fluidez do Frontend

### 1.1. Objetivo
Transformar a interface do usuário em uma experiência mais dinâmica, moderna e profissional, focando em fluidez, animações sutis e correção de quaisquer bugs visuais remanescentes. O objetivo é criar uma sensação de "premium" e "tecnologia de ponta" que ressoe com o nicho de e-commerce de tecnologia.

### 1.2. Princípios de Design
- **Minimalismo Funcional**: Remover elementos desnecessários, priorizando a clareza e a usabilidade.
- **Micro-interações**: Adicionar pequenas animações e feedbacks visuais para tornar a interação mais agradável e intuitiva.
- **Transições Suaves**: Garantir que as mudanças de estado (hover, clique, carregamento) sejam fluidas e não abruptas.
- **Consistência**: Manter um padrão visual rigoroso em todas as páginas e componentes.
- **Performance**: Otimizar animações e transições para não impactar negativamente o tempo de carregamento ou a responsividade.

### 1.3. Elementos a Aprimorar

#### a) Animações e Transições
- **Botões**: Efeitos de `hover` e `active` mais elaborados (ex: gradiente sutil se movendo, brilho ao clicar).
- **Cards de Produtos**: Animação de `scale` ou `lift` ao passar o mouse, com sombra suave para profundidade.
- **Navegação (Header/Footer)**: Transições suaves para links e ícones.
- **Carregamento de Conteúdo**: Indicadores de carregamento animados (skeletons, spinners) para uma experiência mais fluida.
- **Scroll**: Efeitos de `parallax` ou elementos que aparecem/desaparecem suavemente ao rolar a página.

#### b) Correção de Bugs CSS
- **Alinhamento**: Garantir alinhamento perfeito de todos os elementos em diferentes resoluções.
- **Espaçamento**: Revisar `padding` e `margin` para evitar sobreposições ou lacunas indesejadas.
- **Responsividade**: Testar e ajustar o layout em uma gama maior de dispositivos e tamanhos de tela.
- **Consistência de Cores/Fontes**: Verificar se a paleta de cores e a tipografia estão sendo aplicadas uniformemente.

#### c) Detalhes Estéticos
- **Sombras e Profundidade**: Uso estratégico de sombras para criar hierarquia e profundidade.
- **Bordas e Cantos**: Arredondamento consistente de bordas para um visual moderno.
- **Ícones**: Utilização de ícones SVG para escalabilidade e clareza.
- **Imagens**: Otimização e, se possível, adição de efeitos visuais (ex: `blur` para fundos, `grayscale` em `hover`).

## 2. Sistema de Login para Usuários

### 2.1. Objetivo
Implementar um sistema de autenticação robusto e seguro que permita aos usuários registrar-se, fazer login e gerenciar suas informações pessoais e pedidos. O sistema deve ser intuitivo e seguir as melhores práticas de segurança.

### 2.2. Entidades do Banco de Dados (Atualização)
Para suportar o sistema de login, a tabela `usuarios` será adicionada ao banco de dados `ecommerce_db`.

#### Tabela `usuarios`
- `id` (INT, PK, AUTO_INCREMENT): Identificador único do usuário.
- `nome` (VARCHAR(255)): Nome completo do usuário.
- `email` (VARCHAR(255), UNIQUE): Endereço de e-mail do usuário (usado para login).
- `senha` (VARCHAR(255)): Senha do usuário (armazenada como hash).
- `telefone` (VARCHAR(20), NULLABLE): Telefone de contato.
- `endereco` (VARCHAR(255), NULLABLE): Endereço completo.
- `cidade` (VARCHAR(100), NULLABLE): Cidade.
- `cep` (VARCHAR(10), NULLABLE): CEP.
- `data_registro` (DATETIME): Data e hora do registro do usuário.
- `ultimo_login` (DATETIME, NULLABLE): Data e hora do último login.

### 2.3. Fluxo do Sistema de Login

#### a) Registro de Usuário
1. **Formulário de Registro**: Página dedicada (`registro.php`) com campos para nome, email, senha, telefone, endereço, cidade, CEP.
2. **Validação**: Validação de entrada no frontend (JavaScript) e backend (PHP) para formato de email, força da senha, campos obrigatórios.
3. **Hashing de Senha**: A senha será armazenada no banco de dados usando `password_hash()` para segurança.
4. **Confirmação**: Após o registro bem-sucedido, o usuário é redirecionado para a página de login ou uma página de sucesso.

#### b) Login de Usuário
1. **Formulário de Login**: Página dedicada (`login.php`) com campos para email e senha.
2. **Autenticação**: Verificação do email e da senha (usando `password_verify()`) no banco de dados.
3. **Sessão**: Se as credenciais forem válidas, uma sessão PHP é iniciada (`session_start()`) e o ID do usuário é armazenado na sessão (`$_SESSION["user_id"]`).
4. **Redirecionamento**: O usuário é redirecionado para a página inicial ou para uma área de perfil.

#### c) Logout de Usuário
1. **Link de Logout**: Um link (`logout.php`) que destrói a sessão PHP (`session_destroy()`) e redireciona o usuário para a página inicial ou de login.

#### d) Recuperação de Senha (Básico)
1. **Formulário de Recuperação**: Página (`recuperar_senha.php`) para o usuário inserir seu email.
2. **Processamento**: (Para uma implementação básica, pode-se apenas indicar que um email foi enviado, sem funcionalidade real de envio de email. Para uma versão completa, seria necessário um sistema de token e envio de email).

### 2.4. Integração com o Site
- **Header**: O header do site exibirá "Login" ou "Registrar" se o usuário não estiver logado, e "Olá, [Nome do Usuário]" com um link para "Meu Perfil" e "Sair" se estiver logado.
- **Checkout**: O processo de checkout poderá ser simplificado para usuários logados, pré-preenchendo informações de entrega.
- **Admin Panel**: O `admin.php` poderá ser protegido, exigindo login de um usuário com permissões administrativas (futura implementação, inicialmente apenas login básico).

## 3. Considerações de Segurança
- **SQL Injection**: Uso de Prepared Statements (PDO) para todas as interações com o banco de dados.
- **XSS (Cross-Site Scripting)**: Sanitização de entradas e escape de saídas (`htmlspecialchars()`).
- **Hashing de Senhas**: Uso de `password_hash()` e `password_verify()` para armazenamento seguro de senhas.
- **Sessões**: Gerenciamento seguro de sessões, incluindo `session_regenerate_id()` e tempo de expiração.

Este documento servirá como guia para as próximas fases de desenvolvimento, garantindo que as melhorias visuais e o sistema de login sejam implementados de forma coesa e segura.

