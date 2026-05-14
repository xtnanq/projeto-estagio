<?php
session_start();

require_once '../config/database.php';
require_once '../includes/functions.php';

if (!estaLogado()) {
    header("Location: ../login.php");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

$sql = "SELECT e.*, wc.url_site 
        FROM empresas e
        LEFT JOIN website_config wc ON wc.empresa_id = e.id
        WHERE e.usuario_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();

$empresa = $stmt->get_result()->fetch_assoc();

$stmt->close();

$nome_empresa = $empresa['nome_empresa'] ?? 'Empresa';
$url_site     = $empresa['url_site'] ?? '';

$link_sistema = 'http://' . ($_SERVER['HTTP_HOST'] ?? '') . '/projeto/empresa/';


?>

<link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<link rel="stylesheet"
    href="../css/empresa_politica_privacidade.css?v=<?= time(); ?>">

<?php include __DIR__ . '/header_cliente.php'; ?>

<section class="privacy-section">

    <div class="container">

        <div class="privacy-container">

            <h1 class="privacy-title">
                Política de Privacidade
            </h1>

            <p class="privacy-subtitle">
                A sua privacidade é importante para nós. Esta política explica como os dados são recolhidos,
                utilizados e protegidos dentro da plataforma FreeBox.
            </p>

            <div class="privacy-content">

                <!-- QUEM SOMOS -->
                <h2>1. Quem somos</h2>

                <p>
                    A <strong>FreeBox</strong> é uma plataforma de criação e gestão de websites empresariais,
                    permitindo às empresas criarem e gerirem a sua presença online de forma simples,
                    rápida e profissional.
                </p>

                <div class="privacy-box">

                    <strong>Endereço da plataforma:</strong>

                    <br><br>

                    <a href="<?= htmlspecialchars($link_sistema); ?>"
                        target="_blank">

                        <?= htmlspecialchars($link_sistema); ?>
                    </a>

                </div>

                <!-- DADOS -->
                <h2>2. Que dados recolhemos</h2>

                <p>
                    Durante a utilização da plataforma poderão ser recolhidos os seguintes dados:
                </p>

                <ul>
                    <li>Nome da empresa</li>
                    <li>Email e contactos telefónicos</li>
                    <li>Morada e informações fiscais</li>
                    <li>Imagens, logótipos e conteúdos enviados</li>
                    <li>Informações inseridas nos websites criados</li>
                    <li>Endereço IP e informações técnicas de acesso</li>
                </ul>

                <!-- FINALIDADE -->
                <h2>3. Finalidade da recolha de dados</h2>

                <p>
                    Os dados recolhidos destinam-se exclusivamente a:
                </p>

                <ul>
                    <li>Criação e gestão dos websites empresariais</li>
                    <li>Identificação e autenticação das contas</li>
                    <li>Prestação de suporte técnico</li>
                    <li>Comunicação entre a plataforma e os utilizadores</li>
                    <li>Melhoria contínua dos serviços</li>
                    <li>Garantia de segurança da plataforma</li>
                </ul>

                <!-- PARTILHA -->
                <h2>4. Partilha de dados</h2>

                <p>
                    A FreeBox não vende nem distribui dados pessoais a terceiros.
                    Os dados apenas poderão ser divulgados quando exigido legalmente
                    pelas autoridades competentes.
                </p>

                <!-- CONSERVAÇÃO -->
                <h2>5. Conservação dos dados</h2>

                <p>
                    Os dados serão armazenados apenas durante o período necessário
                    para garantir o funcionamento da plataforma e enquanto a conta
                    permanecer ativa.
                </p>

                <!-- DIREITOS -->
                <h2>6. Direitos do utilizador</h2>

                <p>
                    Nos termos da legislação aplicável, o utilizador poderá solicitar:
                </p>

                <ul>
                    <li>Acesso aos seus dados pessoais</li>
                    <li>Correção ou atualização das informações</li>
                    <li>Eliminação da conta e dos dados associados</li>
                    <li>Exportação dos dados armazenados</li>
                </ul>

                <!-- COOKIES -->
                <h2>7. Cookies</h2>

                <p>
                    A plataforma pode utilizar cookies para melhorar a experiência de utilização,
                    manter sessões autenticadas e otimizar funcionalidades internas.
                </p>

                <!-- SEGURANÇA -->
                <h2>8. Segurança</h2>

                <p>
                    Implementamos medidas técnicas e organizacionais adequadas para proteger
                    os dados pessoais contra acessos não autorizados, perda,
                    alteração ou divulgação indevida.
                </p>

                <div class="privacy-highlight">

                    <h3>
                        Compromisso com a privacidade
                    </h3>

                    <p>
                        Trabalhamos diariamente para garantir a proteção e confidencialidade
                        das informações dos nossos utilizadores e empresas.
                    </p>

                </div>

                <!-- ALTERAÇÕES -->
                <h2>9. Alterações desta política</h2>

                <p>
                    A presente Política de Privacidade poderá ser atualizada periodicamente,
                    sem necessidade de aviso prévio, de forma a refletir melhorias,
                    alterações legais ou novas funcionalidades da plataforma.
                </p>

                <!-- CONTACTO -->
                <h2>10. Contacto</h2>

                <p>
                    Para questões relacionadas com privacidade,
                    proteção de dados ou suporte técnico:
                </p>

                <div class="privacy-box">

                    <strong>Email de suporte:</strong>

                    <br><br>

                    <a href="mailto:suporte@freebox.pt">
                        suporte@freebox.pt
                    </a>

                </div>

            </div>

        </div>

    </div>

</section>

<?php include __DIR__ . '/footer_cliente.php'; ?>
