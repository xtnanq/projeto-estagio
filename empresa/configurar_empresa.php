<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Verificar se o ID da empresa foi passado via GET
if(isset($_GET['id'])) {
    $empresa_id = $_GET['id'];
} else {
    header("Location: dashboard.php");
    exit();
}

// Buscar informações da empresa
$sql = "SELECT * FROM empresas WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $empresa_id);
$stmt->execute();
$result = $stmt->get_result();
$empresa = $result->fetch_assoc();

if(!$empresa) {
    header("Location: dashboard.php");
    exit();
}

// Processar o formulário quando enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome_empresa = $_POST['nome_empresa'] ?? '';
    $morada = $_POST['morada'] ?? '';
    $codigo_postal = $_POST['codigo_postal'] ?? '';
    $telefone = $_POST['telefone'] ?? '';
    $email_empresa = $_POST['email_empresa'] ?? '';
    $nome_contato = $_POST['nome_contato'] ?? '';
    $telefone_contato = $_POST['telefone_contato'] ?? '';
    $email_contato = $_POST['email_contato'] ?? '';

    $update_sql = "UPDATE empresas SET 
                   nome_empresa = ?, 
                   morada = ?, 
                   codigo_postal = ?, 
                   telefone = ?, 
                   email_empresa = ?, 
                   nome_contato = ?, 
                   telefone_contato = ?, 
                   email_contato = ? 
                   WHERE id = ?";
    
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ssssssssi", $nome_empresa, $morada, $codigo_postal, $telefone, $email_empresa, $nome_contato, $telefone_contato, $email_contato, $empresa_id);
    
    if ($update_stmt->execute()) {
        $_SESSION['success_message'] = "Informações da empresa atualizadas com sucesso!";
        header("Location: configurar_empresa.php?id=" . $empresa_id . "&show_message=1");
        exit();
    } else {
        $_SESSION['error_message'] = "Erro ao atualizar as informações da empresa: " . $conn->error;
        header("Location: configurar_empresa.php?id=" . $empresa_id . "&show_message=1");
        exit();
    }
}

include '../includes/header.php';
include '../admin/includes/header_admin.php';
include '../admin/includes/functions_admin.php';
?>

<style>
    .button-container_left {
        display: flex;
        justify-content: flex-end;
    }
    .button-container_left .btn {
        margin-left: 10px;
    }
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
                <a href="../logout.php" class="btn btn-outline-danger custom-logout" id="logoutBtn"><i class="fas fa-power-off"></i> Logout
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
                    <a class="nav-link active " href="#informacoes"><i class="fas fa-circle-info"></i> Informações</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#servicos"><i class="fas fa-handshake"></i> Serviços</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#portfolio"><i class="fas fa-image"></i> Portfólio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#website"><i class="fas fa-globe"></i> Website</a>
                </li>
                <li class="nav-item_2">
                    <a class="nav-link_2" href="/projeto/admin/dashboard.php"><i class="fas fa-house"></i> Dashboard</a>
                </li>                
            </ul>
        </div>
        <div class="col-md-9">
            <?php
            if (isset($success_message)) {
                echo "<div class='alert alert-success'>" . $success_message . "</div>";
            }
            if (isset($error_message)) {
                echo "<div class='alert alert-danger'>" . $error_message . "</div>";
            }
            ?>
            <div id="informacoes" class="config-section">
                <div class="text-center">
                    <h4>Informações da Empresa</h4>
                </div>    
                <form method="POST" action="">
                    <div class="form-group mt-4">
                        <label for="nome_empresa"><i class="fas fa-building"></i> Nome da Empresa</label>
                        <input type="text" class="form-control" id="nome_empresa" name="nome_empresa" value="<?php echo htmlspecialchars($empresa['nome_empresa']); ?>">
                    </div>
                    <div class="form-group mt-4">
                        <label for="morada"><i class="fas fa-map-marker-alt"></i> Morada</label>
                        <input type="text" class="form-control" id="morada" name="morada" value="<?php echo htmlspecialchars($empresa['morada'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div class="form-group mt-4">
                        <label for="codigo_postal"><i class="fas fa-mail-bulk"></i> Código Postal</label>
                        <input type="text" class="form-control" id="codigo_postal" name="codigo_postal" value="<?php echo htmlspecialchars($empresa['codigo_postal']); ?>">
                    </div>
                    <div class="form-group mt-4">
                        <label for="telefone"><i class="fas fa-phone"></i> Telefone</label>
                        <input type="tel" class="form-control" id="telefone" name="telefone" value="<?php echo htmlspecialchars($empresa['telefone']); ?>">
                    </div>
                    <div class="form-group mt-4">
                        <label for="email_empresa"><i class="fas fa-envelope"></i> Email da Empresa</label>
                        <input type="email" class="form-control" id="email_empresa" name="email_empresa" value="<?php echo htmlspecialchars($empresa['email_empresa']); ?>">
                    </div>
                    <div class="form-group mt-4">
                        <label for="nome_contato"><i class="fas fa-user"></i> Nome de Contato</label>
                        <input type="text" class="form-control" id="nome_contato" name="nome_contato" value="<?php echo htmlspecialchars($empresa['nome_contato']); ?>">
                    </div>
                    <div class="form-group mt-4">
                        <label for="telefone_contato"><i class="fas fa-phone"></i> Telefone de Contato</label>
                        <input type="tel" class="form-control" id="telefone_contato" name="telefone_contato" value="<?php echo htmlspecialchars($empresa['telefone_contato']); ?>">
                    </div>
                    <div class="form-group mt-4">
                        <label for="email_contato"><i class="fas fa-envelope"></i> Email de Contato</label>
                        <input type="email" class="form-control" id="email_contato" name="email_contato" value="<?php echo htmlspecialchars($empresa['email_contato']); ?>">
                    </div>
                    <div class="button-container_left mt-4">    
                        <button type="submit" class="btn btn-success">Guardar Informações</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="messageModal" class="modal">
    <div class="modal-content">
        <h2 id="modalTitle"></h2>
        <p id="modalMessage"></p>
        <button id="okButton" class="btn btn-success">Ok</button>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    $(document).ready(function(){
        $('.nav-link').on('click', function(e){
            e.preventDefault();
            var target = $(this).attr('href');
            $('.config-section').hide();
            $(target).show();
        });

        $('#mostrarFormularioServico').on('click', function(){
            $('#formularioServico').toggle();
        });

        $('#cancelarFormulario').on('click', function(){
            $('#formularioServico').hide();
        });

        // Novo código para o modal de eliminação
        $('.btn-danger').on('click', function(e){
            e.preventDefault();
            var servicoId = $(this).data('id');
            var servicoNome = $(this).data('nome');
            $('#modalTitle').text('Eliminar ' + servicoNome);
            $('#confirmarEliminacao').data('id', servicoId);
            $('#eliminarServicoModal').show();
        });

        $('#cancelarEliminacao').on('click', function(){
            $('#eliminarServicoModal').hide();
        });

        $('#confirmarEliminacao').on('click', function(){
            var servicoId = $(this).data('id');
            window.location.href = 'eliminar_servico.php?id=' + servicoId;
        });
    });
</script>
<style>
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.4);
}

.modal-content {
    background-color: #fefefe;
    margin: 15% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
    max-width: 500px;
    text-align: center;
}

#okButton {
    margin-top: 20px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('show_message') === '1') {
        var modal = document.getElementById('messageModal');
        var modalTitle = document.getElementById('modalTitle');
        var modalMessage = document.getElementById('modalMessage');
        var okButton = document.getElementById('okButton');

        <?php if (isset($_SESSION['success_message'])): ?>
            modalTitle.textContent = 'Sucesso';
            modalMessage.textContent = '<?php echo $_SESSION['success_message']; ?>';
            <?php unset($_SESSION['success_message']); ?>
        <?php elseif (isset($_SESSION['error_message'])): ?>
            modalTitle.textContent = 'Erro';
            modalMessage.textContent = '<?php echo $_SESSION['error_message']; ?>';
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        modal.style.display = 'block';

        okButton.onclick = function() {
            modal.style.display = 'none';
            // Remove a flag da URL
            window.history.replaceState({}, document.title, window.location.pathname + '?id=<?php echo $empresa_id; ?>');
        }
    }
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Código existente...

    // Adicione este novo código
    var hash = window.location.hash;
    if (hash) {
        // Remove o '#' do início
        var targetId = hash.substring(1);
        var targetElement = document.getElementById(targetId);
        if (targetElement) {
            // Oculta todas as seções
            var sections = document.getElementsByClassName('config-section');
            for (var i = 0; i < sections.length; i++) {
                sections[i].style.display = 'none';
            }
            // Mostra a seção alvo
            targetElement.style.display = 'block';
            // Rola até a seção
            targetElement.scrollIntoView({behavior: 'smooth'});
            // Atualiza a navegação
            var navLinks = document.getElementsByClassName('nav-link');
            for (var i = 0; i < navLinks.length; i++) {
                navLinks[i].classList.remove('active');
                if (navLinks[i].getAttribute('href') === hash) {
                    navLinks[i].classList.add('active');
                }
            }
        }
    }
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var hash = window.location.hash;
    if (hash) {
        // Remove o '#' do início
        var targetId = hash.substring(1);
        var targetElement = document.getElementById(targetId);
        if (targetElement) {
            // Oculta todas as seções
            var sections = document.getElementsByClassName('config-section');
            for (var i = 0; i < sections.length; i++) {
                sections[i].style.display = 'none';
            }
            // Mostra a seção alvo
            targetElement.style.display = 'block';
            // Rola até a seção
            targetElement.scrollIntoView({behavior: 'smooth'});
            // Atualiza a navegação
            var navLinks = document.getElementsByClassName('nav-link');
            for (var i = 0; i < navLinks.length; i++) {
                navLinks[i].classList.remove('active');
                if (navLinks[i].getAttribute('href') === hash) {
                    navLinks[i].classList.add('active');
                }
            }
        }
    }
});
</script>
<script>
$(document).ready(function() {
    $('#mostrarFormularioPortfolio').on('click', function() {
        $('#formularioPortfolio').toggle();
    });

    $('#cancelarFormularioPortfolio').on('click', function() {
        $('#formularioPortfolio').hide();
    });

    // Nova função para manejar a eliminação do portfólio com modal
    $('.eliminar-portfolio').on('click', function(e) {
        e.preventDefault();
        var imagemId = $(this).data('id');
        $('#portfolioModalTitle').text('Eliminar Imagem');
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

<?php
include '../includes/footer.php';
?>
