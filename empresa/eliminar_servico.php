<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Verificar autenticação
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login.php");
    exit();
}

// Validar ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error_message'] = "ID do serviço inválido.";
    header("Location: ../admin/dashboard.php");
    exit();
}

$servico_id = intval($_GET['id']);
$usuario_id = $_SESSION['usuario_id'];

// Buscar serviço e verificar permissão
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
$stmt->close();

if (!$servico) {
    $_SESSION['error_message'] = "Serviço não encontrado ou sem permissão.";
    header("Location: ../admin/dashboard.php");
    exit();
}

// CORRIGIDO: apenas um DELETE com execute() correto
$delete_stmt = $conn->prepare("DELETE FROM servicos WHERE id = ?");
$delete_stmt->bind_param("i", $servico_id);

if ($delete_stmt->execute()) {
    $_SESSION['success_message'] = "Serviço eliminado com sucesso.";
} else {
    $_SESSION['error_message'] = "Erro ao eliminar o serviço: " . $conn->error;
}

$delete_stmt->close();
$conn->close();
header("Location: empresa_servicos.php?id=" . $servico['empresa_id'] . "&show_message=1");
?>