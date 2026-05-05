<?php
session_start();
require_once '../config/database.php';

$usuario_id = $_SESSION['usuario_id'];

// buscar empresa do utilizador
$sql = "SELECT id FROM empresas WHERE usuario_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$empresa = $result->fetch_assoc();

if ($empresa) {
    header("Location: empresa_informacoes.php?id=" . $empresa['id']);
    exit;
} else {
    echo "Empresa não encontrada.";
}