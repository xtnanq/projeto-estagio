<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

if (isset($_GET['id'])) {
    $empresa_id = intval($_GET['id']);
} else {
    header("Location: ../admin/dashboard.php");
    exit();
}

// Empresa
$sql = "SELECT * FROM empresas WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $empresa_id);
$stmt->execute();
$result = $stmt->get_result();
$empresa = $result->fetch_assoc();
$stmt->close();

if (!$empresa) {
    header("Location: ../admin/dashboard.php");
    exit();
}

// Website config
$website_sql = "SELECT * FROM website_config WHERE empresa_id = ?";
$website_stmt = $conn->prepare($website_sql);
$website_stmt->bind_param("i", $empresa_id);
$website_stmt->execute();
$website = $website_stmt->get_result()->fetch_assoc();
$website_stmt->close();

/* --------- GUARDAR --------- */
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $descricao_empresa = trim($_POST['descricao_empresa'] ?? '');
    $link_facebook     = trim($_POST['link_facebook'] ?? '');
    $link_instagram    = trim($_POST['link_instagram'] ?? '');
    $link_x            = trim($_POST['link_x'] ?? '');

    $logotipo     = $website['logotipo'] ?? '';
    $capa_empresa = $website['capa_empresa'] ?? '';

    $upload_dir = '../imagens/' . $empresa_id . '/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

    // LOGOTIPO
    if (!empty($_FILES['logotipo']['tmp_name'])) {
        $ext = pathinfo($_FILES['logotipo']['name'], PATHINFO_EXTENSION);
        move_uploaded_file($_FILES['logotipo']['tmp_name'], $upload_dir . 'logotipo.' . $ext);
        $logotipo = '/projeto/imagens/' . $empresa_id . '/logotipo.' . $ext;
    }

    // CAPA
    if (!empty($_FILES['capa_empresa']['tmp_name'])) {
        $ext = pathinfo($_FILES['capa_empresa']['name'], PATHINFO_EXTENSION);
        move_uploaded_file($_FILES['capa_empresa']['tmp_name'], $upload_dir . 'capa.' . $ext);
        $capa_empresa = '/projeto/imagens/' . $empresa_id . '/capa.' . $ext;
    }

    $sql = "INSERT INTO website_config 
            (empresa_id, descricao_empresa, logotipo, capa_empresa, link_facebook, link_instagram, link_x)
            VALUES (?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
            descricao_empresa=VALUES(descricao_empresa),
            logotipo=VALUES(logotipo),
            capa_empresa=VALUES(capa_empresa),
            link_facebook=VALUES(link_facebook),
            link_instagram=VALUES(link_instagram),
            link_x=VALUES(link_x)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issssss", $empresa_id, $descricao_empresa, $logotipo, $capa_empresa, $link_facebook, $link_instagram, $link_x);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Guardado com sucesso!";
    } else {
        $_SESSION['error_message'] = "Erro ao guardar.";
    }

    header("Location: empresa_website.php?id=$empresa_id&show_message=1");
    exit();
}

include '../includes/header.php';
include '../admin/includes/header_admin.php';
?>

<!-- FONT AWESOME -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
.modal {
    display: none;
    position: fixed;
    z-index: 9999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.4);
}

.modal-content {
    background: #fff;
    margin: 15% auto;
    padding: 25px;
    width: 90%;
    max-width: 400px;
    text-align: center;
    border-radius: 12px;
}

.modal-content h4 {
    color: #16a34a;
}

.modal-content p {
    margin-top: 10px;
}

.preview-img {
    width: 120px;
    margin-top: 10px;
    border-radius: 8px;
}
</style>

<!-- HEADER -->
<div class="white-background">
    <div class="container-fluid">
        <div class="header-container">
            <img src="../imagens/Logotipo_freebox.png" style="height:75px;">
            <h4><?= htmlspecialchars($empresa['nome_empresa']); ?></h4>
            <a href="../logout.php" class="btn btn-danger">
                <i class="fas fa-power-off"></i> Logout
            </a>
        </div>
    </div>
</div>

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

                    <h4 class="text-center">
                        <i class="fas fa-globe"></i> Website
                    </h4>

                    <form method="POST" enctype="multipart/form-data">

                        <input type="text" class="form-control mt-3"
                               value="<?= htmlspecialchars($empresa['nome_empresa']); ?>.projeto.pt" readonly>

                        <label class="mt-3"><i class="fas fa-image"></i> Logotipo</label>
                        <input type="file" name="logotipo" class="form-control">
                        <?php if (!empty($website['logotipo'])): ?>
                            <img src="<?= $website['logotipo']; ?>" class="preview-img">
                        <?php endif; ?>

                        <label class="mt-3"><i class="fas fa-image"></i> Capa</label>
                        <input type="file" name="capa_empresa" class="form-control">
                        <?php if (!empty($website['capa_empresa'])): ?>
                            <img src="<?= $website['capa_empresa']; ?>" class="preview-img">
                        <?php endif; ?>

                        <label class="mt-3"><i class="fas fa-align-left"></i> Descrição</label>
                        <textarea name="descricao_empresa" class="form-control"><?= $website['descricao_empresa'] ?? '' ?></textarea>

                        <label class="mt-3"><i class="fab fa-facebook"></i> Facebook</label>
                        <input type="url" name="link_facebook" class="form-control" value="<?= $website['link_facebook'] ?? '' ?>">

                        <label class="mt-2"><i class="fab fa-instagram"></i> Instagram</label>
                        <input type="url" name="link_instagram" class="form-control" value="<?= $website['link_instagram'] ?? '' ?>">

                        <label class="mt-2"><i class="fab fa-x-twitter"></i> X</label>
                        <input type="url" name="link_x" class="form-control" value="<?= $website['link_x'] ?? '' ?>">

                        <button class="btn btn-success mt-4">
                            <i class="fas fa-save"></i> Guardar
                        </button>

                    </form>

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