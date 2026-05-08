
<link rel="stylesheet" href="/projeto/css/header_cliente.css">

<div class="cliente-header">

    <div class="cliente-header-left">

        <img src="../imagens/Logotipo_freebox.png"
             alt="Logo"
             class="cliente-logo">

        <div class="cliente-header-title">

            <h3>
                <?= htmlspecialchars($_SESSION['nome_admin'] ?? 'Administrador'); ?>
            </h3>

            <span>
                Painel de Administração
            </span>

        </div>

    </div>

    <div class="cliente-header-right">

        <a href="../admin/editar_admin.php" class="btn btn-primary me-2">
            Editar Admin
        </a>

        <a href="../logout.php" class="btn btn-danger">
            Logout
        </a>

    </div>

</div>

<div class="cliente-separator"></div>
