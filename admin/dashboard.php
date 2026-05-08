<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!eAdmin()) {
    header("Location: ../login.php");
    exit;
}

$sql = "SELECT e.id, e.nome_empresa, u.email 
        FROM empresas e 
        JOIN usuarios u ON e.usuario_id = u.id";
$result = $conn->query($sql);

include '../includes/header.php';
include '../admin/includes/header_admin.php';
include '../admin/includes/functions_admin.php';
?>

<link rel="stylesheet" href="/projeto/css/admin_dashboard.css">

<!-- TOPBAR -->
<div class="white-background">
    <div class="container-fluid">
        <div class="header-container">
            <div class="logo-container">
                <img src="../imagens/Logotipo_freebox.png" alt="Logotipo" class="admin-logo">
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
            <a href="/projeto/register.php" class="btn btn-freebox-blue">
                + Adicionar Empresa
            </a>

            <div class="search-box">
                <svg xmlns="http://www.w3.org/2000/svg"
                    width="16"
                    height="16"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2.2"
                    stroke-linecap="round"
                    stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8" />
                    <line x1="21" y1="21" x2="16.65" y2="16.65" />
                </svg>

                <input type="text"
                    id="searchInput"
                    placeholder="Pesquisar empresa..."
                    onkeyup="filtrarEmpresas()">
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
                                    <a href="editar_empresa.php?id=<?php echo $row['id']; ?>"
                                        class="btn btn-sm btn-success">
                                        Editar
                                    </a>

                                    <a href="/projeto/empresa/empresa_informacoes.php?id=<?php echo $row['id']; ?>"
                                        class="btn btn-sm btn-freebox-blue">
                                        Configurar
                                    </a>

                                    <?php
                                    $url_stmt = $conn->prepare("SELECT url_site FROM website_config WHERE empresa_id = ?");
                                    $url_stmt->bind_param("i", $row['id']);
                                    $url_stmt->execute();
                                    $url_row = $url_stmt->get_result()->fetch_assoc();
                                    $url_stmt->close();
                                    $url_site = $url_row['url_site'] ?? '';
                                    ?>

                                    <?php if (!empty($url_site)): ?>
                                        <a href="/projeto/freebox/<?php echo htmlspecialchars($url_site); ?>"
                                            class="btn btn-sm btn-info"
                                            target="_blank">
                                            Ver Website
                                        </a>
                                    <?php else: ?>
                                        <a class="btn btn-sm btn-secondary"
                                            style="cursor: default; opacity: 0.6;"
                                            title="Cliente ainda não definiu o endereço do site">
                                            Sem Website
                                        </a>
                                    <?php endif; ?>

                                    <button class="btn btn-sm btn-danger"
                                        onclick="confirmarExclusao(<?php echo (int)$row['id']; ?>, '<?php echo addslashes(htmlspecialchars($row['nome_empresa'])); ?>')">
                                        Eliminar
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <p id="semResultados" class="sem-resultados">
                Nenhuma empresa encontrada.
            </p>
        </div>

    </div>
</div>

<!-- Modal Confirmação -->
<div id="modalConfirm" class="modal-confirm">
    <div class="modal-content">
        <h2>Eliminar <span id="nomeEmpresaEliminar"></span>?</h2>

        <p>Tem a certeza que deseja eliminar esta empresa? Esta ação não pode ser desfeita.</p>
        <p>Digite <strong>CONFIRMAR</strong> para prosseguir:</p>

        <input type="text"
            id="confirmText"
            class="form-control mb-3"
            placeholder="CONFIRMAR">

        <div class="modal-actions">
            <button id="btnCancel" class="btn btn-secondary">
                Cancelar
            </button>

            <button id="btnConfirm" class="btn btn-danger">
                Eliminar
            </button>
        </div>
    </div>
</div>

<!-- Modal Mensagem -->
<div id="messageModal" class="modal">
    <div class="modal-content">
        <p id="modalMessage"></p>

        <button id="modalOkButton" class="btn btn-freebox-blue modal-ok-button">
            OK
        </button>
    </div>
</div>

<script>
    function filtrarEmpresas() {
        const input = document.getElementById('searchInput').value.toLowerCase();
        const linhas = document.querySelectorAll('#tabelaEmpresas tbody tr');
        let visiveis = 0;

        linhas.forEach(function(linha) {
            const nome = linha.cells[0].textContent.toLowerCase();
            const email = linha.cells[1].textContent.toLowerCase();

            if (nome.includes(input) || email.includes(input)) {
                linha.style.display = '';
                visiveis++;
            } else {
                linha.style.display = 'none';
            }
        });

        document.getElementById('semResultados').style.display = visiveis === 0 ? 'block' : 'none';
    }
</script>

<?php
gerarScriptEmpresasAdmin();
$conn->close();
include '../includes/footer.php';
?>