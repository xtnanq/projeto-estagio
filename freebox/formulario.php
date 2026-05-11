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
$logo               = trim($website['logotipo'] ?? '');
$capa               = trim($website['capa_empresa'] ?? '');
$telefone_principal = !empty($empresa['telefone']) ? $empresa['telefone'] : ($empresa['telefone_contato'] ?? '');
$email_principal    = !empty($empresa['email_empresa']) ? $empresa['email_empresa'] : ($empresa['email_contato'] ?? '');
$morada_completa    = trim(($empresa['morada'] ?? '') . ' ' . ($empresa['codigo_postal'] ?? ''));
$descricao          = trim($website['descricao_empresa'] ?? '');

include 'header_publico.php';
?>

<section class="about-section section-padding">
    <div class="container">
        <h2 class="section-title">Formulário de Contacto</h2>
        <div class="section-line"></div>

        <div class="row justify-content-center mt-4">
            <div class="col-lg-7">
                <div class="about-contact-card" style="padding: 36px 40px;">

                    <form id="contactoForm" method="POST" action="enviar_contacto.php">
                        <input type="hidden" name="empresa_id" value="<?= $empresa_id; ?>">

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-user me-2" style="color:#0066cc;"></i>Nome
                            </label>
                            <input type="text" name="nome" class="form-control" placeholder="O seu nome" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-phone me-2" style="color:#0066cc;"></i>Telefone
                            </label>
                            <input type="tel" name="telefone" class="form-control" placeholder="O seu telefone">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-envelope me-2" style="color:#0066cc;"></i>Email
                            </label>
                            <input type="email" name="email" class="form-control" placeholder="O seu email" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-message me-2" style="color:#0066cc;"></i>Mensagem
                            </label>
                            <textarea name="mensagem" class="form-control" rows="5" placeholder="Escreva a sua mensagem..." required></textarea>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="hero-button" style="text-decoration:none; cursor:pointer; border:none;">
                                Enviar 
                            </button>
                        </div>
                    </form>

                    <div id="formFeedback" style="display:none; margin-top:16px;"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.getElementById('contactoForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = this;
    const feedback = document.getElementById('formFeedback');
    const btn = form.querySelector('button[type="submit"]');

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> A enviar...';

    fetch('enviar_contacto.php', {
        method: 'POST',
        body: new FormData(form)
    })
    .then(r => r.json())
    .then(res => {
        feedback.style.display = 'block';
        if (res.success) {
            feedback.className = 'footer-feedback-ok';
            feedback.textContent = '✓ Mensagem enviada com sucesso!';
            form.reset();
        } else {
            feedback.className = 'footer-feedback-err';
            feedback.textContent = '✗ Erro ao enviar. Tente novamente.';
        }
        btn.disabled = false;
        btn.innerHTML = 'Enviar <i class="fas fa-paper-plane ms-1"></i>';
    })
    .catch(() => {
        feedback.style.display = 'block';
        feedback.className = 'footer-feedback-err';
        feedback.textContent = '✗ Erro de ligação. Tente novamente.';
        btn.disabled = false;
        btn.innerHTML = 'Enviar <i class="fas fa-paper-plane ms-1"></i>';
    });
});
</script>

<?php include 'footer_publico.php'; ?>