<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!eAdmin()) {
    header("Location: ../login.php");
    exit;
}

$sql = "SELECT e.id, e.nome_empresa, u.email FROM empresas e JOIN usuarios u ON e.usuario_id = u.id";
$result = $conn->query($sql);

include '../admin/includes/header_admin.php';
include '../admin/includes/functions_admin.php';
?>

<!-- TOPBAR -->
<div class="white-background">
    <div class="container-fluid">
        <div class="header-container">
            <div class="logo-container">
                <img src="../imagens/Logotipo_freebox.png" alt="Logotipo" style="height:62px;">
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

<!-- CONTEÚDO PRINCIPAL -->
<div class="container-fluid mt-4">
    <div class="dashboard-container">

        <!-- Toolbar: Adicionar + Pesquisa -->
        <div class="dashboard-toolbar">
            <a href="/projeto/register.php" class="btn btn-freebox-blue">+ Adicionar Empresa</a>
            <div class="search-box">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" id="searchInput" placeholder="Pesquisar empresa..." onkeyup="filtrarEmpresas()">
            </div>
        </div>

        <!-- Tabela -->
        <div class="table-responsive">
            <table class="table table-striped table-hover" id="tabelaEmpresas">
                <thead>
                    <tr>
                        <th>Nome da Empresa</th>
                        <th>Email</th>
                        <th>Ações</th>
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
            <p id="semResultados" style="display:none; text-align:center; padding:20px; color:#5a7089; font-size:0.9rem;">Nenhuma empresa encontrada.</p>
        </div>

    </div>
</div>

<!-- Modal Confirmação -->
<div id="modalConfirm" class="modal-confirm" style="display: none;">
    <div class="modal-content">
        <h2>Eliminar <span id="nomeEmpresaEliminar"></span>?</h2>
        <p>Tem a certeza que deseja eliminar esta empresa? Esta ação não pode ser desfeita.</p>
        <p>Digite <strong>CONFIRMAR</strong> para prosseguir:</p>
        <input type="text" id="confirmText" class="form-control mb-3" placeholder="CONFIRMAR">
        <div style="display:flex; gap:8px; margin-top:12px;">
            <button id="btnCancel" class="btn btn-secondary">Cancelar</button>
            <button id="btnConfirm" class="btn btn-danger">Eliminar</button>
        </div>
    </div>
</div>

<!-- Modal Mensagem -->
<div id="messageModal" class="modal" style="display: none;">
    <div class="modal-content">
        <p id="modalMessage"></p>
        <button id="modalOkButton" class="btn btn-freebox-blue" style="margin-top:16px;">OK</button>
    </div>
</div>
<script src="/projeto/js/dashboard.js" defer></script>

<?php
$conn->close();
include '../includes/footer.php';
?>