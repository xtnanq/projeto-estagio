<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $empresa_id = intval($_POST['empresa_id']);
    $nome_servico = trim($_POST['nome_servico'] ?? '');
    $titulo_servico = trim($_POST['titulo_servico'] ?? '');
    $descricao_servico = trim($_POST['descricao_servico'] ?? '');

    if ($nome_servico == '' || $titulo_servico == '') {
        $_SESSION['error_message'] = "Preencha todos os campos obrigatórios.";
        header("Location: empresa_servicos.php?id=$empresa_id&show_message=1");
        exit;
    }

    $sql = "INSERT INTO servicos (empresa_id, nome_servico, titulo_servico, descricao_servico)
            VALUES (?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isss", $empresa_id, $nome_servico, $titulo_servico, $descricao_servico);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Serviço adicionado com sucesso!";
        header("Location: empresa_servicos.php?id=$empresa_id&show_message=1");
    } else {
        $_SESSION['error_message'] = "Erro ao adicionar serviço.";
        header("Location: empresa_servicos.php?id=$empresa_id&show_message=1");
    }

    exit;
}