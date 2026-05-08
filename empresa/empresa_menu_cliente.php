<?php
if (!isset($empresa_id) && isset($_GET['id'])) {
    $empresa_id = intval($_GET['id']);
}
if (!isset($empresa_id)) {
    $empresa_id = null;
}
?>

<link rel="stylesheet" href="/projeto/css/empresa_menu.css">

<div class="sidebar-config">
    <div class="sidebar-header">
        <span>Configurações</span>
    </div>

    <ul class="nav flex-column">

        <li class="nav-item">
            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>"
                href="/projeto/empresa/dashboard.php">
                <span class="nav-icon"><i class="fas fa-house"></i></span>
                Dashboard
            </a>
        </li>

        <li class="nav-item"><div class="nav-divider"></div></li>

        <li class="nav-item">
            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'empresa_informacoes.php' ? 'active' : ''; ?>"
                href="empresa_informacoes.php?id=<?= $empresa_id; ?>">
                <span class="nav-icon"><i class="fas fa-circle-info"></i></span>
                Informações
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'empresa_servicos.php' ? 'active' : ''; ?>"
                href="empresa_servicos.php?id=<?= $empresa_id; ?>">
                <span class="nav-icon"><i class="fas fa-handshake"></i></span>
                Serviços
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'empresa_portfolio.php' ? 'active' : ''; ?>"
                href="empresa_portfolio.php?id=<?= $empresa_id; ?>">
                <span class="nav-icon"><i class="fas fa-image"></i></span>
                Portfólio
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'empresa_website.php' ? 'active' : ''; ?>"
                href="empresa_website.php?id=<?= $empresa_id; ?>">
                <span class="nav-icon"><i class="fas fa-globe"></i></span>
                Website
            </a>
        </li>

        <li class="nav-item"><div class="nav-divider"></div></li>

        <li class="nav-item">
            <?php
            if ($empresa_id) {
                global $conn;
                $url_stmt = $conn->prepare("SELECT url_site FROM website_config WHERE empresa_id = ?");
                $url_stmt->bind_param("i", $empresa_id);
                $url_stmt->execute();
                $url_result = $url_stmt->get_result()->fetch_assoc();
                $url_stmt->close();
                $url_site = $url_result['url_site'] ?? '';
            } else {
                $url_site = '';
            }
            if (!empty($url_site)): ?>
                <a class="nav-link"
                    href="/projeto/freebox/<?= htmlspecialchars($url_site); ?>"
                    target="_blank">
                    <span class="nav-icon"><i class="fas fa-eye"></i></span>
                    Ver Website
                </a>
            <?php else: ?>
                <a class="nav-link text-muted" style="cursor: default;">
                    <span class="nav-icon"><i class="fas fa-eye-slash"></i></span>
                    Ver Website
                    <small class="d-block" style="font-size:0.75rem;">Defina o endereço primeiro</small>
                </a>
            <?php endif; ?>
        </li>

    </ul>
</div>