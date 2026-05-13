<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!estaLogado()) {
    header("Location: ../login.php");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$sql = "SELECT e.*, wc.url_site FROM empresas e 
        LEFT JOIN website_config wc ON wc.empresa_id = e.id
        WHERE e.usuario_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$empresa = $stmt->get_result()->fetch_assoc();
$stmt->close();

$nome_empresa = $empresa['nome_empresa'] ?? 'Empresa';
$link_sistema = 'http://' . ($_SERVER['HTTP_HOST'] ?? '') . '/projeto/empresa/';
?>

<?php include 'header_cliente.php'; ?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<link rel="stylesheet" href="../css/style.css"> 

<link rel="stylesheet" href="../css/empresa_politica_privacidade.css?v=<?= time(); ?>">

<div class="container-fluid mt-4">
    <div class="dashboard-container">
        <h1 class="privacy-title">Política de Privacidade</h1>
        <div class="privacy-line"></div>

        <div class="privacy-content">
            <h2>Quem somos</h2>
            <p>O endereço do nosso sistema de gestão é: 
                <a href="<?= htmlspecialchars($link_sistema); ?>" target="_blank"><?= htmlspecialchars($link_sistema); ?></a>
            </p>

            <h2>Proteção de Dados Pessoais</h2>
            <p>A proteção dos seus dados pessoais é muito importante para a <strong><?= htmlspecialchars($nome_empresa); ?></strong>.</p>

            <h2>Que dados recolhidos</h2>
            <ul>
                <li>Dados de Registo (Nome, NIF, Contactos)</li>
                <li>Dados de Acesso (Email e Password encriptada)</li>
                <li>Conteúdos enviados (Imagens e Logótipos)</li>
            </ul>

            <h2>Segurança</h2>
            <p>Implementamos medidas técnicas e organizativas adequadas para proteger os seus dados pessoais contra acessos não autorizados.</p>

            <div class="privacy-date">
                Última atualização: <?= date('d/m/Y'); ?>
            </div>
        </div>
    </div>
</div>

<?php include 'footer_cliente.php'; ?>