<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

echo "Conteúdo da sessão:<br>";
var_dump($_SESSION);
echo "<br><br>";

if (!isset($_SESSION['usuario_id'])) {
    echo "Sessão 'usuario_id' não está definida. Redirecionando para login...<br>";
    exit();
} else {
    echo "ID do usuário na sessão: " . $_SESSION['usuario_id'] . "<br><br>";
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "ID do serviço inválido ou não fornecido.<br>";
    exit();
} else {
    $servico_id = intval($_GET['id']);
    $usuario_id = $_SESSION['usuario_id'];
    echo "ID do serviço: " . $servico_id . "<br>";
    echo "ID do usuário: " . $usuario_id . "<br><br>";

    // Modifique a consulta SQL para permitir que o administrador exclua qualquer serviço
    if ($_SESSION['tipo_usuario'] == 'admin') {
        $sql = "SELECT s.*, e.id as empresa_id FROM servicos s
                JOIN empresas e ON s.empresa_id = e.id
                WHERE s.id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $servico_id);
    } else {
        $sql = "SELECT s.*, e.id as empresa_id FROM servicos s
                JOIN empresas e ON s.empresa_id = e.id
                WHERE s.id = ? AND e.usuario_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $servico_id, $usuario_id);
    }

    echo "Consulta SQL: " . $sql . "<br>";
    echo "Parâmetros: servico_id = $servico_id" . ($_SESSION['tipo_usuario'] != 'admin' ? ", usuario_id = $usuario_id" : "") . "<br><br>";

    $stmt->execute();
    $result = $stmt->get_result();
    $servico = $result->fetch_assoc();

    if ($servico) {
        echo "Informações do serviço:<br>";
        print_r($servico);
    } else {
        echo "Serviço não encontrado ou você não tem permissão para eliminá-lo.<br>";
        echo "Erro MySQL: " . $conn->error . "<br>";
    }
}

// Adicione estas consultas diretas ao banco de dados para verificação
echo "<br><br>Verificação direta no banco de dados:<br>";
$check_sql = "SELECT * FROM servicos WHERE id = $servico_id";
$check_result = $conn->query($check_sql);
if ($check_result->num_rows > 0) {
    echo "O serviço com ID $servico_id existe na tabela servicos.<br>";
    $servico_data = $check_result->fetch_assoc();
    echo "Empresa ID associada ao serviço: " . $servico_data['empresa_id'] . "<br>";
    
    $check_empresa_sql = "SELECT * FROM empresas WHERE id = " . $servico_data['empresa_id'] . " AND usuario_id = $usuario_id";
    $check_empresa_result = $conn->query($check_empresa_sql);
    if ($check_empresa_result->num_rows > 0) {
        echo "A empresa associada ao serviço pertence ao usuário correto.<br>";
    } else {
        echo "A empresa associada ao serviço NÃO pertence ao usuário correto.<br>";
        echo "Consulta empresa: " . $check_empresa_sql . "<br>";
    }
} else {
    echo "O serviço com ID $servico_id NÃO existe na tabela servicos.<br>";
}

// Verificação adicional para o usuário
$check_user_sql = "SELECT * FROM usuarios WHERE id = $usuario_id";
$check_user_result = $conn->query($check_user_sql);
if ($check_user_result->num_rows > 0) {
    echo "O usuário com ID $usuario_id existe na tabela usuarios.<br>";
}
    else {
    echo "O usuário com ID $usuario_id NÃO existe na tabela usuarios.<br>";
}

// Verificação final e tentativa de exclusão
if ($servico || $_SESSION['tipo_usuario'] == 'admin') {
    $delete_sql = "DELETE FROM servicos WHERE id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("i", $servico_id);
    
    if ($delete_stmt->execute()) {
        echo "Serviço excluído com sucesso.<br>";
    } else {
        echo "Erro ao excluir o serviço: " . $conn->error . "<br>";
    }
    
    $delete_stmt->close();
} else {
    echo "Não foi possível excluir o serviço devido a problemas de permissão ou porque ele não existe.<br>";
}

// Fechar conexão com o banco de dados
$conn->close();
?>
