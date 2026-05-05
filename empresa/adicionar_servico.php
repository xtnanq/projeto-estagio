<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $empresa_id = intval($_POST['empresa_id']);
    $nome_servico = trim($_POST['nome_servico'] ?? '');
    $titulo_servico = trim($_POST['titulo_servico'] ?? '');
    $descricao_servico = trim($_POST['descricao_servico'] ?? '');

    if ($nome_servico == '' || $titulo_servico == '') {
        header("Location: empresa_servicos.php?id=$empresa_id&error=1");
        exit;
    }

    $sql = "INSERT INTO servicos (empresa_id, nome_servico, titulo_servico, descricao_servico)
            VALUES (?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isss", $empresa_id, $nome_servico, $titulo_servico, $descricao_servico);

    if ($stmt->execute()) {
        header("Location: empresa_servicos.php?id=$empresa_id&success=1");
    } else {
        header("Location: empresa_servicos.php?id=$empresa_id&error=1");
    }

    exit;
}