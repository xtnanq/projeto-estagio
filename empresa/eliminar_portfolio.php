<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Verificar se o usuário está autenticado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// Verificar se o ID da imagem foi passado via GET
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error_message'] = "ID da imagem inválido.";
    header("Location: dashboard.php");
    exit();
}

$imagem_id = intval($_GET['id']);
$usuario_id = $_SESSION['usuario_id'];

// Buscar informações da imagem e verificar se pertence ao usuário
if ($_SESSION['tipo_usuario'] == 'admin') {
    $sql = "SELECT p.*, e.id as empresa_id FROM portfolio p
            JOIN empresas e ON p.empresa_id = e.id
            WHERE p.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $imagem_id);
} else {
    $sql = "SELECT p.*, e.id as empresa_id FROM portfolio p
            JOIN empresas e ON p.empresa_id = e.id
            WHERE p.id = ? AND e.usuario_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $imagem_id, $usuario_id);
}

$stmt->execute();
$result = $stmt->get_result();
$imagem = $result->fetch_assoc();

if (!$imagem) {
    $_SESSION['error_message'] = "Imagem não encontrada ou você não tem permissão para eliminá-la.";
    header("Location: configurar_empresa.php");
    exit();
}

// Eliminar o arquivo do servidor
if (file_exists($imagem['imagem'])) {
    unlink($imagem['imagem']);
}

// Eliminar a imagem do banco de dados
$delete_sql = "DELETE FROM portfolio WHERE id = ?";
$delete_stmt = $conn->prepare($delete_sql);
$delete_stmt->bind_param("i", $imagem_id);

if ($delete_stmt->execute()) {
    $_SESSION['success_message'] = "Imagem eliminada com sucesso.";
} else {
    $_SESSION['error_message'] = "Erro ao eliminar a imagem: " . $conn->error;
}

$conn->close();

// Redirecionar de volta para a página de configuração da empresa
header("Location: configurar_empresa.php?id=" . $imagem['empresa_id'] . "&show_message=1#portfolio");

exit();
?>
