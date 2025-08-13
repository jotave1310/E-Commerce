# E-commerce Project

Um projeto de loja online desenvolvido em PHP, HTML, CSS e MySQL com design moderno de tecnologia, painel administrativo e chatbot inteligente.

## 📋 Índice

- [Sobre o Projeto](#sobre-o-projeto)
- [Tecnologias Utilizadas](#tecnologias-utilizadas)
- [Funcionalidades](#funcionalidades)
- [Instalação e Configuração](#instalação-e-configuração)
- [Estrutura do Projeto](#estrutura-do-projeto)
- [Como Usar](#como-usar)
- [Painel Administrativo](#painel-administrativo)
- [Chatbot Virtual](#chatbot-virtual)
- [Contribuição](#contribuição)
- [Licença](#licença)

## 🎯 Sobre o Projeto

O **E-commerce Project** é uma aplicação web completa que simula uma loja online de tecnologia, desenvolvida para demonstrar conhecimentos avançados em desenvolvimento web usando tecnologias server-side e client-side. O projeto inclui todas as funcionalidades essenciais de um e-commerce moderno, desde a exibição de produtos até o processo de checkout, além de recursos avançados como painel administrativo e chatbot inteligente.

### Objetivos do Projeto

- Demonstrar proficiência em PHP para desenvolvimento backend
- Implementar design responsivo moderno com tema de tecnologia
- Criar uma experiência de usuário intuitiva e interativa
- Aplicar boas práticas de desenvolvimento web e segurança
- Implementar sistema de gerenciamento de produtos
- Desenvolver assistente virtual para suporte ao cliente
- Implementar controle de versão com Git/GitHub
- Documentar adequadamente o código e funcionalidades

## 🚀 Tecnologias Utilizadas

### Backend
- **PHP 8.1+**: Linguagem principal para lógica server-side
- **MySQL 8.0+**: Sistema de gerenciamento de banco de dados relacional
- **PDO**: Extensão PHP para acesso a banco de dados com prepared statements
- **Sessões PHP**: Gerenciamento de estado do carrinho de compras
- **Apache**: Servidor web para hospedagem

### Frontend
- **HTML5**: Estruturação semântica das páginas
- **CSS3**: Estilização avançada com gradientes, glassmorphism e animações
- **JavaScript ES6+**: Interações dinâmicas e comunicação AJAX
- **Design Responsivo**: Layout adaptável para desktop e mobile

### Ferramentas de Desenvolvimento
- **Git**: Controle de versão
- **GitHub**: Repositório remoto e colaboração
- **Apache/PHP/MySQL**: Ambiente de desenvolvimento local

## ✨ Funcionalidades

### 🛍️ Catálogo de Produtos
- Exibição dinâmica de produtos do banco de dados
- Categorização por tipo (Smartphones, Notebooks, Tablets, etc.)
- Grid responsivo com cards modernos
- Formatação de preços em Real brasileiro
- Sistema de busca e filtros por categoria
- Páginas de detalhes individuais dos produtos

### 🛒 Sistema de Carrinho de Compras
- Adição e remoção de produtos
- Atualização de quantidades em tempo real
- Cálculo automático de totais
- Persistência durante a sessão
- Contador visual no header
- Interface intuitiva e responsiva

### 💳 Processo de Checkout
- Formulário completo de dados do cliente
- Validação de campos obrigatórios
- Resumo detalhado do pedido
- Geração automática de número de pedido
- Salvamento no banco de dados
- Página de confirmação de compra
- Limpeza automática do carrinho

### 🎨 Design Moderno de Tecnologia
- Tema escuro com paleta azul/vermelho
- Efeitos glassmorphism e backdrop-filter
- Gradientes dinâmicos e animações suaves
- Tipografia moderna (Montserrat + Lato)
- Layout totalmente responsivo
- Micro-interações e hover effects
- Scrollbar personalizada

### 👨‍💼 Painel Administrativo
- Interface dedicada para gestão de produtos
- Formulário de cadastro com validação
- Seleção de categorias predefinidas
- Listagem em tempo real dos produtos
- Integração completa com banco de dados
- Design consistente com o tema principal

### 🤖 Chatbot Virtual Inteligente
- Interface flutuante moderna
- Respostas contextuais sobre:
  - Produtos e categorias disponíveis
  - Processo de compra e checkout
  - Formas de pagamento aceitas
  - Informações de entrega e frete
  - Suporte ao cliente e contato
- Animações de digitação realistas
- Timestamps nas mensagens
- Comunicação via AJAX em tempo real

### 🗄️ Sistema de Banco de Dados
- Estrutura relacional otimizada
- Tabelas: produtos, categorias, pedidos, itens_pedido
- Relacionamentos com chaves estrangeiras
- Prepared statements para segurança
- Tratamento de erros e logging
- Backup e versionamento de schema

### 📱 Interface Responsiva
- Layout adaptável para desktop, tablet e mobile
- Breakpoints otimizados para diferentes telas
- Touch-friendly para dispositivos móveis
- Navegação intuitiva em qualquer dispositivo
- Performance otimizada para carregamento rápido

## 🔧 Instalação e Configuração

### Pré-requisitos

- PHP 8.1 ou superior
- MySQL 8.0 ou superior
- Apache 2.4 ou superior
- Git para controle de versão

### Passo a Passo

1. **Clone o repositório**
   ```bash
   git clone https://github.com/jotave1310/ecommerce_project.git
   cd ecommerce_project
   ```

2. **Configure o banco de dados**
   ```bash
   # Crie o banco de dados
   mysql -u root -p -e "CREATE DATABASE ecommerce_db;"
   
   # Importe o schema e dados iniciais
   mysql -u root -p ecommerce_db < database.sql
   ```

3. **Configure as credenciais**
   - Edite o arquivo `db_connect.php`
   - Ajuste as configurações de conexão com o banco

4. **Configure o servidor web**
   - Aponte o DocumentRoot para o diretório do projeto
   - Certifique-se de que o mod_rewrite está habilitado
   - Configure as permissões adequadas

5. **Teste a instalação**
   - Acesse `http://localhost/ecommerce_project/`
   - Verifique se todos os produtos são exibidos
   - Teste o painel administrativo em `/admin.php`

## 📁 Estrutura do Projeto

```
ecommerce_project/
├── index.php              # Página principal
├── produtos.php            # Catálogo completo
├── produto.php             # Detalhes do produto
├── carrinho.php            # Carrinho de compras
├── checkout.php            # Processo de checkout
├── sucesso.php             # Confirmação de compra
├── sobre.php               # Página institucional
├── contato.php             # Formulário de contato
├── admin.php               # Painel administrativo
├── chatbot.php             # API do chatbot
├── config.php              # Configurações gerais
├── db_connect.php          # Conexão e funções do banco
├── database.sql            # Schema e dados iniciais
├── style.css               # Estilos principais
├── docs/                   # Documentação
│   ├── INSTALL.md          # Guia de instalação
│   ├── API.md              # Documentação da API
│   ├── CHANGELOG.md        # Histórico de versões
│   ├── DATABASE_SCHEMA.md  # Esquema do banco
│   └── DESIGN_CONCEPT.md   # Conceito de design
└── README.md               # Este arquivo
```

## 🎮 Como Usar

### Para Clientes

1. **Navegação**
   - Acesse a página inicial para ver produtos em destaque
   - Use o menu para navegar entre seções
   - Clique em "Produtos" para ver o catálogo completo

2. **Compras**
   - Clique em "Ver Detalhes" em qualquer produto
   - Use "Adicionar ao Carrinho" para incluir itens
   - Acesse o carrinho pelo ícone no header
   - Finalize a compra com seus dados

3. **Suporte**
   - Clique no ícone do chatbot (💬) no canto inferior direito
   - Digite suas dúvidas sobre produtos, compras ou entrega
   - Receba respostas instantâneas e contextuais

### Para Administradores

1. **Acesso ao Painel**
   - Acesse `/admin.php` diretamente
   - Use a interface para gerenciar produtos

2. **Cadastro de Produtos**
   - Preencha o formulário com dados do produto
   - Selecione a categoria apropriada
   - Defina preço e descrição detalhada
   - Clique em "Adicionar Produto"

3. **Gerenciamento**
   - Visualize todos os produtos cadastrados
   - Monitore vendas e estoque
   - Atualize informações conforme necessário

## 👨‍💼 Painel Administrativo

O painel administrativo (`admin.php`) oferece uma interface simples e intuitiva para gerenciamento de produtos:

### Funcionalidades
- **Cadastro de Produtos**: Formulário completo com validação
- **Categorias**: Seleção entre categorias predefinidas
- **Listagem**: Visualização em tempo real dos produtos
- **Validação**: Campos obrigatórios e formatação de preços
- **Feedback**: Mensagens de sucesso e erro

### Categorias Disponíveis
- Smartphones
- Notebooks  
- Tablets
- Acessórios
- Games
- Audio
- Computadores
- Periféricos

## 🤖 Chatbot Virtual

O chatbot inteligente oferece suporte automatizado aos clientes:

### Capacidades
- **Produtos**: Informações sobre categorias e especificações
- **Compras**: Orientações sobre o processo de compra
- **Pagamento**: Detalhes sobre formas de pagamento
- **Entrega**: Informações sobre frete e prazos
- **Suporte**: Canais de contato e ajuda

### Tecnologia
- Interface JavaScript moderna
- Comunicação AJAX em tempo real
- Processamento PHP no backend
- Respostas contextuais inteligentes
- Animações e feedback visual

## 🔒 Segurança

### Medidas Implementadas
- **Prepared Statements**: Prevenção contra SQL Injection
- **Sanitização**: Limpeza de dados de entrada
- **Escape de Output**: Prevenção contra XSS
- **Validação**: Verificação de dados no frontend e backend
- **Sessões Seguras**: Gerenciamento adequado de sessões PHP

## 🚀 Performance

### Otimizações
- **CSS Otimizado**: Estilos eficientes e organizados
- **JavaScript Assíncrono**: Carregamento não-bloqueante
- **Queries Otimizadas**: Consultas eficientes ao banco
- **Caching**: Aproveitamento de cache do navegador
- **Compressão**: Assets otimizados para carregamento rápido

## 📊 Banco de Dados

### Estrutura
- **produtos**: Informações dos produtos
- **categorias**: Categorização dos produtos  
- **pedidos**: Dados dos pedidos realizados
- **itens_pedido**: Itens específicos de cada pedido

### Relacionamentos
- Produtos ↔ Categorias (N:1)
- Pedidos ↔ Itens (1:N)
- Itens ↔ Produtos (N:1)

## 🤝 Contribuição

Contribuições são bem-vindas! Para contribuir:

1. Faça um fork do projeto
2. Crie uma branch para sua feature (`git checkout -b feature/nova-funcionalidade`)
3. Commit suas mudanças (`git commit -am 'feat: add nova funcionalidade'`)
4. Push para a branch (`git push origin feature/nova-funcionalidade`)
5. Abra um Pull Request

### Diretrizes
- Siga as convenções de commit estabelecidas
- Mantenha o código limpo e bem documentado
- Adicione testes quando aplicável
- Atualize a documentação conforme necessário

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo `LICENSE` para mais detalhes.

## 📞 Suporte

- **Repositório**: [https://github.com/jotave1310/ecommerce_project](https://github.com/jotave1310/ecommerce_project)
- **Issues**: [https://github.com/jotave1310/ecommerce_project/issues](https://github.com/jotave1310/ecommerce_project/issues)
- **Documentação**: Consulte os arquivos na pasta `docs/`

---

**Desenvolvido por**: Dexo  
**Última atualização**: 07 de Agosto de 2025  
**Versão**: 2.0.0

