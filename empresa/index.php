<?php
session_start();

require_once '../config/database.php';
require_once '../includes/functions.php';

if (!estaLogado()) {
    header("Location: ../login.php");
    exit;
}

if (eCliente()) {
    header("Location: dashboard.php");
    exit;
}

if (eAdmin()) {
    header("Location: ../admin/dashboard.php");
    exit;
}

header("Location: ../login.php");
exit;
?>