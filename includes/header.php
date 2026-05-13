<?php
// includes/header.php - Cabeçalho comum para todas as páginas

// Iniciar a sessão se ainda não estiver iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'FreeBox'; ?></title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <style>
        body {
            background-color:rgb(134, 194, 243);
        }
        .login-container {
            max-width: 450px;
            margin: 100px auto;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: #356096;
            color: white;
            padding: 20px;
            border-radius: 10px 10px 0 0;
        }
        .btn-primary {
            background-color: #377E47;
            border-color: #377E47;
        }
        .btn-primary:hover {
            background-color:rgb(35, 94, 49);
            border-color: rgb(35, 94, 49);
        }
    </style>
</head>
<body>
    <!-- Conteúdo principal começará aqui -->