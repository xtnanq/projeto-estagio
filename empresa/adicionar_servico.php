<?php
require_once '../config/database.php';

$response = array('success' => false);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $empresa_id = intval($_POST['empresa_id']);
    $nome_servico = trim($_POST['nome_servico'] ?? '');
    $titulo_servico = trim($_POST['titulo_servico'] ?? '');
    $descricao_servico = trim($_POST['descricao_servico'] ?? '');

    // Validação básica
    if (empty($nome_servico) || empty($titulo_servico)) {
        $response['error'] = 'Nome e título são obrigatórios.';
        echo json_encode($response);
        exit;
    }

    $insert_servico_sql = "INSERT INTO servicos (empresa_id, nome_servico, titulo_servico, descricao_servico) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_servico_sql);
    $stmt->bind_param("isss", $empresa_id, $nome_servico, $titulo_servico, $descricao_servico);

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['id'] = $stmt->insert_id;
        $response['nome_servico'] = htmlspecialchars($nome_servico);
    } else {
        $response['error'] = 'Erro ao inserir serviço.';
    }
    $stmt->close();
}

echo json_encode($response);
$conn->close();
?>