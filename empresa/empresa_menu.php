<?php
// empresa_menu.php — Menu lateral reutilizável
// Inclui assim em cada página: include 'empresa_menu.php';
// A variável $empresa_id tem de estar definida antes do include
?>

<link rel="stylesheet" href="/projeto/css/empresa_menu.css">

<div class="sidebar-config">
    <div class="sidebar-header">
        <i class="fas fa-sliders"></i>
        <span>Configurações</span>
    </div>

    <ul class="nav flex-column">

        <?php if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin'): ?>
            <li class="nav-item">
                <a class="nav-link" href="/projeto/admin/dashboard.php">
                    <span class="nav-icon"><i class="fas fa-house"></i></span>
                    Dashboard
                    <i class="fas fa-chevron-right nav-arrow"></i>
                </a>
            </li>

            <li class="nav-item">
                <div class="nav-divider"></div>
            </li>
        <?php else: ?>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>"
                   href="dashboard.php">
                    <span class="nav-icon"><i class="fas fa-house"></i></span>
                    Dashboard
                    <i class="fas fa-chevron-right nav-arrow"></i>
                </a>
            </li>

            <li class="nav-item">
                <div class="nav-divider"></div>
            </li>
        <?php endif; ?>

        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'empresa_informacoes.php' ? 'active' : ''; ?>"
               href="empresa_informacoes.php?id=<?php echo $empresa_id; ?>">
                <span class="nav-icon"><i class="fas fa-circle-info"></i></span>
                Informações
                <i class="fas fa-chevron-right nav-arrow"></i>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'empresa_servicos.php' ? 'active' : ''; ?>"
               href="empresa_servicos.php?id=<?php echo $empresa_id; ?>">
                <span class="nav-icon"><i class="fas fa-handshake"></i></span>
                Serviços
                <i class="fas fa-chevron-right nav-arrow"></i>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'empresa_portfolio.php' ? 'active' : ''; ?>"
               href="empresa_portfolio.php?id=<?php echo $empresa_id; ?>">
                <span class="nav-icon"><i class="fas fa-image"></i></span>
                Portfólio
                <i class="fas fa-chevron-right nav-arrow"></i>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'empresa_website.php' ? 'active' : ''; ?>"
               href="empresa_website.php?id=<?php echo $empresa_id; ?>">
                <span class="nav-icon"><i class="fas fa-globe"></i></span>
                Website
                <i class="fas fa-chevron-right nav-arrow"></i>
            </a>
        </li>

        <li class="nav-item">
            <div class="nav-divider"></div>
        </li>

        <li class="nav-item">
            <a class="nav-link"
               href="/projeto/sites/index.php?id=<?php echo $empresa_id; ?>"
               target="_blank">
                <span class="nav-icon"><i class="fas fa-eye"></i></span>
                Ver Website
                <i class="fas fa-up-right-from-square nav-arrow"></i>
            </a>
        </li>

    </ul>
</div>