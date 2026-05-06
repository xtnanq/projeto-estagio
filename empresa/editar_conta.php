<?php
session_start();

require_once '../config/database.php';
require_once '../includes/functions.php';

if (!estaLogado()) {
    header("Location: ../login.php");
    exit;
}

if (!eCliente()) {
    header("Location: ../admin/dashboard.php");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$mensagem = '';

$sql = "SELECT nome, email FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();
$stmt->close();

if (!$usuario) {
    header("Location: ../login.php");
    exit;
}

$sql_empresa = "SELECT id, nome_empresa FROM empresas WHERE usuario_id = ?";
$stmt_empresa = $conn->prepare($sql_empresa);
$stmt_empresa->bind_param("i", $usuario_id);
$stmt_empresa->execute();
$result_empresa = $stmt_empresa->get_result();
$empresa = $result_empresa->fetch_assoc();
$stmt_empresa->close();

if (!$empresa) {
    $_SESSION['error_message'] = "Não foi encontrada nenhuma empresa associada à sua conta.";
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = limparDados($_POST['nome'] ?? '');
    $email = limparDados($_POST['email'] ?? '');
    $nova_senha = $_POST['nova_senha'] ?? '';

    if (empty($nome) || empty($email)) {
        $mensagem = "Erro: preenche o nome e o email.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensagem = "Erro: email inválido.";
    } elseif (!empty($nova_senha) && strlen($nova_senha) < 6) {
        $mensagem = "Erro: a nova palavra-passe deve ter pelo menos 6 caracteres.";
    } else {
        try {
            if (!empty($nova_senha)) {
                $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);

                $sql_update = "UPDATE usuarios SET nome = ?, email = ?, senha = ? WHERE id = ?";
                $stmt_update = $conn->prepare($sql_update);
                $stmt_update->bind_param("sssi", $nome, $email, $senha_hash, $usuario_id);
            } else {
                $sql_update = "UPDATE usuarios SET nome = ?, email = ? WHERE id = ?";
                $stmt_update = $conn->prepare($sql_update);
                $stmt_update->bind_param("ssi", $nome, $email, $usuario_id);
            }

            if ($stmt_update->execute()) {
                $_SESSION['nome_usuario'] = $nome;
                $_SESSION['email_usuario'] = $email;

                $usuario['nome'] = $nome;
                $usuario['email'] = $email;

                $mensagem = "Conta atualizada com sucesso!";
            } else {
                $mensagem = "Erro: não foi possível atualizar a conta.";
            }

            $stmt_update->close();
        } catch (Exception $e) {
            $mensagem = "Erro: " . $e->getMessage();
        }
    }
}

include '../includes/header.php';
include '../admin/includes/header_admin.php';
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
.center-card {
    display: flex;
    justify-content: center;
    align-items: flex-start;
    min-height: 70vh;
}

.account-card {
    width: 100%;
    max-width: 750px;
    border: none;
    border-radius: 16px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    background: #ffffff;
    overflow: hidden;
}

.account-card .card-header {
    background: linear-gradient(135deg, #1565C0 0%, #1E88E5 70%, #42A5F5 100%);
    color: white;
    padding: 26px;
}

.account-card .card-header h3 {
    font-weight: 700;
}

.button-container {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

.form-control {
    border-radius: 10px;
    padding: 10px 14px;
}

label {
    font-weight: 500;
}
</style>

<div class="white-background">
    <div class="container-fluid">
        <div class="header-container">
            <div class="logo-container">
                <img src="../imagens/Logotipo_freebox.png" style="height:75px;">
            </div>

            <div class="title-container">
                <h4><?= htmlspecialchars($empresa['nome_empresa']); ?></h4>
            </div>

            <div class="buttons-container">
                <a href="../logout.php" class="btn btn-danger">
                    <i class="fas fa-power-off"></i> Logout
                </a>
            </div>
        </div>
    </div>
</div>

<div class="separator"></div>

<div class="container-fluid mt-4">
    <div class="center-card">
        <div class="card account-card">
            <div class="card-header">
                <h3 class="text-center mb-0">
                    <i class="fas fa-user-gear"></i> Editar Conta
                </h3>
            </div>

            <div class="card-body p-4">

                <?php if (!empty($mensagem)): ?>
                    <div class="alert <?= strpos($mensagem, 'Erro') !== false ? 'alert-danger' : 'alert-success'; ?>">
                        <?= htmlspecialchars($mensagem); ?>
                    </div>
                <?php endif; ?>

                <form method="POST">

                    <div class="form-group">
                        <label for="nome">
                            <i class="fas fa-user"></i> Nome
                        </label>
                        <input type="text" class="form-control" id="nome" name="nome"
                               value="<?= htmlspecialchars($usuario['nome']); ?>" required>
                    </div>

                    <div class="form-group mt-4">
                        <label for="email">
                            <i class="fas fa-envelope"></i> Email
                        </label>
                        <input type="email" class="form-control" id="email" name="email"
                               value="<?= htmlspecialchars($usuario['email']); ?>" required>
                    </div>

                    <div class="form-group mt-4">
                        <label for="nova_senha">
                            <i class="fas fa-lock"></i> Nova Palavra-passe
                        </label>
                        <input type="password" class="form-control" id="nova_senha" name="nova_senha" minlength="6">
                        <small class="form-text text-muted">
                            Deixa em branco se não quiseres alterar.
                        </small>
                    </div>

                    <div class="button-container mt-5">
                        <a href="dashboard.php" class="btn btn-secondary">
                            Voltar
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