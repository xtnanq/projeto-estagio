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
    } elseif (isset($_POST['adicionar_portfolio'])) {
        // Tratamento da seção de portfólio
        $descricao_imagem = $_POST['descricao_imagem'] ?? '';

    if (isset($_FILES['portfolio_imagem']) && $_FILES['portfolio_imagem']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = 'imagens/' . $empresa['nome_empresa'] . '/';
        
        // Crie o diretório se ele não existir
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_name = basename($_FILES['portfolio_imagem']['name']);
        $uploaded_file = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['portfolio_imagem']['tmp_name'], $uploaded_file)) {
            $insert_portfolio_sql = "INSERT INTO portfolio (empresa_id, imagem, descricao_imagem) VALUES (?, ?, ?)";
            $insert_portfolio_stmt = $conn->prepare($insert_portfolio_sql);
            $insert_portfolio_stmt->bind_param("iss", $empresa_id, $uploaded_file, $descricao_imagem);

            if ($insert_portfolio_stmt->execute()) {
                $_SESSION['success_message'] = "Imagem adicionada ao portfólio com sucesso!";
                header("Location: configurar_empresa.php?id=" . $empresa_id . "&show_message=1#portfolio");
                exit();
            } else {
                $_SESSION['error_message'] = "Erro ao adicionar imagem: " . $conn->error;
                header("Location: configurar_empresa.php?id=" . $empresa_id . "&show_message=1#portfolio");
                exit();
            }
        } else {
            $_SESSION['error_message'] = "Erro ao mover o upload.";
            header("Location: configurar_empresa.php?id=" . $empresa_id . "&show_message=1#portfolio");
            exit();
        }
    } else {
        $_SESSION['error_message'] = "Erro no upload: " . $_FILES['portfolio_imagem']['error'];
        header("Location: configurar_empresa.php?id=" . $empresa_id . "&show_message=1#portfolio");
        exit();
    }
    } else {
        // Tratamento da seção de informações da empresa
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
            header("Location: configurar_empresa.php?id=" . $empresa_id . "&show_message=1#informacoes");
            exit();
        } else {
            $_SESSION['error_message'] = "Erro ao atualizar as informações da empresa: " . $conn->error;
            header("Location: configurar_empresa.php?id=" . $empresa_id . "&show_message=1#informacoes");
            exit();
        }
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
    .img-thumbnail {
        width: 100px;
        height: 100px;
        object-fit: cover;
        margin-right: 5px; /* Adiciona espaço entre a imagem e o texto */
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
 

            <!-- Outras seções como Portfólio e Website -->
            <div id="portfolio" class="config-section" style="display:none;">
                <div class="text-center">
                    <h4>Portfólio</h4>
                </div>

                    <!-- Botão para mostrar formulário de adição -->
                <div class="mt-4">
                    <button id="mostrarFormularioPortfolio" class="btn btn-freebox-blue">Adicionar Imagem</button>
                </div>

                <!-- Formulário para adicionar nova imagem -->
                <div class="card mt-4" id="formularioPortfolio" style="display:none;">
                    <div class="card-body">
                        <h5 class="card-title">Adicionar Nova Imagem ao Portfólio</h5>
                        <form method="POST" action="" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="portfolio_imagem">Imagem</label>
                                <input type="file" class="form-control-file" id="portfolio_imagem" name="portfolio_imagem" required>
                            </div>
                            <div class="form-group">
                                <label for="descricao_imagem">Descrição da Imagem</label>
                                <textarea class="form-control" id="descricao_imagem" name="descricao_imagem"></textarea>
                            </div>
                            <div class="button-container_left mt-4">
                                <button type="button" id="cancelarFormularioPortfolio" class="btn btn-secondary">Cancelar</button>
                                <button type="submit" name="adicionar_portfolio" class="btn btn-success">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Listar imagens existentes -->
                <?php

                // Recolhe todas as imagens associadas à empresa atual
                $portfolio_sql = "SELECT * FROM portfolio WHERE empresa_id = ?";
                $portfolio_stmt = $conn->prepare($portfolio_sql);
                $portfolio_stmt->bind_param("i", $empresa_id);
                $portfolio_stmt->execute();
                $portfolio_result = $portfolio_stmt->get_result();

                while ($portfolio = $portfolio_result->fetch_assoc()):
                    $imagemPath = $portfolio['imagem'];
                ?>

                <div class="card mt-3">
                    <div class="card-body p-2">
                        <img src="<?php echo htmlspecialchars($imagemPath); ?>" alt="Imagem do Portfólio" class="img-thumbnail mb-1">
                        <div class="d-flex justify-content-between align-items-center">
                            <p class="mb-0"><?php echo htmlspecialchars($portfolio['descricao_imagem']); ?></p>
                            <button class="btn btn-danger btn-sm eliminar-portfolio" data-id="<?php echo $portfolio['id']; ?>">Eliminar</button>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>

            <!-- seções como  Website -->
            <div id="website" class="config-section" style="display:none;">
                <div class="text-center">
                    <h4>Configurações do Website</h4>
                </div>    
                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="endereco_url">Endereço URL</label>
                        <input type="text" class="form-control" id="endereco_url" name="endereco_url" readonly value="<?php echo htmlspecialchars($empresa['nome_empresa']) . '.projeto.pt'; ?>">
                    </div>
                    <div class="form-group">
                        <label for="logotipo">Logotipo da Empresa</label>
                        <input type="file" class="form-control-file" id="logotipo" name="logotipo">
                    </div>
                    <div class="form-group">
                        <label for="capa_empresa">Imagem da Capa da Empresa</label>
                        <input type="file" class="form-control-file" id="capa_empresa" name="capa_empresa">
                    </div>
                    <div class="form-group">
                        <label for="descricao_empresa">Descrição da Empresa</label>
                        <textarea class="form-control" id="descricao_empresa" name="descricao_empresa"><?php echo htmlspecialchars($website['descricao_empresa'] ?? ''); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="link_facebook">Facebook</label>
                        <input type="url" class="form-control" id="link_facebook" name="link_facebook" value="<?php echo htmlspecialchars($website['link_facebook'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="link_instagram">Instagram</label>
                        <input type="url" class="form-control" id="link_instagram" name="link_instagram" value="<?php echo htmlspecialchars($website['link_instagram'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="link_x">X (Twitter)</label>
                        <input type="url" class="form-control" id="link_x" name="link_x" value="<?php echo htmlspecialchars($website['link_x'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="localizacao">Localização (Google Maps)</label>
                        <iframe src="https://www.google.com/maps/embed/v1/place?key=YOUR_API_KEY&q=<?php echo urlencode($empresa['morada']); ?>" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                    </div>
                    <div class="button-container_left mt-4">    
                        <button type="submit" class="btn btn-success">Guardar Configurações</button>
                    </div>
                </form>
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
            
<div id="eliminarPortfolioModal" class="modal">
    <div class="modal-content">
        <h2 id="portfolioModalTitle"></h2>
        <p>Tem certeza que deseja eliminar esta imagem do portfólio? Esta ação não pode ser desfeita.</p>
        <div class="button-container mt-4">
            <button id="cancelarEliminacaoPortfolio" class="btn btn-secondary">Cancelar</button>
            <button id="confirmarEliminacaoPortfolio" class="btn btn-danger">Eliminar</button>
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

</script>
</body>
</html>
