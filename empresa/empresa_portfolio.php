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

// Buscar empresa
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

/* ---------------- UPLOAD PORTFOLIO ---------------- */
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['adicionar_portfolio'])) {

    $descricao_imagem = $_POST['descricao_imagem'] ?? '';

    if (isset($_FILES['portfolio_imagem']) && $_FILES['portfolio_imagem']['error'] == UPLOAD_ERR_OK) {

        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $file_type = mime_content_type($_FILES['portfolio_imagem']['tmp_name']);

        if (!in_array($file_type, $allowed)) {
            $_SESSION['error_message'] = "Apenas imagens JPG, PNG, GIF ou WEBP são permitidas.";
            header("Location: empresa_portfolio.php?id=$empresa_id&show_message=1");
            exit();
        }

        if ($_FILES['portfolio_imagem']['size'] > 5 * 1024 * 1024) {
            $_SESSION['error_message'] = "O ficheiro não pode ter mais de 5MB.";
            header("Location: empresa_portfolio.php?id=$empresa_id&show_message=1");
            exit();
        }

        $upload_dir = '../imagens/' . $empresa_id . '/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $ext = pathinfo($_FILES['portfolio_imagem']['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $ext;
        $uploaded_file = '/projeto/imagens/' . $empresa_id . '/' . $filename;

        if (move_uploaded_file($_FILES['portfolio_imagem']['tmp_name'], $upload_dir . $filename)) {

            $insert_sql = "INSERT INTO portfolio (empresa_id, imagem, descricao_imagem) VALUES (?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("iss", $empresa_id, $uploaded_file, $descricao_imagem);

            if ($insert_stmt->execute()) {
                $_SESSION['success_message'] = "Imagem adicionada com sucesso!";
            } else {
                $_SESSION['error_message'] = "Erro ao guardar no banco de dados.";
            }

            $insert_stmt->close();
        } else {
            $_SESSION['error_message'] = "Erro ao fazer upload.";
        }
    } else {
        $_SESSION['error_message'] = "Erro no upload da imagem.";
    }

    header("Location: empresa_portfolio.php?id=$empresa_id&show_message=1");
    exit();
}

/* ---------------- PORTFÓLIO ---------------- */
$portfolio_sql = "SELECT * FROM portfolio WHERE empresa_id = ?";
$portfolio_stmt = $conn->prepare($portfolio_sql);
$portfolio_stmt->bind_param("i", $empresa_id);
$portfolio_stmt->execute();
$portfolio_result = $portfolio_stmt->get_result();
$portfolio_stmt->close();

include '../includes/header.php';
include '../admin/includes/header_admin.php';
?>

<!-- FONT AWESOME -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="/projeto/css/empresa_portfolio.css">
<link rel="stylesheet" href="/projeto/css/empresa_menu.css">

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
                <a href="../logout.php" class="btn btn-outline-danger">
                    <i class="fas fa-power-off"></i> Logout
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
                        <h4><i class="fas fa-images"></i> Portfólio</h4>
                    </div>

                    <div class="mt-4">
                        <button id="mostrarFormularioPortfolio" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Adicionar Imagem
                        </button>
                    </div>

                    <!-- FORM -->
                    <div class="card mt-4" id="formularioPortfolio" style="display:none;">
                        <div class="card-body">
                            <h5><i class="fas fa-upload"></i> Nova Imagem</h5>

                            <form method="POST" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label><i class="fas fa-image"></i> Imagem</label>
                                    <input type="file" name="portfolio_imagem" class="form-control" required>
                                </div>

                                <div class="form-group mt-3">
                                    <label><i class="fas fa-align-left"></i> Descrição</label>
                                    <textarea name="descricao_imagem" class="form-control"></textarea>
                                </div>

                                <div class="button-container_left mt-4">
                                    <button type="button" id="cancelarFormularioPortfolio" class="btn btn-secondary">
                                        Cancelar
                                    </button>
                                    <button type="submit" name="adicionar_portfolio" class="btn btn-success">
                                        <i class="fas fa-save"></i> Guardar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- LISTA -->
                    <?php while ($p = $portfolio_result->fetch_assoc()): ?>
                        <div class="card mt-3">
                            <div class="card-body">
                                <img src="<?= htmlspecialchars($p['imagem']); ?>" class="img-thumbnail">

                                <div class="d-flex justify-content-between mt-2">
                                    <p><?= htmlspecialchars($p['descricao_imagem']); ?></p>

                                    <div>
                                        <a href="editar_portfolio.php?id=<?= $p['id']; ?>" class="btn btn-success btn-sm">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>
                                        <a href="eliminar_portfolio.php?id=<?= $p['id']; ?>" class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash"></i> Eliminar
                                        </a>
                                    </div>
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

<!-- Dados PHP passados ao JS via data attributes -->
<div id="php-data"
    data-show-message="<?= isset($_GET['show_message']) && $_GET['show_message'] === '1' ? '1' : '0' ?>"
    data-success="<?= isset($_SESSION['success_message']) ? htmlspecialchars($_SESSION['success_message'], ENT_QUOTES) : '' ?>"
    data-error="<?= isset($_SESSION['error_message']) ? htmlspecialchars($_SESSION['error_message'], ENT_QUOTES) : '' ?>"
    data-empresa-id="<?= $empresa_id ?>"
    style="display:none;">
</div>

<?php
unset($_SESSION['success_message']);
unset($_SESSION['error_message']);
?>

<script src="/projeto/js/empresa_portfolio.js"></script>

<?php include '../includes/footer.php'; ?>