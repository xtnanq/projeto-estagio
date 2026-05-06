 <?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Verificar se o usuário é admin
if (!eAdmin()) {
    header("Location: ../login.php");
    exit;
}

$admin_id = $_SESSION['usuario_id'];
$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = limparDados($_POST['nome']);
    $email = limparDados($_POST['email']);
    $nova_senha = $_POST['nova_senha'];

    $conn->begin_transaction();

    try {
        $sql = "UPDATE usuarios SET nome = ?, email = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $nome, $email, $admin_id);
        
        if ($stmt->execute()) {
            $_SESSION['nome_usuario'] = $nome;
            $_SESSION['email_usuario'] = $email;
            
            if (!empty($nova_senha)) {
                if (strlen($nova_senha) < 6) {
                    throw new Exception("A nova senha deve ter pelo menos 6 caracteres.");
                }
                $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
                $sql_senha = "UPDATE usuarios SET senha = ? WHERE id = ?";
                $stmt_senha = $conn->prepare($sql_senha);
                $stmt_senha->bind_param("si", $senha_hash, $admin_id);
                
                if ($stmt_senha->execute()) {
                    $mensagem = "Perfil e senha atualizados com sucesso!";
                } else {
                    throw new Exception("Erro ao atualizar a senha.");
                }
                $stmt_senha->close();
            } else {
                $mensagem = "Perfil atualizado com sucesso!";
            }
        } else {
            throw new Exception("Erro ao atualizar o perfil.");
        }
        $stmt->close();

        $conn->commit();
    } catch (Exception $e) {
        $conn->rollback();
        $mensagem = "Erro: " . $e->getMessage();
    }
}

// Buscar informações atuais do admin
$sql = "SELECT nome, email FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
$stmt->close();

include '../includes/header.php';
?>
<link rel="stylesheet" href="../css/includes_editar_admin.css">


<div class="container">
    <div class="center-card">
        <div class="card">
            <div class="card-header">
                <h2 class="text-center">Editar Administrador</h2>
            </div>
            <div class="card-body">
                <?php if (!empty($mensagem)): ?>
                    <div class="alert <?php echo strpos($mensagem, 'Erro') !== false ? 'alert-danger' : 'alert-success'; ?>"><?php echo $mensagem; ?></div>
                <?php endif; ?>
                <form action="editar_admin.php" method="post">
                    <div class="form-group">
                        <label for="nome"><i class="fas fa-user"></i> Nome:</label>
                        <input type="text" class="form-control" id="nome" name="nome" value="<?php echo htmlspecialchars($admin['nome']); ?>" required>
                    </div>
                    
                    <div class="form-group mt-4">
                        <label for="email"><i class="fas fa-envelope"></i> E-mail:</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($admin['email']); ?>" required>
                    </div>
                    
                    <div class="form-group mt-4">
                        <label for="nova_senha"><i class="fas fa-lock"></i> Nova Senha (deixe em branco para não alterar):</label>
                        <input type="password" class="form-control" id="nova_senha" name="nova_senha" minlength="6">
                        <small class="form-text text-muted">A senha deve ter pelo menos 6 caracteres.</small>
                    </div>
                    
                    <div class="button-container mt-5">
                        <a href="dashboard.php" class="btn btn-secondary">Voltar</a>
                        <button type="submit" class="btn btn-success">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
