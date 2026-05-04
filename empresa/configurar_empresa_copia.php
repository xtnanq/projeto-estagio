<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$empresa_id = $_GET['id'];

// Utilize este arquivo para servir de ponte para páginas específicas:
include '../includes/header.php';
include '../admin/includes/header_admin.php';
?>
<div class="white-background">
    <div class="container-fluid">
        <div class="header-container">
            <div class="logo-container">
                <img src="../imagens/Logotipo_freebox.png" alt="Logotipo" style="height: 75px;">
            </div>
            <div class="title-container">
                <h4><?php echo htmlspecialchars($empresa['nome_empresa']); ?></h4>
            </div>
            <div class="buttons-container">
                <a href="../logout.php" class="btn btn-outline-danger custom-logout" id="logoutBtn"><i class="fas fa-power-off"></i> Logout</a>
            </div>
        </div>
    </div>
</div>

<div class="separator"></div>

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-3">
            <h3>Configurações</h3>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="config_informacoes_empresa.php?id=<?php echo $empresa_id; ?>"><i class="fas fa-circle-info"></i> Informações</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="config_servicos_empresa.php?id=<?php echo $empresa_id; ?>"><i class="fas fa-handshake"></i> Serviços</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="config_portfolio_empresa.php?id=<?php echo $empresa_id; ?>"><i class="fas fa-image"></i> Portfólio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="config_website_empresa.php?id=<?php echo $empresa_id; ?>"><i class="fas fa-globe"></i> Website</a>
                </li>
                <li class="nav-item_2">
                    <a class="nav-link_2" href="/projeto/admin/dashboard.php"><i class="fas fa-house"></i> Dashboard</a>
                </li>                
            </ul>
        </div>
        <div class="col-md-9">
            <p>Escolha uma seção no menu para modificar as respectivas configurações.</p>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

