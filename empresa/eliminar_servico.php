<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Verificar se o usuário está autenticado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
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

$stmt->execute();
$result = $stmt->get_result();
$servico = $result->fetch_assoc();

if (!$servico) {
    $_SESSION['error_message'] = "Serviço não encontrado ou você não tem permissão para eliminá-lo.";
    header("Location: configurar_empresa.php");
    exit();
}

// Verificação final e tentativa de exclusão
if ($servico || $_SESSION['tipo_usuario'] == 'admin') {
    $delete_sql = "DELETE FROM servicos WHERE id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("i", $servico_id);
    $delete_stmt->close();
} else {
     $_SESSION['error_message'] = "Erro ao eliminar o serviço: " . $conn->error;
}
// Eliminar o serviço
$delete_sql = "DELETE FROM servicos WHERE id = ?";
$delete_stmt = $conn->prepare($delete_sql);
$delete_stmt->bind_param("i", $servico_id);

if ($delete_stmt->execute()) {
    $_SESSION['success_message'] = "Serviço eliminado com sucesso.";
} else {
    $_SESSION['error_message'] = "Erro ao eliminar o serviço: " . $conn->error;
}

$conn->close();

// Em vez de redirecionar imediatamente, vamos passar uma flag na URL
header("Location: configurar_empresa.php?id=" . $servico['empresa_id'] . "&show_message=1#servicos");

exit();
?>


