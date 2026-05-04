<?php
// login.php - Página de login do sistema

// Incluir arquivos necessários
require_once 'config/database.php';
require_once 'includes/functions.php';

iniciarSessao();

// Se já estiver logado, redireciona conforme o tipo de usuário
if (estaLogado()) {
    redirecionarUsuario();
}

$erro = '';

// Processar formulário de login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = limparDados($_POST['email']);
    $senha = $_POST['senha'];
    
    // Validação básica
    if (empty($email) || empty($senha)) {
        $erro = "Por favor, preencha todos os campos.";
    } else {
        // Buscar usuário pelo email
        $sql = "SELECT id, nome, email, senha, tipo FROM usuarios WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $usuario = $result->fetch_assoc();
            
            // Verificar senha
            if (password_verify($senha, $usuario['senha'])) {
                // Login bem-sucedido - guardar dados na sessão
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['nome_usuario'] = $usuario['nome'];
                $_SESSION['email_usuario'] = $usuario['email'];
                $_SESSION['tipo_usuario'] = $usuario['tipo'];
                
                // Redirecionar conforme o tipo de usuário
                if ($usuario['tipo'] == 'admin') {
                    header("Location: admin/index.php");
                } else {
                    header("Location: empresa/index.php");
                }
                exit;
            } else {
                $erro = "Senha incorreta.";
            }
        } else {
            $erro = "Usuário não encontrado.";
        }
    }
}

// Incluir o cabeçalho
include 'includes/header.php';
?>

<div class="container login-container">
    <div class="card">
        <div class="card-header text-center">
            <h2><i class="fas fa-lock"></i> Login</h2>
            <p class="mb-0">Introduza as suas credenciais para ter acesso</p>
        </div>
        <div class="card-body p-4">
            <?php if (!empty($erro)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $erro; ?>
                </div>
            <?php endif; ?>
            
            <?php
            // Exibir alertas salvos na sessão (ex: após registo)
            $alerta = obterAlerta();
            if ($alerta): 
            ?>
                <div class="alert alert-<?php echo $alerta['tipo']; ?>">
                    <i class="fas fa-info-circle"></i> <?php echo $alerta['mensagem']; ?>
                </div>
            <?php endif; ?>
            
            <form method="post" action="">
                <div class="mb-3">
                    <label for="email" class="form-label"><i class="fas fa-envelope"></i> E-mail:</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-4">
                    <label for="senha" class="form-label"><i class="fas fa-key"></i> Senha:</label>
                    <input type="password" class="form-control" id="senha" name="senha" required>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-sign-in-alt"></i> Entrar
                    </button>
                </div>
            </form>
            
            <div class="text-center mt-4">
                <p>Ainda não tem uma conta? <a href="register.php">Registe-se Aqui</a></p>
            </div>
        </div>
    </div>
</div>

<?php
// Incluir o rodapé
include 'includes/footer.php';
?>