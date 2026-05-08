<?php
require_once '../config/database.php';

if (isset($_GET['url'])) {
    $url_site = preg_replace('/[^a-zA-Z0-9\-]/', '', $_GET['url']);
    $website_stmt = $conn->prepare("SELECT * FROM website_config WHERE url_site = ?");
    $website_stmt->bind_param("s", $url_site);
    $website_stmt->execute();
    $website = $website_stmt->get_result()->fetch_assoc();
    $website_stmt->close();
    if (!$website) { header("Location: ../index.php"); exit(); }
    $empresa_id = $website['empresa_id'];
} elseif (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $empresa_id = intval($_GET['id']);
    $website_stmt = $conn->prepare("SELECT * FROM website_config WHERE empresa_id = ?");
    $website_stmt->bind_param("i", $empresa_id);
    $website_stmt->execute();
    $website = $website_stmt->get_result()->fetch_assoc();
    $website_stmt->close();
} else {
    header("Location: ../index.php");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM empresas WHERE id = ?");
$stmt->bind_param("i", $empresa_id);
$stmt->execute();
$empresa = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$empresa) { header("Location: ../index.php"); exit(); }

if (!$website) {
    $website = [
        'logotipo' => '', 'capa_empresa' => '',
        'descricao_empresa' => '', 'link_facebook' => '',
        'link_instagram' => '', 'link_x' => '', 'url_site' => ''
    ];
}

$servicos_stmt = $conn->prepare("SELECT * FROM servicos WHERE empresa_id = ?");
$servicos_stmt->bind_param("i", $empresa_id);
$servicos_stmt->execute();
$servicos = $servicos_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$servicos_stmt->close();

$portfolio_stmt = $conn->prepare("SELECT * FROM portfolio WHERE empresa_id = ?");
$portfolio_stmt->bind_param("i", $empresa_id);
$portfolio_stmt->execute();
$portfolio = $portfolio_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$portfolio_stmt->close();

$conn->close();

$nome_empresa       = $empresa['nome_empresa'] ?? 'Empresa';
$descricao          = trim($website['descricao_empresa'] ?? '');
$logo               = trim($website['logotipo'] ?? '');
$capa               = trim($website['capa_empresa'] ?? '');
$telefone_principal = !empty($empresa['telefone']) ? $empresa['telefone'] : ($empresa['telefone_contato'] ?? '');
$email_principal    = !empty($empresa['email_empresa']) ? $empresa['email_empresa'] : ($empresa['email_contato'] ?? '');
$morada_completa    = trim(($empresa['morada'] ?? '') . ' ' . ($empresa['codigo_postal'] ?? ''));

$hero_style = !empty($capa) ? "background-image: url('" . htmlspecialchars($capa, ENT_QUOTES) . "');" : '';
$portfolio_bg = !empty($capa) ? $capa : (!empty($portfolio[0]['imagem']) ? $portfolio[0]['imagem'] : '');
$portfolio_style = !empty($portfolio_bg) ? "background-image: url('" . htmlspecialchars($portfolio_bg, ENT_QUOTES) . "');" : '';

include 'header_publico.php';
?>

<!-- HERO -->
<section id="inicio" class="hero <?= !empty($capa) ? 'has-image' : ''; ?>" style="<?= $hero_style; ?>">
    <div class="hero-content">
        <h1><?= htmlspecialchars($nome_empresa); ?></h1>
        <p class="hero-subtitle">
            <?php if (!empty($servicos)): ?>
                <?= htmlspecialchars(implode(' · ', array_slice(array_column($servicos, 'nome_servico'), 0, 4))); ?>
            <?php else: ?>
                Soluções profissionais para a sua empresa
            <?php endif; ?>
        </p>
        <a href="/projeto/freebox/contato.php?url=<?= htmlspecialchars($website['url_site'] ?? ''); ?>" class="hero-button">Contacte-nos</a>
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
                    <div class="about-icon"><i class="fas fa-store"></i></div>
                    <div>
                        <h3>Empresa</h3>
                        <p class="about-text">
                            <?= !empty($descricao) ? nl2br(htmlspecialchars($descricao)) : 'Somos uma empresa dedicada a prestar serviços de qualidade, com foco na satisfação dos nossos clientes.'; ?>
                        </p>
                        <div class="about-contact-card">
                            <?php if (!empty($morada_completa)): ?>
                                <p><i class="fas fa-location-dot"></i> <?= htmlspecialchars($morada_completa); ?></p>
                            <?php endif; ?>
                            <?php if (!empty($telefone_principal)): ?>
                                <p><i class="fas fa-phone"></i> <?= htmlspecialchars($telefone_principal); ?></p>
                            <?php endif; ?>
                            <?php if (!empty($email_principal)): ?>
                                <p><i class="fas fa-envelope"></i> <?= htmlspecialchars($email_principal); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <?php if (!empty($morada_completa)): ?>
                    <div class="about-map-card">
                        <iframe src="https://maps.google.com/maps?q=<?= urlencode($morada_completa); ?>&output=embed"
                                allowfullscreen loading="lazy"></iframe>
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
        <?php $servicos_chunks = array_chunk($servicos, 6); $s_pages = count($servicos_chunks); ?>
        <div class="carousel-outer mt-4">
            <?php if ($s_pages > 1): ?>
                <button class="carousel-btn prev" id="svcPrev"><i class="fas fa-chevron-left"></i></button>
                <button class="carousel-btn next" id="svcNext"><i class="fas fa-chevron-right"></i></button>
            <?php endif; ?>
            <div class="carousel-wrapper">
                <div class="carousel-track" id="svcTrack">
                    <?php foreach ($servicos_chunks as $chunk): ?>
                        <div class="carousel-page">
                            <?php foreach ($chunk as $servico): ?>
                                <div class="service-card">
                                    <div class="service-icon"><i class="fas fa-screwdriver-wrench"></i></div>
                                    <h4><?= htmlspecialchars($servico['titulo_servico'] ?: ($servico['nome_servico'] ?? '')); ?></h4>
                                    <p><?= nl2br(htmlspecialchars($servico['descricao_servico'] ?? '')); ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php if ($s_pages > 1): ?>
            <div class="carousel-dots" id="svcDots">
                <?php for ($d = 0; $d < $s_pages; $d++): ?>
                    <button class="dot <?= $d === 0 ? 'active' : ''; ?>" data-page="<?= $d ?>"></button>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<!-- PORTFÓLIO -->
<?php if (!empty($portfolio)): ?>
<section id="portfolio" class="portfolio-section section-padding" style="<?= $portfolio_style; ?>">
    <div class="container">
        <h2 class="section-title">Portfólio</h2>
        <div class="section-line"></div>
        <?php $portfolio_chunks = array_chunk($portfolio, 6); $p_pages = count($portfolio_chunks); ?>
        <div class="carousel-outer mt-4">
            <?php if ($p_pages > 1): ?>
                <button class="carousel-btn prev" id="prtPrev"><i class="fas fa-chevron-left"></i></button>
                <button class="carousel-btn next" id="prtNext"><i class="fas fa-chevron-right"></i></button>
            <?php endif; ?>
            <div class="carousel-wrapper">
                <div class="carousel-track" id="prtTrack">
                    <?php foreach ($portfolio_chunks as $chunkIndex => $chunk): ?>
                        <div class="carousel-page">
                            <?php foreach ($chunk as $i => $item):
                                $globalIndex = $chunkIndex * 6 + $i; ?>
                                <div class="portfolio-card" data-index="<?= $globalIndex ?>">
                                    <img src="<?= htmlspecialchars($item['imagem']); ?>"
                                         alt="<?= htmlspecialchars($item['descricao_imagem'] ?: 'Imagem de portfólio'); ?>">
                                    <div class="overlay"><i class="fas fa-magnifying-glass-plus"></i></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php if ($p_pages > 1): ?>
            <div class="carousel-dots" id="prtDots">
                <?php for ($d = 0; $d < $p_pages; $d++): ?>
                    <button class="dot <?= $d === 0 ? 'active' : ''; ?>" data-page="<?= $d ?>"></button>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<?php include 'footer_publico.php'; ?>