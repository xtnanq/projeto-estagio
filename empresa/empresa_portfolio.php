<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

if(isset($_GET['id'])) {
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

if(!$empresa) {
    header("Location: ../admin/dashboard.php");
    exit();
}

// Processar upload de imagem
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['adicionar_portfolio'])) {
    $descricao_imagem = $_POST['descricao_imagem'] ?? '';

    if (isset($_FILES['portfolio_imagem']) && $_FILES['portfolio_imagem']['error'] == UPLOAD_ERR_OK) {
        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $file_type = mime_content_type($_FILES['portfolio_imagem']['tmp_name']);

        if (!in_array($file_type, $allowed)) {
            $_SESSION['error_message'] = "Apenas imagens JPG, PNG, GIF ou WEBP são permitidas.";
            header("Location: empresa_portfolio.php?id=" . $empresa_id . "&show_message=1");
            exit();
        }

        if ($_FILES['portfolio_imagem']['size'] > 5 * 1024 * 1024) {
            $_SESSION['error_message'] = "O ficheiro não pode ter mais de 5MB.";
            header("Location: empresa_portfolio.php?id=" . $empresa_id . "&show_message=1");
            exit();
        }

        $upload_dir = 'imagens/' . $empresa_id . '/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

        $ext = pathinfo($_FILES['portfolio_imagem']['name'], PATHINFO_EXTENSION);
        $uploaded_file = $upload_dir . uniqid() . '.' . $ext;

        if (move_uploaded_file($_FILES['portfolio_imagem']['tmp_name'], $uploaded_file)) {
            $insert_sql = "INSERT INTO portfolio (empresa_id, imagem, descricao_imagem) VALUES (?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("iss", $empresa_id, $uploaded_file, $descricao_imagem);

            if ($insert_stmt->execute()) {
                $_SESSION['success_message'] = "Imagem adicionada com sucesso!";
            } else {
                $_SESSION['error_message'] = "Erro ao adicionar imagem.";
            }
            $insert_stmt->close();
        } else {
            $_SESSION['error_message'] = "Erro ao fazer upload.";
        }
    } else {
        $_SESSION['error_message'] = "Erro no upload da imagem.";
    }

    header("Location: empresa_portfolio.php?id=" . $empresa_id . "&show_message=1");
    exit();
}

// Buscar imagens do portfólio
$portfolio_sql = "SELECT * FROM portfolio WHERE empresa_id = ?";
$portfolio_stmt = $conn->prepare($portfolio_sql);
$portfolio_stmt->bind_param("i", $empresa_id);
$portfolio_stmt->execute();
$portfolio_result = $portfolio_stmt->get_result();
$portfolio_stmt->close();

include '../includes/header.php';
include '../admin/includes/header_admin.php';
?>

<style>
    .button-container_left { display: flex; justify-content: flex-end; }
    .button-container_left .btn { margin-left: 10px; }
    .img-thumbnail { width: 100px; height: 100px; object-fit: cover; margin-right: 5px; }
    .modal {
        display: none; position: fixed; z-index: 1000;
        left: 0; top: 0; width: 100%; height: 100%;
        background-color: rgba(0,0,0,0.4);
    }
    .modal-content {
        background-color: #fefefe; margin: 15% auto; padding: 20px;
        border: 1px solid #888; width: 80%; max-width: 500px; text-align: center;
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
                <a href="../logout.php" class="btn btn-outline-danger custom-logout">
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
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'empresa_informacoes.php' ? 'active' : ''; ?>"
                       href="empresa_informacoes.php?id=<?php echo $empresa_id; ?>">
                        <i class="fas fa-circle-info"></i> Informações
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'empresa_servicos.php' ? 'active' : ''; ?>"
                       href="empresa_servicos.php?id=<?php echo $empresa_id; ?>">
                        <i class="fas fa-handshake"></i> Serviços
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'empresa_portfolio.php' ? 'active' : ''; ?>"
                       href="empresa_portfolio.php?id=<?php echo $empresa_id; ?>">
                        <i class="fas fa-image"></i> Portfólio
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'empresa_website.php' ? 'active' : ''; ?>"
                       href="empresa_website.php?id=<?php echo $empresa_id; ?>">
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
                    <h4>Portfólio</h4>
                </div>

                <div class="mt-4">
                    <button id="mostrarFormularioPortfolio" class="btn btn-freebox-blue">Adicionar Imagem</button>
                </div>

                <div class="card mt-4" id="formularioPortfolio" style="display:none;">
                    <div class="card-body">
                        <h5 class="card-title">Adicionar Nova Imagem</h5>
                        <form method="POST" action="" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="portfolio_imagem">Imagem</label>
                                <input type="file" class="form-control-file" id="portfolio_imagem"
                                       name="portfolio_imagem" accept="image/*" required>
                            </div>
                            <div class="form-group mt-3">
                                <label for="descricao_imagem">Descrição</label>
                                <textarea class="form-control" id="descricao_imagem" name="descricao_imagem"></textarea>
                            </div>
                            <div class="button-container_left mt-4">
                                <button type="button" id="cancelarFormularioPortfolio" class="btn btn-secondary">Cancelar</button>
                                <button type="submit" name="adicionar_portfolio" class="btn btn-success">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Listar imagens -->
                <?php while ($portfolio = $portfolio_result->fetch_assoc()):
                    $imagemPath = $portfolio['imagem'];
                ?>
                <div class="card mt-3">
                    <div class="card-body p-2">
                        <img src="<?php echo htmlspecialchars($imagemPath); ?>"
                             alt="Imagem do Portfólio" class="img-thumbnail mb-1">
                        <div class="d-flex justify-content-between align-items-center">
                            <p class="mb-0"><?php echo htmlspecialchars($portfolio['descricao_imagem']); ?></p>
                            <button class="btn btn-danger btn-sm eliminar-portfolio"
                                    data-id="<?php echo $portfolio['id']; ?>">Eliminar</button>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal eliminar -->
<div id="eliminarPortfolioModal" class="modal">
    <div class="modal-content">
        <h2>Eliminar Imagem</h2>
        <p>Tem certeza que deseja eliminar esta imagem? Esta ação não pode ser desfeita.</p>
        <div class="button-container mt-4">
            <button id="cancelarEliminacaoPortfolio" class="btn btn-secondary">Cancelar</button>
            <button id="confirmarEliminacaoPortfolio" class="btn btn-danger">Eliminar</button>
        </div>
    </div>
</div>

<!-- Modal mensagem -->
<div id="messageModal" class="modal">
    <div class="modal-content">
        <h2 id="modalTitle"></h2>
        <p id="modalMessage"></p>
        <button id="okButton" class="btn btn-success">Ok</button>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script>
$(document).ready(function() {
    $('#mostrarFormularioPortfolio').on('click', function() {
        $('#formularioPortfolio').toggle();
    });

    $('#cancelarFormularioPortfolio').on('click', function() {
        $('#formularioPortfolio').hide();
    });

    $('.eliminar-portfolio').on('click', function(e) {
        e.preventDefault();
        var imagemId = $(this).data('id');
        $('#confirmarEliminacaoPortfolio').data('id', imagemId);
        $('#eliminarPortfolioModal').show();
    });

    $('#cancelarEliminacaoPortfolio').on('click', function() {
        $('#eliminarPortfolioModal').hide();
    });

    $('#confirmarEliminacaoPortfolio').on('click', function() {
        var imagemId = $(this).data('id');
        window.location.href = 'eliminar_portfolio.php?id=' + imagemId;
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
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

        okButton.onclick = function() {
            modal.style.display = 'none';
            window.history.replaceState({}, document.title,
                window.location.pathname + '?id=<?php echo $empresa_id; ?>');
        };
    }
});
</script>

<?php include '../includes/footer.php'; ?>