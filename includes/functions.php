<?php
// includes/functions.php - Funções auxiliares para o sistema de login

// Inicia a sessão se ainda não estiver iniciada
function iniciarSessao() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
}

// Verifica se o usuário está logado
function estaLogado() {
    iniciarSessao();
    return isset($_SESSION['usuario_id']);
}

// Verifica se o usuário é administrador
function eAdmin() {
    iniciarSessao();
    return isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] == 'admin';
}

// Verifica se o usuário é cliente
function eCliente() {
    iniciarSessao();
    return isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] == 'cliente';
}

// Redireciona para página adequada conforme o tipo de usuário
function redirecionarUsuario() {
    iniciarSessao();
    
    if (!estaLogado()) {
        header("Location: login.php");
        exit;
    }
    
    if (eAdmin()) {
        header("Location: admin/index.php");
        exit;
    }
    
    if (eCliente()) {
        header("Location: empresa/index.php");
        exit;
    }
}

// Limpa e valida dados de entrada
function limparDados($dados) {
    $dados = trim($dados);
    $dados = stripslashes($dados);
    $dados = htmlspecialchars($dados);
    return $dados;
}

// Exibe mensagens de alerta/erro
function mostrarAlerta($mensagem, $tipo = 'success') {
    $_SESSION['alerta'] = [
        'mensagem' => $mensagem,
        'tipo' => $tipo
    ];
}

// Recupera e limpa alertas da sessão
function obterAlerta() {
    iniciarSessao();
    if (isset($_SESSION['alerta'])) {
        $alerta = $_SESSION['alerta'];
        unset($_SESSION['alerta']);
        return $alerta;
    }
    return null;
}
?>