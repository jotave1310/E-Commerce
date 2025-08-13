# Changelog - E-commerce Project

Todas as mudanças notáveis neste projeto serão documentadas neste arquivo.

O formato é baseado em [Keep a Changelog](https://keepachangelog.com/pt-BR/1.0.0/),
e este projeto adere ao [Versionamento Semântico](https://semver.org/lang/pt-BR/).

## [Não Lançado]

### Planejado
- Sistema de autenticação de usuários
- Integração com gateway de pagamento
- Sistema de avaliações de produtos
- API REST para mobile
- Sistema de cupons de desconto

## [2.0.0] - 2025-08-07

### Adicionado
- **Novo Design de Tecnologia**: Tema escuro moderno com paleta azul/vermelho, efeitos glassmorphism e animações suaves.
- **Painel Administrativo (admin.php)**: Interface completa para cadastro e gerenciamento de produtos.
- **Chatbot Virtual Inteligente**: Assistente automatizado com respostas contextuais sobre produtos, compras, pagamento e suporte.
- **Sistema de Categorias Dinâmicas**: Categorização automática de produtos com criação de novas categorias.
- **Interface Responsiva Avançada**: Layout otimizado para desktop, tablet e mobile com micro-interações.

### Alterado
- **Design Completo**: Repaginação total do frontend com tema de tecnologia.
- **Tipografia**: Implementação de fontes modernas (Montserrat + Lato).
- **Navegação**: Menu aprimorado com efeitos visuais e animações.
- **Cards de Produtos**: Redesign completo com gradientes e hover effects.
- **Formulários**: Estilização moderna com validação visual.

### Melhorado
- **Experiência do Usuário**: Interface mais intuitiva e interativa.
- **Performance**: Otimizações de CSS e JavaScript.
- **Acessibilidade**: Melhor contraste e navegação por teclado.
- **Responsividade**: Breakpoints otimizados para todos os dispositivos.
- **Feedback Visual**: Animações e transições suaves em toda a interface.

### Técnico
- **Arquitetura**: Separação clara entre apresentação e lógica de negócio.
- **Banco de Dados**: Função `adicionarProduto()` para inserção dinâmica.
- **JavaScript**: Implementação de AJAX para comunicação com chatbot.
- **CSS**: Uso avançado de CSS3 com gradientes, backdrop-filter e animações.
- **PHP**: Melhorias na estrutura de funções e tratamento de erros.

## [1.1.1] - 2025-08-06

### Corrigido
- Warning: Undefined variable $dadosCliente em `checkout.php`.
- Warnings: Trying to access array offset on value of type null em `db_connect.php`.
- Fluxo de finalização de compra para garantir a persistência correta dos pedidos no banco de dados.

## [1.1.0] - 2025-08-06

### Adicionado
- Integração completa com banco de dados MySQL para produtos, categorias e pedidos.
- Persistência de pedidos no banco de dados.
- Arquivo `db_connect.php` para gerenciamento da conexão e funções de interação com o DB.
- Script `database.sql` para criação do esquema do DB e população inicial de dados.
- Documentação do esquema do banco de dados (`DATABASE_SCHEMA.md`).

### Alterado
- `config.php` refatorado para usar o banco de dados e remover o array de produtos estático.
- Páginas `index.php`, `produtos.php` e `produto.php` atualizadas para buscar dados do MySQL.
- `checkout.php` adaptado para salvar pedidos no banco de dados.
- Rodapé de todas as páginas atualizado com a menção "| Dexo".

### Melhorado
- Estrutura de dados e funções para interação com o banco de dados.
- Modularidade do código com a separação da lógica de DB.

## [1.0.0] - 2025-08-06

### Adicionado
- Estrutura inicial do projeto
- Sistema de catálogo de produtos
- Funcionalidade de carrinho de compras
- Processo de checkout completo
- Design responsivo
- Páginas institucionais (Sobre, Contato)
- Documentação completa
- Controle de versão com Git/GitHub

### Funcionalidades Principais

#### Sistema de Produtos
- Exibição de produtos em grid responsivo
- Página de detalhes de produto individual
- Categorização de produtos
- Formatação de preços em real brasileiro
- Imagens placeholder para produtos

#### Carrinho de Compras
- Adição de produtos ao carrinho
- Remoção de produtos do carrinho
- Atualização de quantidades
- Cálculo automático de totais
- Persistência durante a sessão
- Contador visual no header

#### Sistema de Checkout
- Formulário de dados do cliente
- Validação de campos obrigatórios
- Resumo do pedido
- Geração de número de pedido
- Página de confirmação de compra
- Limpeza automática do carrinho após compra

#### Interface e Design
- Layout responsivo para desktop e mobile
- Navegação intuitiva
- Feedback visual para ações do usuário
- Esquema de cores profissional
- Tipografia legível
- Componentes reutilizáveis

#### Páginas Institucionais
- Página inicial com produtos em destaque
- Página "Sobre" com informações do projeto
- Página de contato com formulário funcional
- Página de listagem completa de produtos

### Técnico

#### Arquitetura
- Estrutura MVC simplificada
- Separação de configurações em arquivo dedicado
- Funções auxiliares centralizadas
- Código limpo e bem documentado

#### Tecnologias
- PHP 8.1+ para backend
- HTML5 semântico
- CSS3 com Flexbox e Grid
- Sessões PHP para estado
- Git para controle de versão

#### Segurança
- Sanitização de dados de entrada
- Escape de output HTML
- Validação de formulários
- Prevenção básica contra XSS

#### Performance
- CSS otimizado
- Estrutura de arquivos eficiente
- Carregamento rápido de páginas

### Documentação
- README.md completo com instruções
- Guia de instalação detalhado (INSTALL.md)
- Documentação da API/funções (API.md)
- Histórico de versões (CHANGELOG.md)
- Comentários no código

### Controle de Versão
- Repositório Git inicializado
- Commits organizados por funcionalidade
- Repositório remoto no GitHub
- Branches organizadas

## [0.3.0] - 2025-08-06

### Adicionado
- Páginas institucionais (sobre.php, contato.php)
- Sistema de checkout completo
- Página de sucesso da compra
- Validação de formulários
- Mensagens de feedback para usuário

### Melhorado
- Estrutura de navegação
- Layout das páginas
- Experiência do usuário

## [0.2.0] - 2025-08-06

### Adicionado
- Sistema completo de carrinho de compras
- Página de detalhes do produto
- Funcionalidades de adicionar/remover produtos
- Cálculo automático de totais
- Página de listagem de produtos

### Melhorado
- Estrutura do código PHP
- Organização de funções
- Interface do usuário

## [0.1.0] - 2025-08-06

### Adicionado
- Estrutura inicial do projeto
- Configuração do ambiente de desenvolvimento
- Página inicial básica
- Estilos CSS fundamentais
- Configuração do repositório Git
- Primeiro commit no GitHub

### Configuração
- Servidor Apache configurado
- PHP 8.1 instalado e configurado
- Estrutura de diretórios criada
- Permissões de arquivo configuradas

## Tipos de Mudanças

- `Adicionado` para novas funcionalidades
- `Alterado` para mudanças em funcionalidades existentes
- `Descontinuado` para funcionalidades que serão removidas
- `Removido` para funcionalidades removidas
- `Corrigido` para correções de bugs
- `Segurança` para vulnerabilidades

## Convenções de Commit

Este projeto segue as seguintes convenções para mensagens de commit:

- `feat:` Nova funcionalidade
- `fix:` Correção de bug
- `docs:` Mudanças na documentação
- `style:` Mudanças de formatação/estilo
- `refactor:` Refatoração de código
- `test:` Adição ou modificação de testes
- `chore:` Tarefas de manutenção

### Exemplos de Commits

```
feat: add product detail page with add to cart functionality
fix: resolve cart total calculation error
docs: update installation guide with Docker instructions
style: improve responsive design for mobile devices
refactor: extract product functions to separate config file
```

## Roadmap Futuro

### Versão 1.2.0 (Planejado)
- Sistema de busca de produtos
- Filtros por categoria e preço
- Paginação de produtos
- Melhorias na interface

### Versão 1.3.0 (Planejado)
- Sistema de usuários e login
- Histórico de pedidos
- Perfil do usuário
- Wishlist de produtos

### Versão 2.0.0 (Planejado)
- Integração com banco de dados
- Painel administrativo
- Sistema de estoque
- Relatórios de vendas

### Versão 2.1.0 (Planejado)
- API REST
- Aplicativo mobile
- Sistema de notificações
- Integração com redes sociais

## Contribuições

Para contribuir com o projeto:

1. Faça um fork do repositório
2. Crie uma branch para sua feature (`git checkout -b feature/nova-funcionalidade`)
3. Commit suas mudanças (`git commit -am "feat: add nova funcionalidade"`)
4. Push para a branch (`git push origin feature/nova-funcionalidade`)
5. Crie um Pull Request

### Diretrizes para Contribuição

- Siga as convenções de commit estabelecidas
- Mantenha o código limpo e bem documentado
- Adicione testes quando aplicável
- Atualize a documentação conforme necessário
- Mantenha compatibilidade com versões anteriores

## Suporte

- **Repositório**: [https://github.com/jotave1310/ecommerce_project](https://github.com/jotave1310/ecommerce_project)
- **Issues**: [https://github.com/jotave1310/ecommerce_project/issues](https://github.com/jotave1310/ecommerce_project/issues)
- **Documentação**: Consulte os arquivos na pasta `docs/`

---

**Mantido por**: Manus AI  
**Última atualização**: 06 de Agosto de 2025



