<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Administração</title>
    <link rel="stylesheet" href="/projeto/css/styles.css">
    <!-- Adicione aqui outros arquivos CSS ou JS específicos para a área de administração -->
</head>
<body>
    <header>
        <!-- Adicione aqui elementos comuns do cabeçalho da área de administração -->
        <nav>
            <!-- Adicione aqui links de navegação para a área de administração -->
        </nav>
    <style>
        body {
            background-color: rgb(235, 244, 253);
        }
        .dashboard-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            padding: 20px;
            margin-top: 20px;
            max-width: 900px; /* Reduz a largura máxima */
            margin-left: auto;
            margin-right: auto;
        }
        .admin-buttons {
            text-align: right;
            padding-right: 15px;
        } 
        .top-section {
                background-color: white;
                padding: 20px 0;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .separator {
                height: 2px;
                background-color: #e0e0e0;
                margin: 0;
        }
        .white-background {
                background-color:rgb(225, 238, 252);
        }
        .dashboard-container {
                padding-top: 20px;
        }      
        .table-responsive {
                overflow-x: auto;
        }
        .table td:last-child {
                text-align: right;
                white-space: nowrap;
        }
        .action-buttons {
                display: flex;
                justify-content: flex-end;
                gap: 5px; /* espaço entre os botões */
        }
        .header-container {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 10px 15px;
        }
        .logo-container {
                flex: 1;
        }
        .title-container {
                flex: 1;
                text-align: center;
        }
        .buttons-container {
                flex: 1;
                text-align: right;
        }
        .btn-freebox-blue {
                background-color: #0066cc;
                border-color: #0066cc;
                color: white;
        }

        .btn-freebox-blue:hover {
                background-color: #0052a3;
                border-color: #0052a3;
                color: white;
        }
        .modal-confirm {
                display: none;
                position: fixed;
                z-index: 1000;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0,0,0,0.5);
        }

        .modal-content {
                background-color: #fefefe;
                margin: 15% auto;
                padding: 20px;
                border: 1px solid #888;
                width: 50%;
                max-width: 500px;
                text-align: center;
        }

        .btn-cancel {
                background-color: #6c757d;
                color: white;
        }

        .btn-confirm {
                background-color: #dc3545;
                color: white;
        }
        .dashboard-blue-container {
                background-color: white;
                padding: 20px;
                border-radius: 10px;
        }
        .modal {
                display: none;
                position: fixed;
                z-index: 1000;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0,0,0,0.4);
        }
        .config-section {
                border: 1px solid #ddd;
                border-radius: 5px;
                margin-bottom: 20px;
                background-color: #f9f9f9;
                max-width: 750px;
                margin: 100;
                padding: 20px;
        }
        .nav-link {
                color: #0066cc;
                font-weight: bold;
        }
        .nav-link:hover, .nav-link.active {
                color: #004080;
                background-color: #e6f2ff;
        }
        .nav-link_2 {
                color: #0066cc;
                font-weight: bold;
        }
        .nav-link_2:hover, .nav-link_2.active {
                color: #004080;
                background-color: #e6f2ff;
        }
        .custom-logout {
                background-color: #dc3545 !important; /* Vermelho */
                color: white !important;
                border: none !important;
        }

        .custom-logout:hover,
        .custom-logout:active {
                background-color: #cc0000 !important; /* Vermelho mais escuro */
                color: white !important;
        }

</style>
</head>
<body>
    <!-- Conteúdo principal começará aqui -->