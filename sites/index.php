<?php
require_once '../config/database.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: ../index.php");
    exit();
}

$empresa_id = intval($_GET['id']);

// Empresa
$sql = "SELECT * FROM empresas WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $empresa_id);
$stmt->execute();
$result = $stmt->get_result();
$empresa = $result->fetch_assoc();
$stmt->close();

if (!$empresa) {
    header("Location: ../index.php");
    exit();
}

// Website config
$website_sql = "SELECT * FROM website_config WHERE empresa_id = ?";
$website_stmt = $conn->prepare($website_sql);
$website_stmt->bind_param("i", $empresa_id);
$website_stmt->execute();
$website_result = $website_stmt->get_result();
$website = $website_result->fetch_assoc();
$website_stmt->close();

if (!$website) {
    $website = [
        'logotipo' => '',
        'capa_empresa' => '',
        'descricao_empresa' => '',
        'link_facebook' => '',
        'link_instagram' => '',
        'link_x' => ''
    ];
}

// Serviços
$servicos_sql = "SELECT * FROM servicos WHERE empresa_id = ?";
$servicos_stmt = $conn->prepare($servicos_sql);
$servicos_stmt->bind_param("i", $empresa_id);
$servicos_stmt->execute();
$servicos_result = $servicos_stmt->get_result();
$servicos = $servicos_result->fetch_all(MYSQLI_ASSOC);
$servicos_stmt->close();

// Portfólio
$portfolio_sql = "SELECT * FROM portfolio WHERE empresa_id = ?";
$portfolio_stmt = $conn->prepare($portfolio_sql);
$portfolio_stmt->bind_param("i", $empresa_id);
$portfolio_stmt->execute();
$portfolio_result = $portfolio_stmt->get_result();
$portfolio = $portfolio_result->fetch_all(MYSQLI_ASSOC);
$portfolio_stmt->close();

$conn->close();

$nome_empresa = $empresa['nome_empresa'] ?? 'Empresa';
$descricao = trim($website['descricao_empresa'] ?? '');
$logo = trim($website['logotipo'] ?? '');
$capa = trim($website['capa_empresa'] ?? '');

$hero_style = '';
if (!empty($capa)) {
    $hero_style = "background-image: url('" . htmlspecialchars($capa, ENT_QUOTES) . "');";
}

$portfolio_bg = !empty($capa) ? $capa : (!empty($portfolio[0]['imagem']) ? $portfolio[0]['imagem'] : '');
$portfolio_style = '';
if (!empty($portfolio_bg)) {
    $portfolio_style = "background-image: url('" . htmlspecialchars($portfolio_bg, ENT_QUOTES) . "');";
}

$telefone_principal = !empty($empresa['telefone']) ? $empresa['telefone'] : ($empresa['telefone_contato'] ?? '');
$email_principal = !empty($empresa['email_empresa']) ? $empresa['email_empresa'] : ($empresa['email_contato'] ?? '');
$morada_completa = trim(($empresa['morada'] ?? '') . ' ' . ($empresa['codigo_postal'] ?? ''));
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($nome_empresa); ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <link rel="stylesheet" href="/projeto/css/site_publico.css">
</head>
<body>

<!-- NAVBAR -->
<header class="public-navbar">
    <div class="container navbar-inner">

        <a href="#inicio" class="brand">
            <?php if (!empty($logo)): ?>
                <img src="<?= htmlspecialchars($logo); ?>" alt="<?= htmlspecialchars($nome_empresa); ?>" class="brand-logo">
            <?php else: ?>
                <span class="brand-name"><?= htmlspecialchars($nome_empresa); ?></span>
            <?php endif; ?>
        </a>

        <nav class="nav-links">
            <a href="#sobre">Sobre Nós</a>

            <?php if (!empty($servicos)): ?>
                <a href="#servicos">Serviços</a>
            <?php endif; ?>

            <?php if (!empty($portfolio)): ?>
                <a href="#portfolio">Portfólio</a>
            <?php endif; ?>

            <a href="#contactos">Contacto</a>
        </nav>

        <div class="language-flags">
            <span title="English">🇬🇧</span>
            <span title="Français">🇫🇷</span>
            <span title="Português">🇵🇹</span>
            <span title="Español">🇪🇸</span>
        </div>

    </div>
</header>

<!-- HERO -->
<section id="inicio" class="hero <?= !empty($capa) ? 'has-image' : ''; ?>" style="<?= $hero_style; ?>">
    <div class="hero-content">
        <h1><?= htmlspecialchars($nome_empresa); ?></h1>

        <p class="hero-subtitle">
            <?php if (!empty($servicos)): ?>
                <?php
                    $nomes_servicos = array_slice(array_column($servicos, 'nome_servico'), 0, 4);
                    echo htmlspecialchars(implode(' · ', $nomes_servicos));
                ?>
            <?php else: ?>
                Soluções profissionais para a sua empresa
            <?php endif; ?>
        </p>

        <a href="#contactos" class="hero-button">
            Contacte-nos
        </a>
    </div>
</section>

<!-- SOBRE NÓS -->
<section id="sobre" class="about-section section-padding">
    <div class="container">

        <h2 class="section-title">Sobre Nós</h2>
        <div class="section-line"></div>

        <div class="row about-grid">
            <div class="col-lg-8">
                <div class="about-text-block">
                    <div class="about-icon">
                        <i class="fas fa-store"></i>
                    </div>

                    <div>
                        <h3>Empresa</h3>

                        <?php if (!empty($descricao)): ?>
                            <p class="about-text">
                                <?= nl2br(htmlspecialchars($descricao)); ?>
                            </p>
                        <?php else: ?>
                            <p class="about-text">
                                Somos uma empresa dedicada a prestar serviços de qualidade,
                                com foco na satisfação dos nossos clientes e na apresentação
                                profissional da nossa marca.
                            </p>
                        <?php endif; ?>

                        <div class="about-contact-card">
                            <?php if (!empty($morada_completa)): ?>
                                <p>
                                    <i class="fas fa-location-dot"></i>
                                    <?= htmlspecialchars($morada_completa); ?>
                                </p>
                            <?php endif; ?>

                            <?php if (!empty($telefone_principal)): ?>
                                <p>
                                    <i class="fas fa-phone"></i>
                                    <?= htmlspecialchars($telefone_principal); ?>
                                </p>
                            <?php endif; ?>

                            <?php if (!empty($email_principal)): ?>
                                <p>
                                    <i class="fas fa-envelope"></i>
                                    <?= htmlspecialchars($email_principal); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <?php if (!empty($morada_completa)): ?>
                    <div class="about-map-card">
                        <iframe src="https://maps.google.com/maps?q=<?= urlencode($morada_completa); ?>&output=embed"
                                allowfullscreen
                                loading="lazy"></iframe>
                    </div>
                <?php else: ?>
                    <div class="about-map-placeholder">
                        <i class="fas fa-map-location-dot"></i>
                        <p>Morada não disponível.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</section>

<!-- SERVIÇOS -->
<?php if (!empty($servicos)): ?>
<section id="servicos" class="services-section section-padding">
    <div class="container">

        <h2 class="section-title">Serviços</h2>
        <div class="section-line"></div>

        <div class="row g-4">
            <?php foreach ($servicos as $servico): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-screwdriver-wrench"></i>
                        </div>

                        <h4>
                            <?= htmlspecialchars($servico['titulo_servico'] ?: $servico['nome_servico']); ?>
                        </h4>

                        <p>
                            <?= nl2br(htmlspecialchars($servico['descricao_servico'] ?? '')); ?>
                        </p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    </div>
</section>
<?php endif; ?>

<!-- PORTFÓLIO -->
<?php if (!empty($portfolio)): ?>
<section id="portfolio" class="portfolio-section section-padding" style="<?= $portfolio_style; ?>">
    <div class="container">

        <h2 class="section-title">Portfólio</h2>
        <div class="section-line"></div>

        <div class="portfolio-grid">
            <?php foreach ($portfolio as $index => $item): ?>
                <div class="portfolio-item" data-index="<?= $index; ?>">
                    <img src="<?= htmlspecialchars($item['imagem']); ?>"
                         alt="<?= htmlspecialchars($item['descricao_imagem'] ?: 'Imagem de portfólio'); ?>">
                </div>
            <?php endforeach; ?>
        </div>

    </div>
</section>
<?php endif; ?>

<!-- CONTACTOS -->
<section id="contactos" class="contact-section">
    <div class="container">

        <div class="contact-cards">

            <div class="contact-card">
                <div class="contact-card-icon">
                    <i class="fas fa-phone"></i>
                </div>

                <div>
                    <h4>
                        <?= !empty($telefone_principal) ? htmlspecialchars($telefone_principal) : 'Telefone'; ?>
                    </h4>
                    <p>
                        Segunda a sexta: 9h às 19h<br>
                        Sábado: 9h às 12:30h
                    </p>
                </div>
            </div>

            <div class="contact-card">
                <div class="contact-card-icon">
                    <i class="fas fa-envelope"></i>
                </div>

                <div>
                    <h4>Fale connosco</h4>

                    <?php if (!empty($email_principal)): ?>
                        <p>
                            <a href="mailto:<?= htmlspecialchars($email_principal); ?>">
                                <?= htmlspecialchars($email_principal); ?>
                            </a>
                        </p>
                    <?php else: ?>
                        <p>Contacte-nos diretamente.</p>
                    <?php endif; ?>

                    <?php if (!empty($empresa['nome_contato'])): ?>
                        <p><?= htmlspecialchars($empresa['nome_contato']); ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="contact-card">
                <div class="contact-card-icon">
                    <i class="fab fa-facebook-f"></i>
                </div>

                <div>
                    <h4>Redes sociais</h4>

                    <?php if (!empty($website['link_facebook'])): ?>
                        <p>
                            <a href="<?= htmlspecialchars($website['link_facebook']); ?>" target="_blank">
                                Siga-nos no Facebook
                            </a>
                        </p>
                    <?php endif; ?>

                    <?php if (!empty($website['link_instagram'])): ?>
                        <p>
                            <a href="<?= htmlspecialchars($website['link_instagram']); ?>" target="_blank">
                                Instagram
                            </a>
                        </p>
                    <?php endif; ?>

                    <?php if (!empty($website['link_x'])): ?>
                        <p>
                            <a href="<?= htmlspecialchars($website['link_x']); ?>" target="_blank">
                                X / Twitter
                            </a>
                        </p>
                    <?php endif; ?>

                    <?php if (empty($website['link_facebook']) && empty($website['link_instagram']) && empty($website['link_x'])): ?>
                        <p>Siga-nos nas nossas redes sociais.</p>
                    <?php endif; ?>
                </div>
            </div>

        </div>

    </div>
</section>

<!-- FOOTER -->
<footer class="public-footer">
    <div class="container">
        <div class="footer-line"></div>

        <div class="footer-links">
            <a href="#">Política de privacidade</a>
            <span>|</span>
            <a href="#">Resolução de conflitos</a>
            <span>|</span>
            <a href="#">Livro de reclamações</a>
        </div>

        <div class="footer-bottom">
            <span>
                © <?= date('Y'); ?> — <?= htmlspecialchars($nome_empresa); ?> — todos os direitos reservados
            </span>

            <span>
                Made by <span class="made-by">IS4 Web Designer</span>
            </span>
        </div>
    </div>
</footer>

<!-- LIGHTBOX -->
<?php if (!empty($portfolio)): ?>
<div id="lightbox" class="lightbox">
    <button class="lightbox-close" id="lightboxClose">
        <i class="fas fa-xmark"></i>
    </button>

    <button class="lightbox-prev" id="lightboxPrev">
        <i class="fas fa-arrow-left"></i>
    </button>

    <div class="lightbox-image-wrap">
        <img id="lightboxImage" src="" alt="Imagem do portfólio">
        <div class="lightbox-counter" id="lightboxCounter"></div>
    </div>

    <button class="lightbox-next" id="lightboxNext">
        <i class="fas fa-arrow-right"></i>
    </button>
</div>
<?php endif; ?>

<a href="#inicio" class="back-to-top">
    <i class="fas fa-chevron-up"></i>
</a>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.querySelectorAll('a[href^="#"]').forEach(function(anchor) {
    anchor.addEventListener('click', function(e) {
        const target = document.querySelector(this.getAttribute('href'));

        if (target) {
            e.preventDefault();
            target.scrollIntoView({ behavior: 'smooth' });
        }
    });
});
</script>

<?php if (!empty($portfolio)): ?>
<script>
const portfolioImages = <?php echo json_encode(array_column($portfolio, 'imagem')); ?>;
let currentImageIndex = 0;

const lightbox = document.getElementById('lightbox');
const lightboxImage = document.getElementById('lightboxImage');
const lightboxCounter = document.getElementById('lightboxCounter');
const lightboxClose = document.getElementById('lightboxClose');
const lightboxPrev = document.getElementById('lightboxPrev');
const lightboxNext = document.getElementById('lightboxNext');

function openLightbox(index) {
    currentImageIndex = index;
    updateLightbox();
    lightbox.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeLightbox() {
    lightbox.classList.remove('active');
    document.body.style.overflow = '';
}

function updateLightbox() {
    lightboxImage.src = portfolioImages[currentImageIndex];
    lightboxCounter.textContent = 'Item ' + (currentImageIndex + 1) + ' of ' + portfolioImages.length;
}

function nextImage() {
    currentImageIndex = (currentImageIndex + 1) % portfolioImages.length;
    updateLightbox();
}

function prevImage() {
    currentImageIndex = (currentImageIndex - 1 + portfolioImages.length) % portfolioImages.length;
    updateLightbox();
}

document.querySelectorAll('.portfolio-item').forEach(function(item) {
    item.addEventListener('click', function() {
        openLightbox(parseInt(this.dataset.index));
    });
});

lightboxClose.addEventListener('click', closeLightbox);
lightboxNext.addEventListener('click', nextImage);
lightboxPrev.addEventListener('click', prevImage);

lightbox.addEventListener('click', function(e) {
    if (e.target === lightbox) {
        closeLightbox();
    }
});

document.addEventListener('keydown', function(e) {
    if (!lightbox.classList.contains('active')) return;

    if (e.key === 'Escape') closeLightbox();
    if (e.key === 'ArrowRight') nextImage();
    if (e.key === 'ArrowLeft') prevImage();
});
</script>
<?php endif; ?>

</body>
</html>