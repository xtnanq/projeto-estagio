<?php
session_start();

require_once '../config/database.php';
require_once '../includes/functions.php';

if (!estaLogado()) {
    header("Location: ../login.php");
    exit;
}

if (!eCliente()) {
    header("Location: ../admin/dashboard.php");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

$sql = "SELECT * FROM empresas WHERE usuario_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$empresa = $result->fetch_assoc();
$stmt->close();

if (!$empresa) {
    $_SESSION['error_message'] = "Não foi encontrada nenhuma empresa associada à sua conta.";
    header("Location: ../login.php");
    exit;
}

$empresa_id = $empresa['id'];

include '../includes/header.php';
include '../admin/includes/header_admin.php';
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<link rel="stylesheet" href="/projeto/css/empresa_dashboard.css">

<div class="white-background">
    <div class="container-fluid">
        <div class="header-container">
            <div class="logo-container">
                <img src="../imagens/Logotipo_freebox.png" style="height:75px;">
            </div>

            <div class="title-container">
                <h4><?= htmlspecialchars($empresa['nome_empresa']); ?></h4>
            </div>

            <div class="buttons-container">
                <a href="../logout.php" class="btn btn-danger">
                    <i class="fas fa-power-off"></i> Logout
                </a>
            </div>
        </div>
    </div>
</div>

<div class="separator"></div>

<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-10">

            <div class="card dashboard-main-card">
                <div class="card-body">

                    <h3 class="text-center mb-4">
                        <i class="fas fa-house"></i> Dashboard
                    </h3>

                    <p class="text-center text-muted mb-5">
                        Escolhe o que queres configurar na tua empresa.
                    </p>

                    <div class="row g-4">

                        <div class="col-md-6 col-lg-4">
                            <a href="empresa_informacoes.php?id=<?= $empresa_id; ?>" class="dashboard-card">
                                <div class="icon-box icon-info">
                                    <i class="fas fa-circle-info"></i>
                                </div>
                                <h5>Informações</h5>
                                <p>Editar dados gerais da empresa.</p>
                            </a>
                        </div>

                        <div class="col-md-6 col-lg-4">
                            <a href="empresa_servicos.php?id=<?= $empresa_id; ?>" class="dashboard-card">
                                <div class="icon-box icon-servicos">
                                    <i class="fas fa-handshake"></i>
                                </div>
                                <h5>Serviços</h5>
                                <p>Adicionar ou editar serviços.</p>
                            </a>
                        </div>

                        <div class="col-md-6 col-lg-4">
                            <a href="empresa_portfolio.php?id=<?= $empresa_id; ?>" class="dashboard-card">
                                <div class="icon-box icon-portfolio">
                                    <i class="fas fa-images"></i>
                                </div>
                                <h5>Portfólio</h5>
                                <p>Gerir imagens e trabalhos.</p>
                            </a>
                        </div>

                        <div class="col-md-6 col-lg-4">
                            <a href="empresa_website.php?id=<?= $empresa_id; ?>" class="dashboard-card">
                                <div class="icon-box icon-website">
                                    <i class="fas fa-globe"></i>
                                </div>
                                <h5>Website</h5>
                                <p>Configurar capa, logo e redes sociais.</p>
                            </a>
                        </div>

                        <div class="col-md-6 col-lg-4">
                            <a href="/projeto/freebox/index.php?id=<?= $empresa_id; ?>" target="_blank" class="dashboard-card">
                                <div class="icon-box icon-ver-website">
                                    <i class="fas fa-eye"></i>
                                </div>
                                <h5>Ver Website</h5>
                                <p>Abrir o site público da empresa.</p>
                            </a>
                        </div>

                        <div class="col-md-6 col-lg-4">
                            <a href="editar_conta.php" class="dashboard-card">
                                <div class="icon-box icon-conta">
                                    <i class="fas fa-user-gear"></i>
                                </div>
                                <h5>Editar Conta</h5>
                                <p>Alterar nome, email e palavra-passe.</p>
                            </a>
                        </div>

                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>