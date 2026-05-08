
<link rel="stylesheet" href="/projeto/css/header_cliente.css">

<div class="cliente-header">

    <div class="cliente-header-left">

        <img src="../imagens/Logotipo_freebox.png"
             alt="Logo"
             class="cliente-logo">

        <div class="cliente-header-title">

            <h3>
                <?= htmlspecialchars($empresa['nome_empresa'] ?? 'Empresa'); ?>
            </h3>

            <span>
                Painel da Empresa
            </span>

        </div>

    </div>

    <div class="cliente-header-right">

        <a href="../logout.php" class="btn btn-danger">

        

            Logout

        </a>

    </div>

</div>

<div class="cliente-separator"></div>

