<?php
// Outras funções admin...

// Captura mensagens da sessão
$flashMessage = [
    "success" => $_SESSION['success'] ?? null,
    "error" => $_SESSION['error'] ?? null
];

// limpa sessão depois de passar para JS
unset($_SESSION['success'], $_SESSION['error']);
?>

<!-- PASSAR DADOS DO PHP PARA JS -->
<script>
window.flashMessage = <?php echo json_encode($flashMessage); ?>;
</script>

<!-- CARREGAR FICHEIRO JS EXTERNO -->
<script src="/projeto/js/includes_functions_admin.js" defer></script>