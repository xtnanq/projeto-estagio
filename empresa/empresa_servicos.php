<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Verificar se o ID da empresa foi passado via GET
if (isset($_GET['id'])) {
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

if (!$empresa) {
    header("Location: dashboard.php");
    exit();
}

// Buscar serviços para a empresa
$servicos_sql = "SELECT * FROM servicos WHERE empresa_id = ?";
$servicos_stmt = $conn->prepare($servicos_sql);
$servicos_stmt->bind_param("i", $empresa_id);
$servicos_stmt->execute();
$servicos_result = $servicos_stmt->get_result();

// Processar o formulário quando enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['adicionar_servico'])) {
        // Tratamento da seção de serviços
        $nome_servico = $_POST['nome_servico'] ?? '';
        $titulo_servico = $_POST['titulo_servico'] ?? '';
        $descricao_servico = $_POST['descricao_servico'] ?? '';

        $insert_servico_sql = "INSERT INTO servicos (empresa_id, nome_servico, titulo_servico, descricao_servico) VALUES (?, ?, ?, ?)";
        $insert_servico_stmt = $conn->prepare($insert_servico_sql);
        $insert_servico_stmt->bind_param("isss", $empresa_id, $nome_servico, $titulo_servico, $descricao_servico);

        if ($insert_servico_stmt->execute()) {
            $_SESSION['success_message'] = "Serviço adicionado com sucesso!";
            header("Location: configurar_empresa.php?id=" . $empresa_id . "&show_message=1#servicos");
            exit();
        } else {
            $_SESSION['error_message'] = "Erro ao adicionar serviço: " . $conn->error;
            header("Location: configurar_empresa.php?id=" . $empresa_id . "&show_message=1#servicos");
            exit();
        }
    }
}

include '../includes/header.php';
include '../admin/includes/header_admin.php';
// include '../admin/includes/functions_admin.php';
?>
<style>
    .button-container_left {
        display: flex;
        justify-content: flex-end;
    }

    .button-container_left .btn {
        margin-left: 10px;
    }

    .img-thumbnail {
        width: 100px;
        height: 100px;
        object-fit: cover;
        margin-right: 5px;
        /* Adiciona espaço entre a imagem e o texto */
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
                <a href="../logout.php" class="btn btn-outline-danger custom-logout" id="logoutBtn"><i class="fas fa-power-off"></i> Logout</a>
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
            <?php
            if (isset($success_message)) {
                echo "<div class='alert alert-success'>" . $success_message . "</div>";
            }
            if (isset($error_message)) {
                echo "<div class='alert alert-danger'>" . $error_message . "</div>";
            }
            ?>

            <div id="servicos" class="config-section" style="display:none;">
                <div class="text-center">
                    <h4>Serviços</h4>
                </div>

                <!-- Botão para mostrar formulário de adição -->
                <div class="mt-4">
                    <button id="mostrarFormularioServico" class="btn btn-freebox-blue">Adicionar Serviço</button>
                </div>

                <!-- Formulário para adicionar novo serviço -->
                <div class="card mt-4" id="formularioServico" style="display:none;">
                    <div class="card-body">
                        <h5 class="card-title">Adicionar Novo Serviço</h5>
                        <form method="POST" action="">
                            <div class="form-group">
                                <label for="nome_servico">Nome do Serviço</label>
                                <input type="text" class="form-control" id="nome_servico" name="nome_servico" required>
                            </div>
                            <div class="form-group">
                                <label for="titulo_servico">Título do Serviço</label>
                                <input type="text" class="form-control" id="titulo_servico" name="titulo_servico" required>
                            </div>
                            <div class="form-group">
                                <label for="descricao_servico">Descrição do Serviço</label>
                                <textarea class="form-control" id="descricao_servico" name="descricao_servico"></textarea>
                            </div>
                            <div class="button-container_left mt-4">
                                <button type="button" id="cancelarFormulario" class="btn btn-secondary">Cancelar</button>
                                <button type="submit" name="adicionar_servico" class="btn btn-success">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Listar serviços existentes -->
                <?php while ($servico = $servicos_result->fetch_assoc()): ?>
                    <div class="card mt-3">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <span><?php echo htmlspecialchars($servico['nome_servico']); ?></span>
                            <div class="button-container">
                                <a href="editar_servico.php?id=<?php echo $servico['id']; ?>" class="btn btn-success">Editar</a>
                                <button class="btn btn-danger" data-id="<?php echo $servico['id']; ?>" data-nome="<?php echo htmlspecialchars($servico['nome_servico']); ?>">Eliminar</button>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</div>

<div id="eliminarServicoModal" class="modal">
    <div class="modal-content">
        <h2 id="modalTitle"></h2>
        <p>Tem certeza que deseja eliminar este serviço? Esta ação não pode ser desfeita.</p>
        <div class="button-container mt-4">
            <button id="cancelarEliminacao" class="btn btn-secondary">Cancelar</button>
            <button id="confirmarEliminacao" class="btn btn-danger">Eliminar</button>
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
    $(document).ready(function() {
        $('.nav-link').on('click', function(e) {
            var href = $(this).attr('href');

            // Só intercepta links com #
            if (href.startsWith('#')) {
                e.preventDefault();
                $('.config-section').hide();
                $(href).show();
            }
        });

        $('#mostrarFormularioServico').on('click', function() {
            $('#formularioServico').toggle();
        });

        $('#cancelarFormulario').on('click', function() {
            $('#formularioServico').hide();
        });

        // Novo código para o modal de eliminação
        $('.btn-danger').on('click', function(e) {
            e.preventDefault();
            var servicoId = $(this).data('id');
            var servicoNome = $(this).data('nome');
            $('#modalTitle').text('Eliminar ' + servicoNome);
            $('#confirmarEliminacao').data('id', servicoId);
            $('#eliminarServicoModal').show();
        });

        $('#cancelarEliminacao').on('click', function() {
            $('#eliminarServicoModal').hide();
        });

        $('#confirmarEliminacao').on('click', function() {
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
        background-color: rgba(0, 0, 0, 0.4);
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
                targetElement.scrollIntoView({
                    behavior: 'smooth'
                });
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
                targetElement.scrollIntoView({
                    behavior: 'smooth'
                });
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
</body>

</html>