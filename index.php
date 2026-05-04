<?php
session_start();
error_log("Admin index acessado. Sessão: " . print_r($_SESSION, true));
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != 'admin') {
    header("Location: /projeto/login.php");
    exit;
}

// index.php - Página inicial que redireciona para o login ou área do usuário

// Incluir arquivos necessários
require_once 'config/database.php';
require_once 'includes/functions.php';

iniciarSessao();

// Se já estiver logado, redireciona conforme o tipo de usuário
if (estaLogado()) {
    redirecionarUsuario();
} else {
    // Se não estiver logado, redireciona para a página de login
    header("Location: login.php");
    exit;
}
?>