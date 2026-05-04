<?php
// Outras funções admin...

// Adicione esta função no final do arquivo
function gerarScriptEmpresasAdmin() {
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        let empresaIdParaEliminar;
        const modalConfirm = document.getElementById('modalConfirm');
        const nomeEmpresaEliminar = document.getElementById('nomeEmpresaEliminar');
        const confirmText = document.getElementById('confirmText');
        const btnConfirm = document.getElementById('btnConfirm');
        const btnCancel = document.getElementById('btnCancel');

        window.confirmarExclusao = function(id, nomeEmpresa) {
            empresaIdParaEliminar = id;
            modalConfirm.style.display = 'block';
            confirmText.value = '';
            nomeEmpresaEliminar.textContent = nomeEmpresa;
        }

        btnCancel.addEventListener('click', function() {
            modalConfirm.style.display = 'none';
        });

        btnConfirm.addEventListener('click', function() {
            if (confirmText.value.toUpperCase() === 'CONFIRMAR') {
                window.location.href = "eliminar_empresa.php?id=" + empresaIdParaEliminar;
            } else {
                alert('Por favor, digite CONFIRMAR para prosseguir com a eliminação.');
            }
        });

        // Fechar o modal se clicar fora dele
        window.onclick = function(event) {
            if (event.target == modalConfirm) {
                modalConfirm.style.display = "none";
            }
        }

        console.log('Script carregado e elementos encontrados:', {
            modalConfirm,
            nomeEmpresaEliminar,
            confirmText,
            btnConfirm,
            btnCancel
        });
    });
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('messageModal');
        const modalMessage = document.getElementById('modalMessage');
        const modalOkButton = document.getElementById('modalOkButton');

        <?php if (isset($_SESSION['success'])) : ?>
            modalMessage.textContent = "<?php echo $_SESSION['success']; ?>";
            modal.style.display = "block";
            <?php unset($_SESSION['success']); ?>
        <?php elseif (isset($_SESSION['error'])) : ?>
            modalMessage.textContent = "<?php echo $_SESSION['error']; ?>";
            modal.style.display = "block";
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        modalOkButton.onclick = function() {
            modal.style.display = "none";
            // Atualizar a lista de empresas (você pode recarregar a página ou usar AJAX para atualizar apenas a lista)
            location.reload();
        }

    // Fechar o modal se clicar fora dele
        window.onclick = function(event) {
            if (event.target == modal) {
              modal.style.display = "none";
              location.reload();
            }
        }
    });
    document.querySelector('form').addEventListener('submit', function(e) {
        var novaSenha = document.getElementById('nova_senha').value;
        if (novaSenha !== '' && novaSenha.length < 6) {
            e.preventDefault();
            alert('A nova senha deve ter pelo menos 6 caracteres.');
        }
    });
$(document).ready(function(){
    // ... (seu código existente) ...

    // Modificar este seletor para ser mais específico
    $('.config-section .btn-danger').on('click', function(e){
        e.preventDefault();
        var servicoId = $(this).data('id');
        var servicoNome = $(this).data('nome');
        $('#modalTitle').text('Eliminar ' + servicoNome);
        $('#confirmarEliminacao').data('id', servicoId);
        $('#eliminarServicoModal').show();
    });

    // Adicionar um manipulador de eventos específico para o botão de logout
    $('#logoutBtn').on('click', function(e){
        e.preventDefault();
        window.location.href = '../logout.php';
    });

    // ... (resto do seu código) ...
});
   </script>
    <?php
}
?>
