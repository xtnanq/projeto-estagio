<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

if (isset($_GET['id'])) {
    $empresa_id = $_GET['id'];
} else {
   header("Location: index.php");
   exit();
}

$sql = "SELECT * FROM empresas WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $empresa_id);
$stmt->execute();
$result = $stmt->get_result();
$empresa = $result->fetch_assoc();

if (!$empresa) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome_empresa      = $_POST['nome_empresa'] ?? '';
    $morada            = $_POST['morada'] ?? '';
    $codigo_postal     = $_POST['codigo_postal'] ?? '';
    $telefone          = $_POST['telefone'] ?? '';
    $email_empresa     = $_POST['email_empresa'] ?? '';
    $nome_contato      = $_POST['nome_contato'] ?? '';
    $telefone_contato  = $_POST['telefone_contato'] ?? '';
    $email_contato     = $_POST['email_contato'] ?? '';

    $sql = "UPDATE empresas SET 
        nome_empresa=?, morada=?, codigo_postal=?, telefone=?, email_empresa=?, 
        nome_contato=?, telefone_contato=?, email_contato=? WHERE id=?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssi",
        $nome_empresa, $morada, $codigo_postal, $telefone,
        $email_empresa, $nome_contato, $telefone_contato, $email_contato, $empresa_id
    );

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Informações atualizadas com sucesso!";
    } else {
        $_SESSION['error_message'] = "Erro ao atualizar.";
    }

    header("Location: empresa_informacoes.php?id=$empresa_id&show_message=1");
    exit();
}

include '../includes/header.php';
include '../admin/includes/header_admin.php';
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<link rel="stylesheet" href="/projeto/css/empresa_informacoes.css">

<!-- HEADER -->
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
                     Logout
                </a>
            </div>
        </div>
    </div>
</div>

<div class="separator"></div>

<div class="container-fluid mt-4">
    <div class="row">

        <!-- MENU -->
        <div class="col-md-3">
            <?php include __DIR__ . '/empresa_menu.php'; ?>
        </div>

        <!-- CONTEÚDO -->
        <div class="col-md-9">
            <div class="card custom-card">
                <div class="card-body">

                    <h4 class="text-center mb-4">
                        <i class="fas fa-building"></i> Informações da Empresa
                    </h4>

                    <form method="POST">

                        <div class="form-group">
                            <label><i class="fas fa-building"></i> Nome da Empresa</label>
                            <input type="text" name="nome_empresa" class="form-control"
                                value="<?= htmlspecialchars($empresa['nome_empresa']); ?>">
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-map-marker-alt"></i> Morada</label>
                            <input type="text" name="morada" class="form-control"
                                value="<?= htmlspecialchars($empresa['morada']); ?>">
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-mail-bulk"></i> Código Postal</label>
                            <input type="text" name="codigo_postal" class="form-control"
                                value="<?= htmlspecialchars($empresa['codigo_postal']); ?>">
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-phone"></i> Telefone</label>
                            <input type="text" name="telefone" class="form-control"
                                value="<?= htmlspecialchars($empresa['telefone']); ?>">
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-envelope"></i> Email da Empresa</label>
                            <input type="email" name="email_empresa" class="form-control"
                                value="<?= htmlspecialchars($empresa['email_empresa']); ?>">
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-user"></i> Nome de Contato</label>
                            <input type="text" name="nome_contato" class="form-control"
                                value="<?= htmlspecialchars($empresa['nome_contato']); ?>">
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-phone"></i> Telefone de Contato</label>
                            <input type="text" name="telefone_contato" class="form-control"
                                value="<?= htmlspecialchars($empresa['telefone_contato']); ?>">
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-envelope"></i> Email de Contato</label>
                            <input type="email" name="email_contato" class="form-control"
                                value="<?= htmlspecialchars($empresa['email_contato']); ?>">
                        </div>

                        <div class="text-end mt-4">
                            <button type="submit" class="btn btn-primary custom-btn">
                                <i class="fas fa-save"></i> Guardar Informações
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>

    </div>
</div>

<!-- MODAL -->
<div id="messageModal" class="modal">
    <div class="modal-content">
        <h2 id="modalTitle"></h2>
        <p id="modalMessage"></p>
        <button id="okButton" class="btn btn-success">OK</button>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {

    const urlParams = new URLSearchParams(window.location.search);

    if (urlParams.get("show_message") === "1") {

        const modal = document.getElementById("messageModal");
        const title = document.getElementById("modalTitle");
        const message = document.getElementById("modalMessage");
        const okBtn = document.getElementById("okButton");

        <?php if (isset($_SESSION['success_message'])): ?>
            title.textContent = "Sucesso";
            message.textContent = "<?= $_SESSION['success_message']; ?>";
            <?php unset($_SESSION['success_message']); ?>
        <?php elseif (isset($_SESSION['error_message'])): ?>
            title.textContent = "Erro";
            message.textContent = "<?= $_SESSION['error_message']; ?>";
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        modal.style.display = "block";

        okBtn.onclick = function() {
            modal.style.display = "none";

            window.history.replaceState({}, document.title,
                window.location.pathname + "?id=<?= $empresa_id ?>");
        };
    }

});
</script>

<?php include '../includes/footer.php'; ?>