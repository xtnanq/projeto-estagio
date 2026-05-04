<?php
// resto do código...
// register.php - Página de registo de novos usuários

// Incluir arquivos necessários
require_once 'config/database.php';
require_once 'includes/functions.php';
?>

<?php
iniciarSessao();

// Processar formulário de registo
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = limparDados($_POST['nome']);
    $email = limparDados($_POST['email']);
    $senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];
    $nome_empresa = limparDados($_POST['nome_empresa']);
    $morada = limparDados($_POST['morada']);
    $codigo_postal = limparDados($_POST['codigo_postal']);
    $telefone = limparDados($_POST['telefone']);
    $aceita_politica = isset($_POST['aceita_politica']) ? 1 : 0;
    
    // Validação básica
    if (empty($nome) || empty($email) || empty($senha) || empty($confirmar_senha) || empty($nome_empresa) || 
        empty($morada) || empty($codigo_postal) || empty($telefone)) {
        $erro = "Por favor, preencha todos os campos.";
    } elseif ($senha !== $confirmar_senha) {
        $erro = "As senhas não coincidem.";
    } elseif (strlen($senha) < 6) {
        $erro = "A senha deve ter pelo menos 6 caracteres.";
    } elseif (!$aceita_politica) {
        $erro = "Você deve aceitar a Política de Privacidade para se registar.";
    } else {
        // Verificar se o email já está em uso
        $sql = "SELECT id FROM usuarios WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $erro = "Já existe um registo com este e-mail.";
        } else {
            // Hash da senha
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            
            // Iniciar transação
            $conn->begin_transaction();
            
            try {
                // Inserir o usuário
                $sql = "INSERT INTO usuarios (nome, email, senha, tipo) VALUES (?, ?, ?, 'cliente')";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sss", $nome, $email, $senha_hash);
                $stmt->execute();
                
                // Obter o ID do usuário inserido
                $usuario_id = $conn->insert_id;
                
                // Inserir a empresa
                $sql = "INSERT INTO empresas (usuario_id, nome_empresa, morada, codigo_postal, telefone) VALUES (?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("issss", $usuario_id, $nome_empresa, $morada, $codigo_postal, $telefone);
                $stmt->execute();
                
                // Finalizar transação
                $conn->commit();
                
                // Enviar mensagem de sucesso
                mostrarAlerta("Registo realizado com sucesso. Faça login para continuar.", "success");
                
                // Redirecionar para a página de login
                header("Location: login.php");
                exit;
                
            } catch (Exception $e) {
                // Reverter transação em caso de erro
                $conn->rollback();
                $erro = "Erro ao registrar: " . $e->getMessage();
            }
        }
    }
}

// Incluir o cabeçalho
include 'includes/header.php';
?>

<div class="container login-container">
    <div class="card">
        <div class="card-header text-center">
            <h2><i class="fas fa-user-plus"></i> Registo de Cliente</h2>
            <p class="mb-0">Crie sua conta para aceder</p>
        </div>
        <div class="card-body p-4">
            <?php if (!empty($erro)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $erro; ?>
                </div>
            <?php endif; ?>
            
            <form method="post" action="">
                <div class="mb-3">
                    <label for="nome_empresa" class="form-label"><i class="fas fa-building"></i> Nome da Empresa:</label>
                    <input type="text" class="form-control" id="nome_empresa" name="nome_empresa" required>
                </div>
                
                <div class="mb-3">
                    <label for="morada" class="form-label"><i class="fas fa-map-marker-alt"></i> Morada:</label>
                    <input type="text" class="form-control" id="morada" name="morada" required>
                </div>
                
                <div class="mb-3">
                    <label for="codigo_postal" class="form-label"><i class="fas fa-mail-bulk"></i> Código Postal:</label>
                    <input type="text" class="form-control" id="codigo_postal" name="codigo_postal" required>
                </div>
                
                <div class="mb-3">
                    <label for="email" class="form-label"><i class="fas fa-envelope"></i> E-mail do LOGIN/empresa:</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                
                <div class="mb-3">
                    <label for="nome" class="form-label"><i class="fas fa-user"></i> Nome Contacto:</label>
                    <input type="text" class="form-control" id="nome" name="nome" required>
                </div>
                
                <div class="mb-3">
                    <label for="telefone" class="form-label"><i class="fas fa-phone"></i> Telefone:</label>
                    <input type="tel" class="form-control" id="telefone" name="telefone" required>
                </div>
                
                <div class="mb-3">
                    <label for="senha" class="form-label"><i class="fas fa-lock"></i> Senha:</label>
                    <input type="password" class="form-control" id="senha" name="senha" required>
                    <small class="form-text text-muted">A senha deve ter pelo menos 6 caracteres.</small>
                </div>
                
                <div class="mb-3">
                    <label for="confirmar_senha" class="form-label"><i class="fas fa-check"></i> Confirmar Senha:</label>
                    <input type="password" class="form-control" id="confirmar_senha" name="confirmar_senha" required>
                </div>
                
                <div class="mb-4 form-check">
                    <input type="checkbox" class="form-check-input" id="aceita_politica" name="aceita_politica" required>
                    <label class="form-check-label" for="aceita_politica">
                        Aceito que as minhas informações sejam processadas conforme descrito na 
                        <a href="https://freemenu.pt/politica-de-privacidade/" target="_blank">Política de Privacidade</a>
                    </label>
                </div>
                
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="fas fa-user-plus"></i> Registar
                    </button>
                </div>
            </form>
            
            <div class="text-center mt-4">
                <p>Já tem uma conta? <a href="login.php">Faça login</a></p>
            </div>
        </div>
    </div>
</div>

<?php
// Incluir o rodapé
include 'includes/footer.php';
?>