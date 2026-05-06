<?php
require_once '../config/database.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: ../index.php");
    exit();
}

$empresa_id = intval($_GET['id']);

// Buscar informações da empresa
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

// Buscar configurações do website
$website_sql = "SELECT * FROM website_config WHERE empresa_id = ?";
$website_stmt = $conn->prepare($website_sql);
$website_stmt->bind_param("i", $empresa_id);
$website_stmt->execute();
$website_result = $website_stmt->get_result();
$website = $website_result->fetch_assoc();
$website_stmt->close();

// Buscar serviços
$servicos_sql = "SELECT * FROM servicos WHERE empresa_id = ?";
$servicos_stmt = $conn->prepare($servicos_sql);
$servicos_stmt->bind_param("i", $empresa_id);
$servicos_stmt->execute();
$servicos_result = $servicos_stmt->get_result();
$servicos = $servicos_result->fetch_all(MYSQLI_ASSOC);
$servicos_stmt->close();

// Buscar portfólio
$portfolio_sql = "SELECT * FROM portfolio WHERE empresa_id = ?";
$portfolio_stmt = $conn->prepare($portfolio_sql);
$portfolio_stmt->bind_param("i", $empresa_id);
$portfolio_stmt->execute();
$portfolio_result = $portfolio_stmt->get_result();
$portfolio = $portfolio_result->fetch_all(MYSQLI_ASSOC);
$portfolio_stmt->close();

$conn->close();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($empresa['nome_empresa']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/projeto/css/sites_index.css">
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar-site">
    <div class="container d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3">
            <?php if (!empty($website['logotipo'])): ?>
                <img src="<?php echo htmlspecialchars($website['logotipo']); ?>"
                     alt="Logo" class="logo">
            <?php endif; ?>
            <span class="nome-empresa"><?php echo htmlspecialchars($empresa['nome_empresa']); ?></span>
        </div>
        <div class="nav-links d-none d-md-block">
            <a href="#sobre">Sobre Nós</a>
            <?php if (!empty($servicos)): ?>
                <a href="#servicos">Serviços</a>
            <?php endif; ?>
            <?php if (!empty($portfolio)): ?>
                <a href="#portfolio">Portfólio</a>
            <?php endif; ?>
            <a href="#contactos">Contactos</a>
        </div>
    </div>
</nav>

<!-- CAPA -->
<?php if (!empty($website['capa_empresa'])): ?>
    <img src="<?php echo htmlspecialchars($website['capa_empresa']); ?>"
         alt="Capa" class="capa">
<?php else: ?>
    <div class="capa-placeholder">
        <h1><?php echo htmlspecialchars($empresa['nome_empresa']); ?></h1>
    </div>
<?php endif; ?>

<!-- SOBRE NÓS -->
<section id="sobre">
    <div class="container">
        <h2 class="section-title">Sobre Nós</h2>
        <div class="row mt-4">
            <div class="col-md-8">
                <?php if (!empty($website['descricao_empresa'])): ?>
                    <p style="font-size: 1.1rem; line-height: 1.8; color: #555;">
                        <?php echo nl2br(htmlspecialchars($website['descricao_empresa'])); ?>
                    </p>
                <?php else: ?>
                    <p class="text-muted">Descrição da empresa não disponível.</p>
                <?php endif; ?>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-4">
                    <?php if (!empty($empresa['telefone'])): ?>
                        <p><i class="fas fa-phone text-primary me-2"></i>
                            <?php echo htmlspecialchars($empresa['telefone']); ?>
                        </p>
                    <?php endif; ?>
                    <?php if (!empty($empresa['email_empresa'])): ?>
                        <p><i class="fas fa-envelope text-primary me-2"></i>
                            <?php echo htmlspecialchars($empresa['email_empresa']); ?>
                        </p>
                    <?php endif; ?>
                    <?php if (!empty($empresa['morada'])): ?>
                        <p><i class="fas fa-map-marker-alt text-primary me-2"></i>
                            <?php echo htmlspecialchars($empresa['morada']); ?>
                            <?php if (!empty($empresa['codigo_postal'])): ?>
                                , <?php echo htmlspecialchars($empresa['codigo_postal']); ?>
                            <?php endif; ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- SERVIÇOS -->
<?php if (!empty($servicos)): ?>
<section id="servicos">
    <div class="container">
        <h2 class="section-title text-center">Os Nossos Serviços</h2>
        <div class="row mt-5">
            <?php foreach ($servicos as $servico): ?>
                <div class="col-md-4 mb-4">
                    <div class="servico-card">
                        <div class="icon">
                            <i class="fas fa-handshake"></i>
                        </div>
                        <h4><?php echo htmlspecialchars($servico['titulo_servico']); ?></h4>
                        <p><?php echo nl2br(htmlspecialchars($servico['descricao_servico'])); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- PORTFÓLIO -->
<?php if (!empty($portfolio)): ?>
<section id="portfolio">
    <div class="container">
        <h2 class="section-title text-center">Portfólio</h2>
        <div class="row mt-5">
            <?php foreach ($portfolio as $item): ?>
                <div class="col-md-4">
                    <div class="portfolio-item">
                        <img src="<?php echo htmlspecialchars($item['imagem']); ?>"
                             alt="<?php echo htmlspecialchars($item['descricao_imagem']); ?>">
                        <?php if (!empty($item['descricao_imagem'])): ?>
                            <div class="portfolio-overlay">
                                <p class="mb-0"><?php echo htmlspecialchars($item['descricao_imagem']); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- CONTACTOS -->
<section id="contactos">
    <div class="container">
        <h2 class="section-title">Contactos</h2>
        <div class="row mt-4">
            <div class="col-md-5">
                <?php if (!empty($empresa['morada'])): ?>
                    <div class="contacto-item">
                        <div class="icon"><i class="fas fa-map-marker-alt"></i></div>
                        <div class="info">
                            <h6>Morada</h6>
                            <p><?php echo htmlspecialchars($empresa['morada']); ?>
                                <?php if (!empty($empresa['codigo_postal'])): ?>
                                    <br><?php echo htmlspecialchars($empresa['codigo_postal']); ?>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($empresa['telefone'])): ?>
                    <div class="contacto-item">
                        <div class="icon"><i class="fas fa-phone"></i></div>
                        <div class="info">
                            <h6>Telefone</h6>
                            <p><?php echo htmlspecialchars($empresa['telefone']); ?></p>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($empresa['email_empresa'])): ?>
                    <div class="contacto-item">
                        <div class="icon"><i class="fas fa-envelope"></i></div>
                        <div class="info">
                            <h6>Email</h6>
                            <p><?php echo htmlspecialchars($empresa['email_empresa']); ?></p>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($empresa['nome_contato'])): ?>
                    <div class="contacto-item">
                        <div class="icon"><i class="fas fa-user"></i></div>
                        <div class="info">
                            <h6>Contacto</h6>
                            <p><?php echo htmlspecialchars($empresa['nome_contato']); ?>
                                <?php if (!empty($empresa['telefone_contato'])): ?>
                                    — <?php echo htmlspecialchars($empresa['telefone_contato']); ?>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- REDES SOCIAIS -->
                <?php if (!empty($website['link_facebook']) || !empty($website['link_instagram']) || !empty($website['link_x'])): ?>
                    <div class="redes-sociais mt-3">
                        <?php if (!empty($website['link_facebook'])): ?>
                            <a href="<?php echo htmlspecialchars($website['link_facebook']); ?>"
                               target="_blank" title="Facebook">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($website['link_instagram'])): ?>
                            <a href="<?php echo htmlspecialchars($website['link_instagram']); ?>"
                               target="_blank" title="Instagram">
                                <i class="fab fa-instagram"></i>
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($website['link_x'])): ?>
                            <a href="<?php echo htmlspecialchars($website['link_x']); ?>"
                               target="_blank" title="X (Twitter)">
                                <i class="fab fa-x-twitter"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- MAPA -->
            <div class="col-md-7">
                <?php if (!empty($empresa['morada'])): ?>
                    <iframe src="https://maps.google.com/maps?q=<?php echo urlencode($empresa['morada']); ?>&output=embed"
                            width="100%" height="350" style="border:0; border-radius:10px;"
                            allowfullscreen="" loading="lazy"></iframe>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- FOOTER -->
<footer>
    <div class="container">
        <p>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($empresa['nome_empresa']); ?>. Todos os direitos reservados.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="/projeto/js/sites_index.js"></script>

</body>
</html>