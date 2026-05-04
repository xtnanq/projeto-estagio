<?php
require_once '../config/database.php';

$response = array('success' => false);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $empresa_id = $_POST['empresa_id'];
    $nome_servico = $_POST['nome_servico'];
    $titulo_servico = $_POST['titulo_servico'];
    $descricao_servico = $_POST['descricao_servico'];

    $insert_servico_sql = "INSERT INTO servicos (empresa_id, nome_servico, titulo_servico, descricao_servico) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_servico_sql);
    $stmt->bind_param("isss", $empresa_id, $nome_servico, $titulo_servico, $descricao_servico);

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['id'] = $stmt->insert_id;
        $response['nome_servico'] = htmlspecialchars($nome_servico);
    }
    $stmt->close();
}

echo json_encode($response);
$conn->close();
