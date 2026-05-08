<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Verificar se o usuário está autenticado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login.php");
    exit();
}

// Verificar se o ID do serviço foi passado via GET
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error_message'] = "ID do serviço inválido.";
    header("Location: dashboard.php");
    exit();
}

$servico_id = intval($_GET['id']);
$usuario_id = $_SESSION['usuario_id'];

// Buscar informações do serviço e verificar se pertence ao usuário
if ($_SESSION['tipo_usuario'] == 'admin') {
    $sql = "SELECT s.*, e.id as empresa_id, e.nome_empresa 
            FROM servicos s
            JOIN empresas e ON s.empresa_id = e.id
            WHERE s.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $servico_id);
} else {
    $sql = "SELECT s.*, e.id as empresa_id, e.nome_empresa 
            FROM servicos s
            JOIN empresas e ON s.empresa_id = e.id
            WHERE s.id = ? AND e.usuario_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $servico_id, $usuario_id);
}

$stmt->execute();
$result = $stmt->get_result();
$servico = $result->fetch_assoc();

if (!$servico) {
    $_SESSION['error_message'] = "Serviço não encontrado ou você não tem permissão para editá-lo.";
    header("Location: dashboard.php");
    exit();
}

// Processar o formulário quando enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome_servico = $_POST['nome_servico'];
    $titulo_servico = $_POST['titulo_servico'];
    $descricao_servico = $_POST['descricao_servico'];

    $update_sql = "UPDATE servicos SET nome_servico = ?, titulo_servico = ?, descricao_servico = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("sssi", $nome_servico, $titulo_servico, $descricao_servico, $servico_id);

    if ($update_stmt->execute()) {
        $_SESSION['success_message'] = "Serviço atualizado com sucesso.";
        header("Location: empresa_servicos.php?id=" . $servico['empresa_id'] . "&show_message=1#servicos");
        exit();
    } else {
        $_SESSION['error_message'] = "Erro ao atualizar o serviço: " . $conn->error;
    }
}

include '../includes/header.php';
include '../admin/includes/header_admin.php';
?>

<link rel="stylesheet" href="/projeto/css/editar_servico.css">

<div class="white-background">
    <div class="container-fluid">
        <div class="header-container">
            <div class="logo-container">
                <img src="../imagens/Logotipo_freebox.png" style="height:75px;">
            </div>

            <div class="title-container">
                <h4><?= htmlspecialchars($servico['nome_empresa']); ?></h4>
            </div>

            <div class="buttons-container">
                <a href="../logout.php" class="btn btn-danger">
                    Logout
                </a>
            </div>
        </div>
    </div>
</div>

<div class="separator"></div>

<div class="container editar-servico-container">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="editar-servico-header">
                <h5>Editar Serviço</h5>
            </div>

            <div class="editar-servico-card">
                <form method="POST" action="editar_servico.php?id=<?php echo $servico_id; ?>">




                    <div class="form-group mt-4">
                        <label for="nome_servico">Nome do Serviço</label>
                        <input type="text" class="form-control" id="nome_servico" name="nome_servico"
                            value="<?php echo htmlspecialchars($servico['nome_servico']); ?>" required>
                    </div>

                    <div class="form-group mt-4">
                        <label for="titulo_servico">Título do Serviço</label>
                        <input type="text" class="form-control" id="titulo_servico" name="titulo_servico"
                            value="<?php echo htmlspecialchars($servico['titulo_servico']); ?>" required>
                    </div>

                    <div class="form-group mt-4">
                        <label for="descricao_servico">Descrição do Serviço</label>
                        <textarea class="form-control" id="descricao_servico" name="descricao_servico" rows="3" required><?php echo htmlspecialchars($servico['descricao_servico']); ?></textarea>
                    </div>

                    <div class="editar-servico-buttons mt-4">
                        <a href="empresa_servicos.php?id=<?php echo $servico['empresa_id']; ?>#servicos" class="btn btn-secondary">
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-success">
                            Guardar
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>