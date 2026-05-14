<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FreeBox — Painel da Empresa</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/projeto/css/header_cliente.css">
</head>
<body>

<div class="cliente-header">
    <div class="cliente-header-left">
        <img src="../imagens/Logotipo_freebox.png" alt="Logo" class="cliente-logo">
        <div class="cliente-header-title">
            <h3><?= htmlspecialchars($empresa['nome_empresa'] ?? 'Empresa'); ?></h3>
            <span>Painel da Empresa</span>
        </div>
    </div>
    <div class="cliente-header-right">
        <a href="../logout.php" class="btn btn-danger">Logout</a>
    </div>
</div>

<div class="cliente-separator"></div>