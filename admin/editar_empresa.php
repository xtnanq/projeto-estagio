<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Verificar se o usuário é admin
if (!eAdmin()) {
    header("Location: ../login.php");
    exit;
}

$empresa_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Buscar informações da empresa e do usuário associado
$sql = "SELECT u.id as usuario_id, u.email as usuario_email, e.id as empresa_id, e.nome_empresa 
        FROM empresas e 
        JOIN usuarios u ON e.usuario_id = u.id 
        WHERE e.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $empresa_id);
$stmt->execute();
$result = $stmt->get_result();
$empresa = $result->fetch_assoc();
$stmt->close();

if (!$empresa) {
    header("Location: dashboard.php?erro=empresa_nao_encontrada");
    exit;
}

$usuario_id = $empresa['usuario_id'];
$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email_usuario = limparDados($_POST['email_usuario']);
    $nova_senha = $_POST['nova_senha'];

    $conn->begin_transaction();

    try {
        // Atualizar e-mail do usuário
        $sql_usuario = "UPDATE usuarios SET email = ? WHERE id = ?";
        $stmt_usuario = $conn->prepare($sql_usuario);
        $stmt_usuario->bind_param("si", $email_usuario, $usuario_id);
        $stmt_usuario->execute();

        // Atualizar senha se fornecida e válida
        if (!empty($nova_senha)) {
            if (strlen($nova_senha) < 6) {
                throw new Exception("A nova senha deve ter pelo menos 6 caracteres.");
            }

            $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);

            $sql_senha = "UPDATE usuarios SET senha = ? WHERE id = ?";
            $stmt_senha = $conn->prepare($sql_senha);
            $stmt_senha->bind_param("si", $senha_hash, $usuario_id);
            $stmt_senha->execute();
            $stmt_senha->close();
        }

        $conn->commit();
        $mensagem = "Informações da empresa atualizadas com sucesso!";

        // Atualizar valor mostrado no input depois de guardar
        $empresa['usuario_email'] = $email_usuario;

    } catch (Exception $e) {
        $conn->rollback();
        $mensagem = "Erro: " . $e->getMessage();
    }
}

include '../includes/header.php';
include '../admin/includes/header_admin.php';
?>

<link rel="stylesheet" href="/projeto/css/editar_empresa.css">

<div class="container">
    <div class="center-card">
        <div class="card empresa-edit-card">
            <div class="card-header">
                <h2 class="text-center">
                    <i class="fas fa-building"></i> Editar Empresa
                </h2>
            </div>

            <div class="card-body p-4">
                <?php if (!empty($mensagem)): ?>
                    <div class="alert <?php echo strpos($mensagem, 'Erro') !== false ? 'alert-danger' : 'alert-success'; ?>">
                        <?php echo htmlspecialchars($mensagem); ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label for="nome">
                            <i class="fas fa-building"></i> Nome da Empresa
                        </label>
                        <input type="text"
                               class="form-control"
                               id="nome"
                               value="<?php echo htmlspecialchars($empresa['nome_empresa'] ?? ''); ?>"
                               readonly>
                    </div>

                    <div class="form-group mt-4">
                        <label for="email_usuario">
                            <i class="fas fa-envelope"></i> E-mail de Login
                        </label>
                        <input type="email"
                               class="form-control"
                               id="email_usuario"
                               name="email_usuario"
                               value="<?php echo htmlspecialchars($empresa['usuario_email'] ?? ''); ?>"
                               required>
                    </div>

                    <div class="form-group mt-4">
                        <label for="nova_senha">
                            <i class="fas fa-lock"></i> Nova Senha
                        </label>
                        <input type="password"
                               class="form-control"
                               id="nova_senha"
                               name="nova_senha"
                               minlength="6">
                        <small class="form-text text-muted">
                            Deixe em branco para não alterar. A senha deve ter pelo menos 6 caracteres.
                        </small>
                    </div>

                    <div class="button-container mt-5">
                        <a href="dashboard.php" class="btn btn-secondary">
                            Cancelar
                        </a>

                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>