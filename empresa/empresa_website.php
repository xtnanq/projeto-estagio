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

$is_admin = isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin';

$website_sql = "SELECT * FROM website_config WHERE empresa_id = ?";
$website_stmt = $conn->prepare($website_sql);
$website_stmt->bind_param("i", $empresa_id);
$website_stmt->execute();
$website = $website_stmt->get_result()->fetch_assoc();
$website_stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $descricao_empresa = trim($_POST['descricao_empresa'] ?? '');
    $link_facebook     = trim($_POST['link_facebook'] ?? '');
    $link_instagram    = trim($_POST['link_instagram'] ?? '');
    $link_x            = trim($_POST['link_x'] ?? '');
    $hero_titulo       = trim($_POST['hero_titulo'] ?? '');
    $hero_subtitulo    = trim($_POST['hero_subtitulo'] ?? '');
    $hero_botao_texto  = trim($_POST['hero_botao_texto'] ?? '');
    $hero_botao_link   = trim($_POST['hero_botao_link'] ?? '');

    // URL do site só o admin pode mudar
    if ($is_admin) {
        $url_site = trim($_POST['url_site'] ?? '');
        $url_site = preg_replace('/[^a-zA-Z0-9\-]/', '', $url_site);
        $url_site = strtolower($url_site);

        if (!empty($url_site)) {
            $check_sql  = "SELECT id FROM website_config WHERE url_site = ? AND empresa_id != ?";
            $check_stmt = $conn->prepare($check_sql);
            $check_stmt->bind_param("si", $url_site, $empresa_id);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            $check_stmt->close();

            if ($check_result->num_rows > 0) {
                $_SESSION['error_message'] = "Este endereço já está em uso por outra empresa. Escolha outro.";
                header("Location: empresa_website.php?id=$empresa_id&show_message=1");
                exit();
            }
        }
    } else {
        $url_site = $website['url_site'] ?? '';
    }

    $logotipo     = $website['logotipo'] ?? '';
    $capa_empresa = $website['capa_empresa'] ?? '';

    $upload_dir = '../imagens/' . $empresa_id . '/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

    if (!empty($_FILES['logotipo']['tmp_name'])) {
        $allowed   = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $file_type = mime_content_type($_FILES['logotipo']['tmp_name']);
        if (in_array($file_type, $allowed) && $_FILES['logotipo']['size'] <= 2 * 1024 * 1024) {
            $ext      = pathinfo($_FILES['logotipo']['name'], PATHINFO_EXTENSION);
            $logotipo = '/projeto/imagens/' . $empresa_id . '/logotipo.' . $ext;
            move_uploaded_file($_FILES['logotipo']['tmp_name'], $upload_dir . 'logotipo.' . $ext);
        } else {
            $_SESSION['error_message'] = "Logotipo inválido. Use JPG, PNG, GIF ou WEBP até 2MB.";
            header("Location: empresa_website.php?id=$empresa_id&show_message=1");
            exit();
        }
    }

    if (!empty($_FILES['capa_empresa']['tmp_name'])) {
        $allowed   = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $file_type = mime_content_type($_FILES['capa_empresa']['tmp_name']);
        if (in_array($file_type, $allowed) && $_FILES['capa_empresa']['size'] <= 5 * 1024 * 1024) {
            $ext          = pathinfo($_FILES['capa_empresa']['name'], PATHINFO_EXTENSION);
            $capa_empresa = '/projeto/imagens/' . $empresa_id . '/capa.' . $ext;
            move_uploaded_file($_FILES['capa_empresa']['tmp_name'], $upload_dir . 'capa.' . $ext);
        } else {
            $_SESSION['error_message'] = "Capa inválida. Use JPG, PNG, GIF ou WEBP até 5MB.";
            header("Location: empresa_website.php?id=$empresa_id&show_message=1");
            exit();
        }
    }

    $sql = "INSERT INTO website_config 
            (empresa_id, descricao_empresa, logotipo, capa_empresa, hero_titulo, hero_subtitulo, hero_botao_texto, hero_botao_link, link_facebook, link_instagram, link_x, url_site)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
            descricao_empresa = VALUES(descricao_empresa),
            logotipo          = VALUES(logotipo),
            capa_empresa      = VALUES(capa_empresa),
            hero_titulo       = VALUES(hero_titulo),
            hero_subtitulo    = VALUES(hero_subtitulo),
            hero_botao_texto  = VALUES(hero_botao_texto),
            hero_botao_link   = VALUES(hero_botao_link),
            link_facebook     = VALUES(link_facebook),
            link_instagram    = VALUES(link_instagram),
            link_x            = VALUES(link_x),
            url_site          = VALUES(url_site)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssssssssss", $empresa_id, $descricao_empresa, $logotipo, $capa_empresa, $hero_titulo, $hero_subtitulo, $hero_botao_texto, $hero_botao_link, $link_facebook, $link_instagram, $link_x, $url_site);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Guardado com sucesso!";
    } else {
        $_SESSION['error_message'] = "Erro ao guardar: " . $conn->error;
    }

    header("Location: empresa_website.php?id=$empresa_id&show_message=1");
    exit();
}

include '../includes/header.php';
include '../admin/includes/header_admin.php';
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="/projeto/css/empresa_website.css">

<style>
    .modal {
        display: none; position: fixed; z-index: 1000;
        left: 0; top: 0; width: 100%; height: 100%;
        background-color: rgba(0,0,0,0.4);
    }
    .modal-content {
        background-color: #fefefe; margin: 15% auto; padding: 20px;
        border: 1px solid #888; width: 80%; max-width: 500px; text-align: center;
    }
    .preview-img {
        max-width: 200px; max-height: 100px; object-fit: contain;
        margin-top: 8px; border: 1px solid #ddd; border-radius: 4px; padding: 4px;
        display: block;
    }
    .url-group {
        display: flex; align-items: center; gap: 5px; margin-top: 8px;
    }
    .url-prefix {
        background-color: #e9ecef; border: 1px solid #ced4da; border-radius: 4px;
        padding: 6px 12px; white-space: nowrap; color: #495057;
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
                <a href="../logout.php" class="btn btn-danger">Logout</a>
            </div>
        </div>
    </div>
</div>

<div class="separator"></div>

<div class="container-fluid mt-4">
    <div class="row">

        <!-- MENU -->
        <div class="col-md-3 d-flex justify-content-start">
            <?php if ($is_admin): ?>
                <?php include __DIR__ . '/empresa_menu.php'; ?>
            <?php else: ?>
                <?php include __DIR__ . '/empresa_menu_cliente.php'; ?>
            <?php endif; ?>
        </div>

        <!-- CONTEÚDO -->
        <div class="col-md-9">
            <div class="card custom-card">
                <div class="card-body">

                    <h4 class="text-center mb-4">
                        <i class="fas fa-globe"></i> Website
                    </h4>

                    <form method="POST" enctype="multipart/form-data">

                        <!-- URL DO SITE — só admin pode mudar -->
                        <label class="mt-3"><i class="fas fa-link"></i> Endereço do seu site</label>
                        <?php if ($is_admin): ?>
                            <div class="url-group">
                                <span class="url-prefix">http://freebox/</span>
                                <input type="text" name="url_site" class="form-control"
                                    placeholder="nome-do-seu-site"
                                    value="<?= htmlspecialchars($website['url_site'] ?? '') ?>"
                                    maxlength="100">
                            </div>
                            <small class="text-muted">Apenas letras, números e hífens. Ex: <?= htmlspecialchars(strtolower(preg_replace('/[^a-zA-Z0-9\-]/', '-', $empresa['nome_empresa']))); ?></small>
                        <?php else: ?>
                            <div class="url-group">
                                <span class="url-prefix">http://freebox/</span>
                                <input type="text" class="form-control"
                                    value="<?= htmlspecialchars($website['url_site'] ?? '') ?>" disabled>
                            </div>
                            <small class="text-muted">Apenas o administrador pode alterar o endereço do site.</small>
                        <?php endif; ?>

                        <?php if (!empty($website['url_site'])): ?>
                            <div class="mt-2">
                                <a href="http://localhost/projeto/freebox/<?= htmlspecialchars($website['url_site']); ?>"
                                    target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-external-link-alt"></i> Ver site
                                </a>
                            </div>
                        <?php endif; ?>

                        <!-- LOGOTIPO -->
                        <label class="mt-4"><i class="fas fa-image"></i> Logotipo</label>
                        <input type="file" name="logotipo" class="form-control"
                            accept="image/jpeg,image/png,image/gif,image/webp">
                        <small class="text-muted">JPG, PNG, GIF ou WEBP. Máx. 2MB.</small>
                        <?php if (!empty($website['logotipo'])): ?>
                            <img src="<?= htmlspecialchars($website['logotipo']); ?>"
                                alt="Logotipo atual" class="preview-img mt-2">
                        <?php endif; ?>

                        <!-- CAPA -->
                        <label class="mt-4"><i class="fas fa-panorama"></i> Capa</label>
                        <input type="file" name="capa_empresa" class="form-control"
                            accept="image/jpeg,image/png,image/gif,image/webp">
                        <small class="text-muted">JPG, PNG, GIF ou WEBP. Máx. 5MB.</small>
                        <?php if (!empty($website['capa_empresa'])): ?>
                            <img src="<?= htmlspecialchars($website['capa_empresa']); ?>"
                                alt="Capa atual" class="preview-img mt-2">
                        <?php endif; ?>

                        <!-- HERO TEXTO -->
                        <hr class="mt-4">
                        <h6 class="mt-3 mb-3"><i class="fas fa-image"></i> Texto em cima da imagem (opcional)</h6>

                        <label><i class="fas fa-heading"></i> Título</label>
                        <input type="text" name="hero_titulo" class="form-control"
                            placeholder="Ex: Bem-vindo à nossa empresa"
                            value="<?= htmlspecialchars($website['hero_titulo'] ?? '') ?>"
                            maxlength="255">

                        <label class="mt-3"><i class="fas fa-font"></i> Subtítulo</label>
                        <input type="text" name="hero_subtitulo" class="form-control"
                            placeholder="Ex: Qualidade e confiança desde 2010"
                            value="<?= htmlspecialchars($website['hero_subtitulo'] ?? '') ?>"
                            maxlength="255">

                        <label class="mt-3"><i class="fas fa-mouse-pointer"></i> Texto do botão</label>
                        <input type="text" name="hero_botao_texto" class="form-control"
                            placeholder="Ex: Contacte-nos"
                            value="<?= htmlspecialchars($website['hero_botao_texto'] ?? '') ?>"
                            maxlength="100">

                        <label class="mt-3"><i class="fas fa-link"></i> Link do botão</label>
                        <input type="text" name="hero_botao_link" class="form-control"
                            placeholder="Ex: https://... ou #contactos"
                            value="<?= htmlspecialchars($website['hero_botao_link'] ?? '') ?>"
                            maxlength="255">
                        <small class="text-muted">Se deixar tudo em branco, a imagem fica limpa sem texto.</small>
                        <hr>

                        <!-- DESCRIÇÃO -->
                        <label class="mt-3"><i class="fas fa-align-left"></i> Descrição (Sobre Nós)</label>
                        <textarea name="descricao_empresa" class="form-control" rows="4"><?= htmlspecialchars($website['descricao_empresa'] ?? '') ?></textarea>

                        <!-- REDES SOCIAIS -->
                        <label class="mt-4"><i class="fab fa-facebook"></i> Facebook</label>
                        <input type="url" name="link_facebook" class="form-control"
                            placeholder="https://facebook.com/suaempresa"
                            value="<?= htmlspecialchars($website['link_facebook'] ?? '') ?>">

                        <label class="mt-3"><i class="fab fa-instagram"></i> Instagram</label>
                        <input type="url" name="link_instagram" class="form-control"
                            placeholder="https://instagram.com/suaempresa"
                            value="<?= htmlspecialchars($website['link_instagram'] ?? '') ?>">

                        <label class="mt-3"><i class="fab fa-x-twitter"></i> X (Twitter)</label>
                        <input type="url" name="link_x" class="form-control"
                            placeholder="https://x.com/suaempresa"
                            value="<?= htmlspecialchars($website['link_x'] ?? '') ?>">

                        <button type="submit" class="btn btn-success mt-4">
                            Guardar
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
        const modal   = document.getElementById("messageModal");
        const title   = document.getElementById("modalTitle");
        const message = document.getElementById("modalMessage");
        const okBtn   = document.getElementById("okButton");

        <?php if (isset($_SESSION['success_message'])): ?>
            title.textContent   = "Sucesso";
            message.textContent = "<?= htmlspecialchars($_SESSION['success_message'], ENT_QUOTES); ?>";
            <?php unset($_SESSION['success_message']); ?>
        <?php elseif (isset($_SESSION['error_message'])): ?>
            title.textContent   = "Erro";
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