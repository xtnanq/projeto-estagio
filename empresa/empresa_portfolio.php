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
$portfolio_items = $portfolio_result->fetch_all(MYSQLI_ASSOC);
$portfolio_stmt->close();

include '../includes/header.php';
include '../admin/includes/header_admin.php';
?>

<link rel="stylesheet" href="/projeto/css/empresa_portfolio.css">

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
                <a href="../logout.php" class="btn btn-danger">Logout</a>
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
                        <h4>Portfólio</h4>
                    </div>

                    <div class="mt-4">
                        <button id="mostrarFormularioPortfolio" class="btn btn-primary">
                            Adicionar Imagem
                        </button>
                    </div>

                    <!-- FORM -->
                    <div class="card mt-4" id="formularioPortfolio" style="display:none;">
                        <div class="card-body">
                            <h5>Nova Imagem</h5>
                            <form method="POST" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label>Imagem</label>
                                    <input type="file" name="portfolio_imagem" class="form-control" required>
                                </div>
                                <div class="form-group mt-3">
                                    <label>Descrição</label>
                                    <textarea name="descricao_imagem" class="form-control"></textarea>
                                </div>
                                <div class="button-container_left mt-4">
                                    <button type="button" id="cancelarFormularioPortfolio" class="btn btn-secondary">
                                        Cancelar
                                    </button>
                                    <button type="submit" name="adicionar_portfolio" class="btn btn-success">
                                        Guardar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- GALERIA -->
                    <?php if (count($portfolio_items) > 0): ?>

                        <?php if (count($portfolio_items) <= 6): ?>
                            <!-- GRID simples (até 6 imagens) -->
                            <div class="portfolio-grid mt-4">
                                <?php foreach ($portfolio_items as $i => $p): ?>
                                    <div class="portfolio-grid-item">
                                        <img src="<?= htmlspecialchars($p['imagem']); ?>"
                                             alt="<?= htmlspecialchars($p['descricao_imagem']); ?>"
                                             onclick="openLightbox(<?= $i ?>)">
                                        <div class="desc"><?= htmlspecialchars($p['descricao_imagem']); ?></div>
                                        <div class="portfolio-actions mt-2 text-center">
                                            <a href="editar_portfolio.php?id=<?= $p['id']; ?>" class="btn btn-success btn-sm">Editar</a>
                                            <a href="eliminar_portfolio.php?id=<?= $p['id']; ?>" class="btn btn-danger btn-sm">Eliminar</a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                        <?php else: ?>
                            <!-- CARROSEL (mais de 6 imagens) -->
                            <?php
                                $perPage = 6;
                                $totalPages = ceil(count($portfolio_items) / $perPage);
                            ?>
                            <div class="carousel-wrapper mt-4">
                                <button class="carousel-btn prev" id="carouselPrev">&#8592;</button>
                                <div class="carousel-track" id="carouselTrack">
                                    <?php foreach ($portfolio_items as $i => $p): ?>
                                        <div class="carousel-slide">
                                            <img src="<?= htmlspecialchars($p['imagem']); ?>"
                                                 alt="<?= htmlspecialchars($p['descricao_imagem']); ?>"
                                                 onclick="openLightbox(<?= $i ?>)">
                                            <div class="desc"><?= htmlspecialchars($p['descricao_imagem']); ?></div>
                                            <div class="portfolio-actions mt-2 text-center">
                                                <a href="editar_portfolio.php?id=<?= $p['id']; ?>" class="btn btn-success btn-sm">Editar</a>
                                                <a href="eliminar_portfolio.php?id=<?= $p['id']; ?>" class="btn btn-danger btn-sm">Eliminar</a>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <button class="carousel-btn next" id="carouselNext">&#8594;</button>
                            </div>

                            <!-- BOLINHAS -->
                            <div class="carousel-dots" id="carouselDots">
                                <?php for ($d = 0; $d < $totalPages; $d++): ?>
                                    <button class="dot <?= $d === 0 ? 'active' : ''; ?>" data-page="<?= $d ?>"></button>
                                <?php endfor; ?>
                            </div>
                        <?php endif; ?>

                    <?php else: ?>
                        <p class="text-muted mt-4 text-center">Ainda não há imagens no portfólio.</p>
                    <?php endif; ?>

                </div>
            </div>
        </div>

    </div>
</div>

<!-- LIGHTBOX -->
<div class="lightbox-overlay" id="lightboxOverlay">
    <button class="lightbox-close" id="lightboxClose">&times;</button>
    <button class="lightbox-arrow left" id="lightboxPrev">&#8592;</button>
    <img class="lightbox-img" id="lightboxImg" src="" alt="">
    <button class="lightbox-arrow right" id="lightboxNext">&#8594;</button>
    <div class="lightbox-desc" id="lightboxDesc"></div>
</div>

<!-- MODAL MENSAGENS -->
<div id="messageModal" class="modal">
    <div class="modal-content">
        <h4 id="modalTitle"></h4>
        <p id="modalMessage"></p>
        <button id="okButton" class="btn btn-success">OK</button>
    </div>
</div>

<script>
// ── Dados das imagens para JS ──
const portfolioImages = <?= json_encode(array_map(function($p) {
    return [
        'src' => $p['imagem'],
        'desc' => $p['descricao_imagem']
    ];
}, $portfolio_items)); ?>;

// ── Formulário ──
document.getElementById('mostrarFormularioPortfolio').onclick = () =>
    document.getElementById('formularioPortfolio').style.display = 'block';

document.getElementById('cancelarFormularioPortfolio').onclick = () =>
    document.getElementById('formularioPortfolio').style.display = 'none';

// ── Lightbox ──
let currentIndex = 0;

function openLightbox(index) {
    currentIndex = index;
    updateLightbox();
    document.getElementById('lightboxOverlay').classList.add('open');
}

function updateLightbox() {
    document.getElementById('lightboxImg').src  = portfolioImages[currentIndex].src;
    document.getElementById('lightboxDesc').textContent = portfolioImages[currentIndex].desc;
}

document.getElementById('lightboxClose').onclick = () =>
    document.getElementById('lightboxOverlay').classList.remove('open');

document.getElementById('lightboxPrev').onclick = () => {
    currentIndex = (currentIndex - 1 + portfolioImages.length) % portfolioImages.length;
    updateLightbox();
};

document.getElementById('lightboxNext').onclick = () => {
    currentIndex = (currentIndex + 1) % portfolioImages.length;
    updateLightbox();
};

// Setas do teclado
document.addEventListener('keydown', function(e) {
    const overlay = document.getElementById('lightboxOverlay');
    if (!overlay.classList.contains('open')) return;
    if (e.key === 'ArrowLeft')  { currentIndex = (currentIndex - 1 + portfolioImages.length) % portfolioImages.length; updateLightbox(); }
    if (e.key === 'ArrowRight') { currentIndex = (currentIndex + 1) % portfolioImages.length; updateLightbox(); }
    if (e.key === 'Escape')     { overlay.classList.remove('open'); }
});

// Fechar ao clicar fora da imagem
document.getElementById('lightboxOverlay').addEventListener('click', function(e) {
    if (e.target === this) this.classList.remove('open');
});

// ── Carrosel (só existe se houver mais de 6 imagens) ──
const track = document.getElementById('carouselTrack');
if (track) {
    const perPage   = 6;
    const perSlide  = 3; // visíveis por vez
    const total     = portfolioImages.length;
    const totalPages = Math.ceil(total / perPage);
    let currentPage  = 0;

    function goToPage(page) {
        currentPage = page;
        const slideWidth = track.querySelector('.carousel-slide').offsetWidth;
        track.style.transform = `translateX(-${page * perPage * slideWidth / perSlide}px)`;

        document.querySelectorAll('.dot').forEach((d, i) => {
            d.classList.toggle('active', i === page);
        });
    }

    document.getElementById('carouselPrev').onclick = () =>
        goToPage((currentPage - 1 + totalPages) % totalPages);

    document.getElementById('carouselNext').onclick = () =>
        goToPage((currentPage + 1) % totalPages);

    document.querySelectorAll('.dot').forEach(dot => {
        dot.addEventListener('click', () => goToPage(parseInt(dot.dataset.page)));
    });
}

// ── Modal mensagens ──
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