<?php
// empresa_menu.php — Menu lateral reutilizável
// Inclui assim em cada página: include 'empresa_menu.php';
// A variável $empresa_id tem de estar definida antes do include

if (!isset($empresa_id)) {
    $empresa_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
}
?>

<style>
/* ── Sidebar Configurações ── */
.sidebar-config {
    background: #fff;
    border-radius: 16px;
    border: 1px solid #e3eaf3;
    box-shadow: 0 2px 16px rgba(21, 101, 192, 0.07);
    padding: 0;
    overflow: hidden;
}

.sidebar-config .sidebar-header {
    background: linear-gradient(135deg, #1565C0 0%, #1E88E5 70%, #42A5F5 100%);
    padding: 1.2rem 1.25rem 1rem;
    display: flex;
    align-items: center;
    gap: 10px;
}

.sidebar-config .sidebar-header i {
    font-size: 18px;
    color: rgba(255,255,255,0.85);
}

.sidebar-config .sidebar-header span {
    font-size: 16px;
    font-weight: 600;
    color: #fff;
    letter-spacing: -0.2px;
}

.sidebar-config .nav {
    padding: 8px 0;
}

.sidebar-config .nav-item {
    margin: 0;
}

.sidebar-config .nav-link {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 11px 18px;
    color: #374151;
    font-size: 14px;
    font-weight: 500;
    border-radius: 0;
    border-left: 3px solid transparent;
    transition: background 0.15s, color 0.15s, border-color 0.15s;
    text-decoration: none;
}

.sidebar-config .nav-link:hover {
    background: #EFF6FF;
    color: #1565C0;
    text-decoration: none;
}

.sidebar-config .nav-link.active {
    background: #EFF6FF;
    color: #1565C0;
    border-left-color: #1565C0;
    font-weight: 600;
}

.sidebar-config .nav-link .nav-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    flex-shrink: 0;
    transition: background 0.15s;
}

/* Cores dos ícones por item */
.sidebar-config .nav-link[href*="informacoes"] .nav-icon { background: #DBEAFE; color: #1D4ED8; }
.sidebar-config .nav-link[href*="servicos"]    .nav-icon { background: #EDE9FE; color: #6D28D9; }
.sidebar-config .nav-link[href*="portfolio"]   .nav-icon { background: #D1FAE5; color: #065F46; }
.sidebar-config .nav-link[href*="website"]     .nav-icon { background: #CFFAFE; color: #0E7490; }
.sidebar-config .nav-link[href*="dashboard"]   .nav-icon { background: #FEF9C3; color: #854D0E; }

.sidebar-config .nav-link.active .nav-icon,
.sidebar-config .nav-link:hover .nav-icon {
    filter: brightness(0.95);
}

.sidebar-config .nav-divider {
    height: 1px;
    background: #F1F5F9;
    margin: 4px 0;
}

.sidebar-config .nav-link .nav-arrow {
    margin-left: auto;
    font-size: 11px;
    color: #CBD5E1;
    transition: transform 0.15s;
}

.sidebar-config .nav-link:hover .nav-arrow,
.sidebar-config .nav-link.active .nav-arrow {
    color: #93C5FD;
    transform: translateX(2px);
}
</style>

<div class="sidebar-config">
    <div class="sidebar-header">
        <i class="fas fa-sliders"></i>
        <span>Configurações</span>
    </div>
    <ul class="nav flex-column">
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
            <a class="nav-link" href="/projeto/admin/dashboard.php">
                <span class="nav-icon"><i class="fas fa-house"></i></span>
                Dashboard
                <i class="fas fa-chevron-right nav-arrow"></i>
            </a>
        </li>
    </ul>
</div>