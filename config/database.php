<?php
// config/database.php - Configuração da conexão com o banco de dados

define('DB_HOST', 'localhost');
define('DB_USER', 'root');         // Usuário padrão do WampServer
define('DB_PASS', '');             // Senha em branco por padrão no WampServer
define('DB_NAME', 'siteInstitucional');

// Criando a conexão
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Verificando a conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Definindo o charset para utf8
$conn->set_charset("utf8");
?>