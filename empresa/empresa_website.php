<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Verificar se o ID da empresa foi passado via GET
if (isset($_GET['id'])) {
    $empresa_id = intval($_GET['id']);
} else {
    header("Location: ../admin/dashboard.php");
    exit();
}

// Buscar informações da empresa
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

// Buscar dados do website da BD
$website_sql = "SELECT * FROM website_config WHERE empresa_id = ?";
$website_stmt = $conn->prepare($website_sql);
$website_stmt->bind_param("i", $empresa_id);
$website_stmt->execute();
$website_result = $website_stmt->get_result();
$website = $website_result->fetch_assoc();
$website_stmt->close();

// Processar o formulário quando enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $descricao_empresa = trim($_POST['descricao_empresa'] ?? '');
    $link_facebook     = trim($_POST['link_facebook'] ?? '');
    $link_instagram    = trim($_POST['link_instagram'] ?? '');
    $link_x            = trim($_POST['link_x'] ?? '');

    $logotipo     = $website['logotipo'] ?? '';
    $capa_empresa = $website['capa_empresa'] ?? '';

    // Upload do logotipo
    if (isset($_FILES['logotipo']) && $_FILES['logotipo']['error'] == UPLOAD_ERR_OK) {
        $allowed   = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $file_type = mime_content_type($_FILES['logotipo']['tmp_name']);

        if (!in_array($file_type, $allowed)) {
            $_SESSION['error_message'] = "Logotipo: apenas imagens JPG, PNG, GIF ou WEBP são permitidas.";
            header("Location: empresa_website.php?id=" . $empresa_id . "&show_message=1");
            exit();
        }

        if ($_FILES['logotipo']['size'] > 2 * 1024 * 1024) {
            $_SESSION['error_message'] = "Logotipo: o ficheiro não pode ter mais de 2MB.";
            header("Location: empresa_website.php?id=" . $empresa_id . "&show_message=1");
            exit();
        }

        $upload_dir = '../imagens/' . $empresa_id . '/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $ext      = pathinfo($_FILES['logotipo']['name'], PATHINFO_EXTENSION);
        $logotipo = $upload_dir . 'logotipo.' . $ext;
        move_uploaded_file($_FILES['logotipo']['tmp_name'], $logotipo);
    }

    // Upload da capa
    if (isset($_FILES['capa_empresa']) && $_FILES['capa_empresa']['error'] == UPLOAD_ERR_OK) {
        $allowed   = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $file_type = mime_content_type($_FILES['capa_empresa']['tmp_name']);

        if (!in_array($file_type, $allowed)) {
            $_SESSION['error_message'] = "Capa: apenas imagens JPG, PNG, GIF ou WEBP são permitidas.";
            header("Location: empresa_website.php?id=" . $empresa_id . "&show_message=1");
            exit();
        }

        if ($_FILES['capa_empresa']['size'] > 5 * 1024 * 1024) {
            $_SESSION['error_message'] = "Capa: o ficheiro não pode ter mais de 5MB.";
            header("Location: empresa_website.php?id=" . $empresa_id . "&show_message=1");
            exit();
        }

        $upload_dir = '../imagens/' . $empresa_id . '/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $ext          = pathinfo($_FILES['capa_empresa']['name'], PATHINFO_EXTENSION);
        $capa_empresa = $upload_dir . 'capa.' . $ext;
        move_uploaded_file($_FILES['capa_empresa']['tmp_name'], $capa_empresa);
    }

    // Inserir ou atualizar
    $save_sql = "INSERT INTO website_config (empresa_id, descricao_empresa, logotipo, capa_empresa, link_facebook, link_instagram, link_x)
                 VALUES (?, ?, ?, ?, ?, ?, ?)
                 ON DUPLICATE KEY UPDATE
                 descricao_empresa = VALUES(descricao_empresa),
                 logotipo          = VALUES(logotipo),
                 capa_empresa      = VALUES(capa_empresa),
                 link_facebook     = VALUES(link_facebook),
                 link_instagram    = VALUES(link_instagram),
                 link_x            = VALUES(link_x)";

    $save_stmt = $conn->prepare($save_sql);
    $save_stmt->bind_param("issssss", $empresa_id, $descricao_empresa, $logotipo, $capa_empresa, $link_facebook, $link_instagram, $link_x);

    if ($save_stmt->execute()) {
        $_SESSION['success_message'] = "Configurações do website guardadas com sucesso!";
    } else {
        $_SESSION['error_message'] = "Erro ao guardar configurações: " . $conn->error;
    }
    $save_stmt->close();

    header("Location: empresa_website.php?id=" . $empresa_id . "&show_message=1");
    exit();
}

include '../includes/header.php';
include '../admin/includes/header_admin.php';
?>

<style>
    .button-container_left {
        display: flex;
        justify-content: flex-end;
    }
    .button-container_left .btn {
        margin-left: 10px;
    }
    .preview-img {
        max-width: 200px;
        max-height: 100px;
        object-fit: contain;
        margin-top: 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 4px;
    }
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0; top: 0;
        width: 100%; height: 100%;
        background-color: rgba(0,0,0,0.4);
    }
    .modal-content {
        background-color: #fefefe;
        margin: 15% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
        max-width: 500px;
        text-align: center;
    }
    #okButton { margin-top: 20px; }
</style>

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
                <a href="../logout.php" class="btn btn-outline-danger custom-logout" id="logoutBtn">
                    <i class="fas fa-power-off"></i> Logout
                </a>
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
                    <a class="nav-link" href="empresa_informacoes.php?id=<?php echo $empresa_id; ?>">
                        <i class="fas fa-circle-info"></i> Informações
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="empresa_servicos.php?id=<?php echo $empresa_id; ?>">
                        <i class="fas fa-handshake"></i> Serviços
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="empresa_portfolio.php?id=<?php echo $empresa_id; ?>">
                        <i class="fas fa-image"></i> Portfólio
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="empresa_website.php?id=<?php echo $empresa_id; ?>">
                        <i class="fas fa-globe"></i> Website
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/projeto/admin/dashboard.php">
                        <i class="fas fa-house"></i> Dashboard
                    </a>
                </li>
            </ul>
        </div>

        <div class="col-md-9">
            <div class="config-section">
                <div class="text-center">
                    <h4>Configurações do Website</h4>
                </div>

                <form method="POST" action="" enctype="multipart/form-data">

                    <div class="form-group mt-4">
                        <label for="endereco_url"><i class="fas fa-globe"></i> Endereço URL</label>
                        <input type="text" class="form-control" id="endereco_url" readonly
                               value="<?php echo htmlspecialchars($empresa['nome_empresa']); ?>.projeto.pt">
                    </div>

                    <div class="form-group mt-4">
                        <label for="logotipo"><i class="fas fa-image"></i> Logotipo da Empresa</label>
                        <?php if (!empty($website['logotipo']) && file_exists($website['logotipo'])): ?>
                            <div>
                                <img src="<?php echo htmlspecialchars($website['logotipo']); ?>"
                                     alt="Logotipo atual" class="preview-img">
                                <small class="text-muted d-block">Logotipo atual</small>
                            </div>
                        <?php endif; ?>
                        <input type="file" class="form-control-file mt-2" id="logotipo" name="logotipo"
                               accept="image/jpeg,image/png,image/gif,image/webp">
                        <small class="text-muted">JPG, PNG, GIF ou WEBP. Máx. 2MB.</small>
                    </div>

                    <div class="form-group mt-4">
                        <label for="capa_empresa"><i class="fas fa-panorama"></i> Imagem de Capa</label>
                        <?php if (!empty($website['capa_empresa']) && file_exists($website['capa_empresa'])): ?>
                            <div>
                                <img src="<?php echo htmlspecialchars($website['capa_empresa']); ?>"
                                     alt="Capa atual" class="preview-img">
                                <small class="text-muted d-block">Capa atual</small>
                            </div>
                        <?php endif; ?>
                        <input type="file" class="form-control-file mt-2" id="capa_empresa" name="capa_empresa"
                               accept="image/jpeg,image/png,image/gif,image/webp">
                        <small class="text-muted">JPG, PNG, GIF ou WEBP. Máx. 5MB.</small>
                    </div>

                    <div class="form-group mt-4">
                        <label for="descricao_empresa"><i class="fas fa-align-left"></i> Descrição da Empresa</label>
                        <textarea class="form-control" id="descricao_empresa" name="descricao_empresa"
                                  rows="4"><?php echo htmlspecialchars($website['descricao_empresa'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-group mt-4">
                        <label for="link_facebook"><i class="fab fa-facebook"></i> Facebook</label>
                        <input type="url" class="form-control" id="link_facebook" name="link_facebook"
                               placeholder="https://facebook.com/suaempresa"
                               value="<?php echo htmlspecialchars($website['link_facebook'] ?? ''); ?>">
                    </div>

                    <div class="form-group mt-4">
                        <label for="link_instagram"><i class="fab fa-instagram"></i> Instagram</label>
                        <input type="url" class="form-control" id="link_instagram" name="link_instagram"
                               placeholder="https://instagram.com/suaempresa"
                               value="<?php echo htmlspecialchars($website['link_instagram'] ?? ''); ?>">
                    </div>

                    <div class="form-group mt-4">
                        <label for="link_x"><i class="fab fa-x-twitter"></i> X (Twitter)</label>
                        <input type="url" class="form-control" id="link_x" name="link_x"
                               placeholder="https://x.com/suaempresa"
                               value="<?php echo htmlspecialchars($website['link_x'] ?? ''); ?>">
                    </div>

                    <div class="form-group mt-4">
                        <label><i class="fas fa-map-marker-alt"></i> Localização</label>
                        <?php if (!empty($empresa['morada'])): ?>
                            <iframe src="https://maps.google.com/maps?q=<?php echo urlencode($empresa['morada']); ?>&output=embed"
                                    width="100%" height="300" style="border:0; border-radius:8px;"
                                    allowfullscreen="" loading="lazy"></iframe>
                        <?php else: ?>
                            <p class="text-muted">Preencha a morada nas
                                <a href="empresa_informacoes.php?id=<?php echo $empresa_id; ?>">Informações da Empresa</a>
                                para ver o mapa.
                            </p>
                        <?php endif; ?>
                    </div>

                    <div class="button-container_left mt-4">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Guardar Configurações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal de mensagem -->
<div id="messageModal" class="modal">
    <div class="modal-content">
        <h2 id="modalTitle"></h2>
        <p id="modalMessage"></p>
        <button id="okButton" class="btn btn-success">Ok</button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('show_message') === '1') {
        var modal      = document.getElementById('messageModal');
        var modalTitle = document.getElementById('modalTitle');
        var modalMsg   = document.getElementById('modalMessage');
        var okButton   = document.getElementById('okButton');

        <?php if (isset($_SESSION['success_message'])): ?>
            modalTitle.textContent = 'Sucesso';
            modalMsg.textContent   = '<?php echo addslashes($_SESSION['success_message']); ?>';
            <?php unset($_SESSION['success_message']); ?>
        <?php elseif (isset($_SESSION['error_message'])): ?>
            modalTitle.textContent = 'Erro';
            modalMsg.textContent   = '<?php echo addslashes($_SESSION['error_message']); ?>';
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        modal.style.display = 'block';

        okButton.onclick = function () {
            modal.style.display = 'none';
            window.history.replaceState({}, document.title,
                window.location.pathname + '?id=<?php echo $empresa_id; ?>');
        };
    }
});
</script>

<?php include '../includes/footer.php'; ?>