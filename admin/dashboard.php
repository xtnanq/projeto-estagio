<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Verificar se o usuário é admin
if (!eAdmin()) {
    header("Location: ../login.php");
    exit;
}

// Buscar empresas do banco de dados
$sql = "SELECT e.id, e.nome_empresa, u.email FROM empresas e JOIN usuarios u ON e.usuario_id = u.id";
$result = $conn->query($sql);

include '../includes/header.php';
include '../admin/includes/header_admin.php';
include '../admin/includes/functions_admin.php';
?>

<div class="white-background">
    <div class="container-fluid">
        <div class="header-container">
            <div class="logo-container">
                <img src="../imagens/Logotipo_freebox.png" alt="Logotipo" style="height: 75px;">
            </div>
            <div class="title-container">
                <h3>Dashboard</h3>
            </div>
            <div class="buttons-container">
                <a href="editar_admin.php" class="btn btn-success">Editar Admin</a>
                <a href="../logout.php" class="btn btn-danger">Logout</a>
            </div>
        </div>
    </div>
</div>

<div class="separator"></div>

<div class="container-fluid mt-4">
    <div class="dashboard-container dashboard-blue-container">
        <div class="row mb-3">
            <div class="col-md-12 text-right">
                <a href="/projeto/register.php" class="btn btn-freebox-blue" onclick="window.location.href='/projeto/register.php'; return false;">Adicionar Empresa</a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Nome da Empresa</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['nome_empresa']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="editar_empresa.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-success">Editar</a>
                                    <a href="/projeto/empresa/empresa_informacoes.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-freebox-blue">Configurar</a>
                                    <a href="/projeto/sites/index.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-info" target="_blank">Ver Website</a>
                                    <button class="btn btn-sm btn-danger" onclick="confirmarExclusao(<?php echo (int)$row['id']; ?>, '<?php echo addslashes(htmlspecialchars($row['nome_empresa'])); ?>')">Eliminar</button>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="modalConfirm" class="modal-confirm" style="display: none;">
    <div class="modal-content">
        <h2>Eliminar <span id="nomeEmpresaEliminar"></span> ?</h2>
        <p>Tem certeza que deseja eliminar esta empresa? Esta ação não pode ser desfeita.</p>
        <p>Digite CONFIRMAR para prosseguir:</p>
        <input type="text" id="confirmText" class="form-control mb-3">
        <button id="btnCancel" class="btn btn-secondary">Cancelar</button>
        <button id="btnConfirm" class="btn btn-danger">Eliminar</button>
    </div>
</div>

<div id="messageModal" class="modal" style="display: none;">
    <div class="modal-content">
        <p id="modalMessage"></p>
        <button id="modalOkButton" class="btn btn-primary">OK</button>
    </div>
</div>

<?php
// Gerar o script JavaScript
gerarScriptEmpresasAdmin();

// Fechar a conexão com o banco de dados
$conn->close();

// Incluir o footer
include '../includes/footer.php';
?>