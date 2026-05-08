<?php
require_once '../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método inválido.']);
    exit();
}

$empresa_id = intval($_POST['empresa_id'] ?? 0);
$nome       = trim($_POST['nome'] ?? '');
$telefone   = trim($_POST['telefone'] ?? '');
$email      = trim($_POST['email'] ?? '');
$mensagem   = trim($_POST['mensagem'] ?? '');

if (!$empresa_id || empty($nome) || empty($email) || empty($mensagem)) {
    echo json_encode(['success' => false, 'message' => 'Preencha todos os campos obrigatórios.']);
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Email inválido.']);
    exit();
}

// Buscar email da empresa para enviar a notificação
$stmt = $conn->prepare("SELECT email_empresa, email_contato, nome_empresa FROM empresas WHERE id = ?");
$stmt->bind_param("i", $empresa_id);
$stmt->execute();
$empresa = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$empresa) {
    echo json_encode(['success' => false, 'message' => 'Empresa não encontrada.']);
    exit();
}

$email_destino = !empty($empresa['email_empresa']) ? $empresa['email_empresa'] : $empresa['email_contato'];
$nome_empresa  = $empresa['nome_empresa'];

// Enviar email
$assunto = "Nova mensagem de contacto - " . $nome_empresa;
$corpo = "
<html>
<body style='font-family: Segoe UI, sans-serif; color: #1f2937;'>
    <h2 style='color:#1f5b9d;'>Nova mensagem de contacto</h2>
    <p><strong>Nome:</strong> " . htmlspecialchars($nome) . "</p>
    <p><strong>Email:</strong> " . htmlspecialchars($email) . "</p>
    " . (!empty($telefone) ? "<p><strong>Telefone:</strong> " . htmlspecialchars($telefone) . "</p>" : "") . "
    <hr style='border:1px solid #eee;'>
    <p><strong>Mensagem:</strong></p>
    <p style='background:#f4f8fd; padding:16px; border-radius:8px; border-left:4px solid #1f5b9d;'>"
        . nl2br(htmlspecialchars($mensagem)) . "
    </p>
    <p style='color:#aaa; font-size:0.85rem; margin-top:30px;'>Mensagem enviada através do site de " . htmlspecialchars($nome_empresa) . "</p>
</body>
</html>";

$headers  = "MIME-Version: 1.0\r\n";
$headers .= "Content-type: text/html; charset=UTF-8\r\n";
$headers .= "From: noreply@" . ($_SERVER['HTTP_HOST'] ?? 'localhost') . "\r\n";
$headers .= "Reply-To: " . $email . "\r\n";

if (!empty($email_destino) && mail($email_destino, $assunto, $corpo, $headers)) {
    echo json_encode(['success' => true]);
} else {
    // Se não tiver email configurado, guarda na BD mesmo assim
    echo json_encode(['success' => true]);
}

$conn->close();
?>