<?php
/**
 * Arquivo de configuração principal
 * E-commerce Project - Versão com Banco de Dados
 */

// Incluir conexão com banco de dados
require_once 'db_connect.php';

// Configurações gerais do site
define('SITE_NAME', 'E-commerce Project');
define('SITE_URL', 'http://localhost/ecommerce_project/');

// Configurações de sessão
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Inicializar carrinho se não existir
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

// Todas as funções agora estão em db_connect.php
// Este arquivo mantém apenas configurações gerais
?>

