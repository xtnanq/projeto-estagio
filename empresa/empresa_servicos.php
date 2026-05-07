<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

if (isset($_GET['id'])) {
    $empresa_id = intval($_GET['id']);
} else {
    header("Location: dashboard.php");
    exit();
}

// Empresa
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

// Serviços
$servicos_sql = "SELECT * FROM servicos WHERE empresa_id = ?";
$servicos_stmt = $conn->prepare($servicos_sql);
$servicos_stmt->bind_param("i", $empresa_id);
$servicos_stmt->execute();
$servicos_result = $servicos_stmt->get_result();

include '../includes/header.php';
include '../admin/includes/header_admin.php';
?>

<link rel="stylesheet" href="/projeto/css/empresa_servicos.css">

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

                    <div class="text-center">
                        <h4><i class="fas fa-briefcase"></i> Serviços</h4>
                    </div>

                    <div class="mt-4">
                        <button id="mostrarFormularioServico" class="btn btn-primary">
                            Adicionar Serviço
                        </button>
                    </div>

                    <!-- FORM -->
                    <div class="card mt-4" id="formularioServico" style="display:none;">
                        <div class="card-body">
                            <form method="POST" action="adicionar_servico.php">
                                <input type="hidden" name="empresa_id" value="<?= $empresa_id; ?>">

                                <input type="text" name="nome_servico" class="form-control mt-2" placeholder="Nome" required>
                                <input type="text" name="titulo_servico" class="form-control mt-2" placeholder="Título" required>
                                <textarea name="descricao_servico" class="form-control mt-2" placeholder="Descrição"></textarea>

                                <div class="button-container_left mt-3">
                                    <button type="button" id="cancelarFormulario" class="btn btn-secondary">
                                        Cancelar
                                    </button>
                                    <button type="submit" class="btn btn-success">
                                        Guardar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- LISTA -->
                    <?php while ($s = $servicos_result->fetch_assoc()): ?>
                        <div class="card mt-3">
                            <div class="card-body d-flex justify-content-between">
                                <span><?= htmlspecialchars($s['nome_servico']); ?></span>

                                <div>
                                    <a href="editar_servico.php?id=<?= $s['id']; ?>" class="btn btn-success btn-sm">
                                        Editar
                                    </a>
                                    <a href="eliminar_servico.php?id=<?= $s['id']; ?>" class="btn btn-danger btn-sm">
                                        Eliminar
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>

                </div>
            </div>
        </div>

    </div>
</div>

<!-- MODAL -->
<div id="messageModal" class="modal">
    <div class="modal-content">
        <h4 id="modalTitle"></h4>
        <p id="modalMessage"></p>
        <button id="okButton" class="btn btn-success">OK</button>
    </div>
</div>

<script>
document.getElementById('mostrarFormularioServico').onclick = () =>
    document.getElementById('formularioServico').style.display = 'block';

document.getElementById('cancelarFormulario').onclick = () =>
    document.getElementById('formularioServico').style.display = 'none';
</script>

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
            message.textContent = "<?= htmlspecialchars($_SESSION['success_message'], ENT_QUOTES); ?>";
            <?php unset($_SESSION['success_message']); ?>
        <?php elseif (isset($_SESSION['error_message'])): ?>
            title.textContent = "Erro";
            message.textContent = "<?= htmlspecialchars($_SESSION['error_message'], ENT_QUOTES); ?>";
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