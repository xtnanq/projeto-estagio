<?php
require_once '../config/database.php';

if (!isset($_GET['url'])) {
    header("Location: ../index.php");
    exit();
}

$url_site = preg_replace('/[^a-zA-Z0-9\-]/', '', $_GET['url']);

$stmt = $conn->prepare("
    SELECT e.*, w.*
    FROM empresas e
    JOIN website_config w ON w.empresa_id = e.id
    WHERE w.url_site = ?
");

$stmt->bind_param("s", $url_site);
$stmt->execute();

$result = $stmt->get_result();
$empresa = $result->fetch_assoc();

$stmt->close();

if (!$empresa) {
    header("Location: ../index.php");
    exit();
}

$nome_empresa       = $empresa['nome_empresa'] ?? 'Empresa';
$email_principal    = $empresa['email_empresa'] ?? '';
$website            = $empresa;

include 'header_publico.php';
?>

<section class="section-padding">
    <div class="container" style="max-width: 1000px;">

        <h1 class="section-title">Política de Privacidade</h1>
        <div class="section-line"></div>

        <div class="privacy-content">

            <h2>Quem somos</h2>

            <?php
            $link_site = 'http://' . ($_SERVER['HTTP_HOST'] ?? '') . '/projeto/freebox/' . ($website['url_site'] ?? '');
            ?>

            <p>
                O endereço do nosso site é:

                <a href="<?= htmlspecialchars($link_site); ?>" target="_blank">
                    <?= htmlspecialchars($link_site); ?>
                </a>
            </p>

            <h2>Proteção de Dados Pessoais</h2>

            <p>
                A proteção dos seus dados pessoais é muito importante para a
                <strong><?= htmlspecialchars($nome_empresa); ?></strong>.
            </p>

            <p>
                Tratamos os seus dados com responsabilidade e adotamos todas as medidas
                necessárias para garantir a sua segurança e confidencialidade.
            </p>

            <h2>Que dados pessoais são recolhidos</h2>

            <p>
                Apenas recolhemos os dados fornecidos voluntariamente
                através dos formulários deste website.
            </p>

            <ul>
                <li>Nome</li>
                <li>Email</li>
                <li>Telefone</li>
                <li>Mensagem enviada</li>
                <li>Endereço IP</li>
            </ul>

            <h2>Finalidade dos dados</h2>

            <p>
                Os dados recolhidos são utilizados exclusivamente para:
            </p>

            <ul>
                <li>Responder a pedidos de contacto</li>
                <li>Comunicação com clientes</li>
                <li>Prestação de serviços</li>
                <li>Melhoria da experiência de navegação</li>
            </ul>

            <h2>Cookies</h2>

            <p>
                Este website poderá utilizar cookies para melhorar
                a experiência do utilizador.
            </p>

            <p>
                Os cookies permitem guardar preferências de navegação
                e recolher dados estatísticos anónimos.
            </p>

            <h2>Conteúdo incorporado</h2>

            <p>
                Algumas páginas podem incluir conteúdos externos,
                como mapas, vídeos ou redes sociais.
            </p>

            <p>
                Esses serviços externos poderão recolher dados
                conforme as respetivas políticas de privacidade.
            </p>

            <h2>Partilha de dados</h2>

            <p>
                A <?= htmlspecialchars($nome_empresa); ?>
                não partilha dados pessoais com terceiros,
                exceto quando exigido por lei.
            </p>

            <h2>Conservação dos dados</h2>

            <p>
                Os dados serão conservados apenas pelo tempo necessário
                para cumprir as finalidades para as quais foram recolhidos.
            </p>

            <h2>Direitos do utilizador</h2>

            <p>
                O utilizador pode solicitar:
            </p>

            <ul>
                <li>Acesso aos seus dados</li>
                <li>Retificação dos dados</li>
                <li>Eliminação dos dados</li>
                <li>Limitação do tratamento</li>
                <li>Retirada do consentimento</li>
            </ul>

            <?php if (!empty($email_principal)): ?>
                <p>
                    Para qualquer questão relacionada com os seus dados:
                    <strong><?= htmlspecialchars($email_principal); ?></strong>
                </p>
            <?php endif; ?>

            <h2>Segurança</h2>

            <p>
                Implementamos medidas técnicas e organizativas adequadas
                para proteger os dados pessoais contra acessos não autorizados,
                perda ou divulgação indevida.
            </p>

            <h2>Livro de Reclamações</h2>

            <p>
                Pode aceder ao Livro de Reclamações Online através do link:
            </p>

            <p>
                <a href="https://www.livroreclamacoes.pt/Inicio/" target="_blank">
                    https://www.livroreclamacoes.pt/Inicio/
                </a>
            </p>

            <h2>Alterações à política</h2>

            <p>
                Esta Política de Privacidade pode ser alterada sem aviso prévio.
            </p>

            <h2>Legislação aplicável</h2>

            <p>
                Esta política é regida pela legislação portuguesa
                e pelo Regulamento Geral de Proteção de Dados (RGPD).
            </p>

        </div>

    </div>
</section>

<?php include 'footer_publico.php'; ?>