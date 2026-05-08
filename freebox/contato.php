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

// Para o header/footer precisam destas variáveis
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

$nome_empresa        = $empresa['nome_empresa'] ?? 'Empresa';
$logo                = trim($website['logotipo'] ?? '');
$capa                = trim($website['capa_empresa'] ?? '');
$telefone_empresa    = $empresa['telefone'] ?? '';
$email_empresa       = $empresa['email_empresa'] ?? '';
$telefone_contato    = $empresa['telefone_contato'] ?? '';
$morada_completa     = trim(($empresa['morada'] ?? '') . ' ' . ($empresa['codigo_postal'] ?? ''));

// Variáveis usadas no header/footer
$telefone_principal  = !empty($telefone_empresa) ? $telefone_empresa : $telefone_contato;
$email_principal     = $email_empresa;
$descricao           = trim($website['descricao_empresa'] ?? '');

include 'header_publico.php';
?>

<!-- CONTACTO -->
<section class="about-section section-padding">
    <div class="container">
        <h2 class="section-title">Contacto</h2>
        <div class="section-line"></div>

        <div class="row about-grid mt-4">

            <!-- LADO ESQUERDO: info -->
            <div class="col-lg-8">
                <div class="about-text-block">
                    <div class="about-icon"><i class="fas fa-store"></i></div>
                    <div style="width:100%;">
                        <h3><?= htmlspecialchars($nome_empresa); ?></h3>

                        <div class="about-contact-card" style="margin-top: 20px;">

                            <?php if (!empty($morada_completa)): ?>
                                <p>
                                    <i class="fas fa-location-dot"></i>
                                    <?= htmlspecialchars($morada_completa); ?>
                                </p>
                            <?php endif; ?>

                            <?php if (!empty($telefone_empresa)): ?>
                                <p>
                                    <i class="fas fa-phone"></i>
                                    <a href="tel:<?= htmlspecialchars($telefone_empresa); ?>">
                                        <?= htmlspecialchars($telefone_empresa); ?>
                                    </a>
                                </p>
                            <?php endif; ?>

                            <?php if (!empty($telefone_contato)): ?>
                                <p>
                                    <i class="fas fa-mobile-screen"></i>
                                    <a href="tel:<?= htmlspecialchars($telefone_contato); ?>">
                                        <?= htmlspecialchars($telefone_contato); ?>
                                    </a>
                                </p>
                            <?php endif; ?>

                            <?php if (!empty($email_empresa)): ?>
                                <p>
                                    <i class="fas fa-envelope"></i>
                                    <a href="mailto:<?= htmlspecialchars($email_empresa); ?>">
                                        <?= htmlspecialchars($email_empresa); ?>
                                    </a>
                                </p>
                            <?php endif; ?>

                        </div>

                        <?php
                        $tem_redes = !empty($website['link_facebook']) || !empty($website['link_instagram']) || !empty($website['link_x']);
                        if ($tem_redes): ?>
                            <div class="about-contact-card" style="margin-top: 16px;">
                                <p style="font-weight:600; margin-bottom:8px;">Redes Sociais</p>
                                <?php if (!empty($website['link_facebook'])): ?>
                                    <p>
                                        <i class="fab fa-facebook-f"></i>
                                        <a href="<?= htmlspecialchars($website['link_facebook']); ?>" target="_blank">Facebook</a>
                                    </p>
                                <?php endif; ?>
                                <?php if (!empty($website['link_instagram'])): ?>
                                    <p>
                                        <i class="fab fa-instagram"></i>
                                        <a href="<?= htmlspecialchars($website['link_instagram']); ?>" target="_blank">Instagram</a>
                                    </p>
                                <?php endif; ?>
                                <?php if (!empty($website['link_x'])): ?>
                                    <p>
                                        <i class="fab fa-x-twitter"></i>
                                        <a href="<?= htmlspecialchars($website['link_x']); ?>" target="_blank">X / Twitter</a>
                                    </p>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>
            </div>

            <!-- LADO DIREITO: mapa -->
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

<?php include 'footer_publico.php'; ?>