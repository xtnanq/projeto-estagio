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

<style>
.dashboard-card {
    background: #fff;
    border-radius: 18px;
    padding: 34px 24px;
    text-align: center;
    border: 1px solid #e3eaf3;
    box-shadow: 0 8px 25px rgba(0,0,0,0.07);
    transition: 0.2s ease;
    height: 100%;
    text-decoration: none;
    color: #111827;
    display: block;
}

.dashboard-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 30px rgba(0,0,0,0.12);
    text-decoration: none;
    color: #1565C0;
}

.dashboard-card .icon-box {
    width: 70px;
    height: 70px;
    margin: 0 auto 18px;
    border-radius: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
}

.icon-info {
    background: #DBEAFE;
    color: #1D4ED8;
}

.icon-servicos {
    background: #EDE9FE;
    color: #6D28D9;
}

.icon-portfolio {
    background: #D1FAE5;
    color: #065F46;
}

.icon-website {
    background: #CFFAFE;
    color: #0E7490;
}

.icon-conta {
    background: #FEF3C7;
    color: #92400E;
}

.dashboard-card h5 {
    font-weight: 700;
    margin-bottom: 8px;
}

.dashboard-card p {
    color: #6B7280;
    margin: 0;
    font-size: 14px;
}

.dashboard-main-card {
    border: none;
    border-radius: 16px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    background: #ffffff;
    padding: 25px;
}
</style>

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