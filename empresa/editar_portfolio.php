<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Verificar se o usuário está autenticado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login.php");
    exit();
}

// Verificar se o ID foi passado via GET
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error_message'] = "ID da imagem inválido.";
    header("Location: dashboard.php");
    exit();
}

$portfolio_id = intval($_GET['id']);
$usuario_id = $_SESSION['usuario_id'];

// Buscar informações da imagem e verificar permissão
if ($_SESSION['tipo_usuario'] == 'admin') {
    $sql = "SELECT p.*, e.id as empresa_id, e.nome_empresa 
            FROM portfolio p
            JOIN empresas e ON p.empresa_id = e.id
            WHERE p.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $portfolio_id);
} else {
    $sql = "SELECT p.*, e.id as empresa_id, e.nome_empresa 
            FROM portfolio p
            JOIN empresas e ON p.empresa_id = e.id
            WHERE p.id = ? AND e.usuario_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $portfolio_id, $usuario_id);
}

$stmt->execute();
$result = $stmt->get_result();
$portfolio = $result->fetch_assoc();
$stmt->close();

if (!$portfolio) {
    $_SESSION['error_message'] = "Imagem não encontrada ou sem permissão para editar.";
    header("Location: dashboard.php");
    exit();
}

// Processar o formulário quando enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $descricao_imagem = $_POST['descricao_imagem'] ?? '';
    $nova_imagem = $portfolio['imagem'];

    // Verificar se foi enviada nova imagem
    if (isset($_FILES['portfolio_imagem']) && $_FILES['portfolio_imagem']['error'] == UPLOAD_ERR_OK) {
        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $file_type = mime_content_type($_FILES['portfolio_imagem']['tmp_name']);

        if (!in_array($file_type, $allowed)) {
            $_SESSION['error_message'] = "Apenas imagens JPG, PNG, GIF ou WEBP são permitidas.";
            header("Location: editar_portfolio.php?id=" . $portfolio_id);
            exit();
        }

        if ($_FILES['portfolio_imagem']['size'] > 5 * 1024 * 1024) {
            $_SESSION['error_message'] = "O ficheiro não pode ter mais de 5MB.";
            header("Location: editar_portfolio.php?id=" . $portfolio_id);
            exit();
        }

        $upload_dir = '../imagens/' . $portfolio['empresa_id'] . '/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $ext = pathinfo($_FILES['portfolio_imagem']['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $ext;
        $nova_imagem = '/projeto/imagens/' . $portfolio['empresa_id'] . '/' . $filename;

        if (!move_uploaded_file($_FILES['portfolio_imagem']['tmp_name'], $upload_dir . $filename)) {
            $_SESSION['error_message'] = "Erro ao fazer upload da nova imagem.";
            header("Location: editar_portfolio.php?id=" . $portfolio_id);
            exit();
        }

        // Apagar imagem antiga do servidor
        $imagem_antiga = '..' . str_replace('/projeto', '', $portfolio['imagem']);
        if (file_exists($imagem_antiga)) {
            unlink($imagem_antiga);
        }
    }

    // Atualizar na base de dados
    $update_sql = "UPDATE portfolio SET imagem = ?, descricao_imagem = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ssi", $nova_imagem, $descricao_imagem, $portfolio_id);

    if ($update_stmt->execute()) {
        $_SESSION['success_message'] = "Imagem atualizada com sucesso.";
        header("Location: empresa_portfolio.php?id=" . $portfolio['empresa_id'] . "&show_message=1");
        exit();
    } else {
        $_SESSION['error_message'] = "Erro ao atualizar a imagem: " . $conn->error;
    }

    $update_stmt->close();
}

include '../includes/header.php';
include '../admin/includes/header_admin.php';
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<link rel="stylesheet" href="/projeto/css/editar_portfolio.css">

<div class="white-background">
    <div class="container-fluid">
        <div class="header-container">
            <div class="logo-container">
                <img src="../imagens/Logotipo_freebox.png" style="height:75px;">
            </div>

            <div class="title-container">
                <h4><?= htmlspecialchars($portfolio['nome_empresa']); ?></h4>
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

<div class="container editar-portfolio-container">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="editar-portfolio-header">
                <h5><i class="fas fa-edit"></i> Editar Imagem do Portfólio</h5>
            </div>

            <div class="editar-portfolio-card">
                <form method="POST" action="editar_portfolio.php?id=<?php echo $portfolio_id; ?>" enctype="multipart/form-data">



                    <div class="form-group mt-4">
                        <label>Imagem Atual</label><br>
                        <img src="<?php echo htmlspecialchars($portfolio['imagem']); ?>"
                            alt="Imagem atual"
                            class="portfolio-current-img">
                    </div>

                    <div class="form-group mt-4">
                        <label for="portfolio_imagem">
                            Nova Imagem
                            <span class="input-help-text">(deixe vazio para manter a atual)</span>
                        </label>
                        <input type="file" class="form-control-file" id="portfolio_imagem" name="portfolio_imagem" accept="image/*">
                    </div>

                    <div class="form-group mt-4">
                        <label for="descricao_imagem">Descrição</label>
                        <textarea class="form-control" id="descricao_imagem" name="descricao_imagem" rows="3"><?php echo htmlspecialchars($portfolio['descricao_imagem']); ?></textarea>
                    </div>

                    <div class="editar-portfolio-buttons mt-4">
                        <a href="empresa_portfolio.php?id=<?php echo $portfolio['empresa_id']; ?>" class="btn btn-secondary">
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