# Guia de Instalação Detalhado - E-commerce Project

Este documento fornece instruções completas para instalação e configuração do E-commerce Project em diferentes ambientes.

## 📋 Requisitos do Sistema

### Requisitos Mínimos

- **Sistema Operacional**: Linux (Ubuntu 18.04+), Windows 10+, macOS 10.14+
- **PHP**: Versão 8.1 ou superior
- **Servidor Web**: Apache 2.4+ ou Nginx 1.18+
- **Memória RAM**: 512 MB mínimo, 1 GB recomendado
- **Espaço em Disco**: 100 MB para a aplicação
- **Git**: Para controle de versão

### Extensões PHP Necessárias

- `php-session`: Para gerenciamento de sessões
- `php-filter`: Para validação de dados
- `php-json`: Para manipulação de dados JSON (geralmente incluído)

## 🐧 Instalação no Linux (Ubuntu/Debian)

### Passo 1: Atualizar o Sistema

```bash
sudo apt update && sudo apt upgrade -y
```

### Passo 2: Instalar Apache e PHP

```bash
# Instalar Apache
sudo apt install apache2 -y

# Instalar PHP e módulos necessários
sudo apt install php libapache2-mod-php php-mysql php-cli php-curl php-json -y

# Habilitar módulo PHP no Apache
sudo a2enmod php8.1
sudo systemctl restart apache2
```

### Passo 3: Verificar Instalação

```bash
# Verificar versão do PHP
php --version

# Verificar status do Apache
sudo systemctl status apache2

# Testar PHP no Apache
echo "<?php phpinfo(); ?>" | sudo tee /var/www/html/info.php
```

Acesse `http://localhost/info.php` para verificar se o PHP está funcionando.

### Passo 4: Clonar o Projeto

```bash
# Navegar para o diretório web
cd /var/www/html

# Clonar o repositório
sudo git clone https://github.com/jotave1310/ecommerce_project.git

# Configurar permissões
sudo chown -R www-data:www-data ecommerce_project
sudo chmod -R 755 ecommerce_project
```

### Passo 5: Configurar Virtual Host (Opcional)

Criar um virtual host para o projeto:

```bash
sudo nano /etc/apache2/sites-available/ecommerce.conf
```

Adicionar o conteúdo:

```apache
<VirtualHost *:80>
    ServerName ecommerce.local
    DocumentRoot /var/www/html/ecommerce_project
    
    <Directory /var/www/html/ecommerce_project>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/ecommerce_error.log
    CustomLog ${APACHE_LOG_DIR}/ecommerce_access.log combined
</VirtualHost>
```

Habilitar o site:

```bash
sudo a2ensite ecommerce.conf
sudo systemctl reload apache2

# Adicionar ao hosts (opcional)
echo "127.0.0.1 ecommerce.local" | sudo tee -a /etc/hosts
```

## 🪟 Instalação no Windows

### Passo 1: Instalar XAMPP

1. Baixe o XAMPP do site oficial: https://www.apachefriends.org/
2. Execute o instalador como administrador
3. Selecione Apache e PHP durante a instalação
4. Inicie o Apache através do painel de controle do XAMPP

### Passo 2: Clonar o Projeto

```cmd
# Abrir prompt de comando como administrador
cd C:\xampp\htdocs

# Clonar o repositório
git clone https://github.com/jotave1310/ecommerce_project.git
```

### Passo 3: Configurar Permissões

No Windows, geralmente não são necessárias configurações especiais de permissão para desenvolvimento local.

### Passo 4: Acessar a Aplicação

Abra o navegador e acesse: `http://localhost/ecommerce_project/`

## 🍎 Instalação no macOS

### Passo 1: Instalar Homebrew

```bash
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
```

### Passo 2: Instalar Apache e PHP

```bash
# Instalar Apache
brew install httpd

# Instalar PHP
brew install php

# Configurar Apache para usar PHP
echo "LoadModule php_module $(brew --prefix)/lib/httpd/modules/libphp.so" >> $(brew --prefix)/etc/httpd/httpd.conf
echo "AddType application/x-httpd-php .php" >> $(brew --prefix)/etc/httpd/httpd.conf
```

### Passo 3: Configurar DocumentRoot

Editar o arquivo de configuração do Apache:

```bash
nano $(brew --prefix)/etc/httpd/httpd.conf
```

Alterar a linha DocumentRoot para:

```apache
DocumentRoot "$(brew --prefix)/var/www"
```

### Passo 4: Clonar o Projeto

```bash
cd $(brew --prefix)/var/www
git clone https://github.com/jotave1310/ecommerce_project.git
```

### Passo 5: Iniciar Serviços

```bash
# Iniciar Apache
sudo brew services start httpd

# Verificar se está rodando
curl http://localhost:8080/ecommerce_project/
```

## 🐳 Instalação com Docker

### Dockerfile

Criar um `Dockerfile` na raiz do projeto:

```dockerfile
FROM php:8.1-apache

# Copiar arquivos do projeto
COPY . /var/www/html/

# Configurar permissões
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Habilitar mod_rewrite
RUN a2enmod rewrite

# Expor porta 80
EXPOSE 80
```

### docker-compose.yml

```yaml
version: '3.8'

services:
  web:
    build: .
    ports:
      - "8080:80"
    volumes:
      - .:/var/www/html
    environment:
      - APACHE_DOCUMENT_ROOT=/var/www/html
```

### Comandos Docker

```bash
# Construir e executar
docker-compose up -d

# Acessar em http://localhost:8080
```

## ⚙️ Configurações Avançadas

### Configuração de Sessões PHP

Editar `php.ini` para otimizar sessões:

```ini
session.save_handler = files
session.save_path = "/tmp"
session.gc_maxlifetime = 1440
session.cookie_lifetime = 0
session.cookie_secure = 0
session.cookie_httponly = 1
```

### Configuração de Segurança Apache

Adicionar ao `.htaccess` (criar se não existir):

```apache
# Segurança básica
ServerTokens Prod
ServerSignature Off

# Prevenir acesso a arquivos sensíveis
<Files "config.php">
    Order Allow,Deny
    Deny from all
</Files>

# Headers de segurança
Header always set X-Content-Type-Options nosniff
Header always set X-Frame-Options DENY
Header always set X-XSS-Protection "1; mode=block"
```

### Otimização de Performance

#### Configuração Apache

```apache
# Habilitar compressão
LoadModule deflate_module modules/mod_deflate.so

<Location />
    SetOutputFilter DEFLATE
    SetEnvIfNoCase Request_URI \
        \.(?:gif|jpe?g|png)$ no-gzip dont-vary
    SetEnvIfNoCase Request_URI \
        \.(?:exe|t?gz|zip|bz2|sit|rar)$ no-gzip dont-vary
</Location>

# Cache de arquivos estáticos
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
    ExpiresByType image/png "access plus 1 month"
    ExpiresByType image/jpg "access plus 1 month"
    ExpiresByType image/jpeg "access plus 1 month"
</IfModule>
```

## 🔧 Solução de Problemas

### Problema: Página em branco

**Causa**: Erro de PHP não exibido

**Solução**:
```bash
# Habilitar exibição de erros
echo "display_errors = On" | sudo tee -a /etc/php/8.1/apache2/php.ini
sudo systemctl restart apache2
```

### Problema: Permissões negadas

**Causa**: Permissões incorretas de arquivo

**Solução**:
```bash
sudo chown -R www-data:www-data /var/www/html/ecommerce_project
sudo chmod -R 755 /var/www/html/ecommerce_project
```

### Problema: Sessões não funcionam

**Causa**: Diretório de sessões sem permissão

**Solução**:
```bash
sudo chmod 1733 /tmp
# ou
sudo mkdir -p /var/lib/php/sessions
sudo chown www-data:www-data /var/lib/php/sessions
```

### Problema: Apache não inicia

**Causa**: Porta 80 em uso

**Solução**:
```bash
# Verificar o que está usando a porta 80
sudo netstat -tulpn | grep :80

# Parar serviço conflitante ou alterar porta do Apache
sudo nano /etc/apache2/ports.conf
# Alterar "Listen 80" para "Listen 8080"
```

## 📊 Verificação da Instalação

### Checklist de Verificação

- [ ] PHP versão 8.1+ instalado
- [ ] Apache rodando e acessível
- [ ] Projeto clonado no diretório correto
- [ ] Permissões configuradas adequadamente
- [ ] Página inicial carrega sem erros
- [ ] Funcionalidades básicas funcionam:
  - [ ] Navegação entre páginas
  - [ ] Visualização de produtos
  - [ ] Adição ao carrinho
  - [ ] Processo de checkout

### Script de Verificação

Criar um script `check_install.php`:

```php
<?php
echo "<h1>Verificação da Instalação</h1>";

// Verificar versão PHP
echo "<p>PHP Version: " . phpversion() . "</p>";

// Verificar extensões
$extensions = ['session', 'filter', 'json'];
foreach ($extensions as $ext) {
    echo "<p>Extensão $ext: " . (extension_loaded($ext) ? "✓" : "✗") . "</p>";
}

// Verificar permissões de escrita
$writable = is_writable(__DIR__);
echo "<p>Diretório gravável: " . ($writable ? "✓" : "✗") . "</p>";

// Verificar sessões
session_start();
$_SESSION['test'] = 'funcionando';
echo "<p>Sessões: " . ($_SESSION['test'] === 'funcionando' ? "✓" : "✗") . "</p>";

echo "<p>Instalação " . (phpversion() >= '8.1' && $writable ? "✓ SUCESSO" : "✗ FALHOU") . "</p>";
?>
```

## 📞 Suporte

Se encontrar problemas durante a instalação:

1. Verifique os logs de erro do Apache: `/var/log/apache2/error.log`
2. Verifique os logs de erro do PHP
3. Consulte a documentação oficial do PHP e Apache
4. Abra uma issue no repositório do GitHub

---

**Última atualização**: Agosto 2025

