document.addEventListener('DOMContentLoaded', function () {

    // =========================
    // DELETE EMPRESA MODAL
    // =========================
    let empresaIdParaEliminar;

    const modalConfirm = document.getElementById('modalConfirm');
    const nomeEmpresaEliminar = document.getElementById('nomeEmpresaEliminar');
    const confirmText = document.getElementById('confirmText');
    const btnConfirm = document.getElementById('btnConfirm');
    const btnCancel = document.getElementById('btnCancel');

    window.confirmarExclusao = function (id, nomeEmpresa) {
        empresaIdParaEliminar = id;
        modalConfirm.style.display = 'block';
        confirmText.value = '';
        nomeEmpresaEliminar.textContent = nomeEmpresa;
    };

    if (btnCancel) {
        btnCancel.addEventListener('click', function () {
            modalConfirm.style.display = 'none';
        });
    }

    if (btnConfirm) {
        btnConfirm.addEventListener('click', function () {
            if (confirmText.value.toUpperCase() === 'CONFIRMAR') {
                window.location.href = "eliminar_empresa.php?id=" + empresaIdParaEliminar;
            } else {
                alert('Por favor, digite CONFIRMAR para prosseguir com a eliminação.');
            }
        });
    }

    window.onclick = function (event) {
        if (event.target === modalConfirm) {
            modalConfirm.style.display = "none";
        }
    };

    // =========================
    // FLASH MESSAGE MODAL
    // =========================
    const modal = document.getElementById('messageModal');
    const modalMessage = document.getElementById('modalMessage');
    const modalOkButton = document.getElementById('modalOkButton');

    if (window.flashMessage) {

        if (window.flashMessage.success) {
            modalMessage.textContent = window.flashMessage.success;
            modal.style.display = "block";
        }

        if (window.flashMessage.error) {
            modalMessage.textContent = window.flashMessage.error;
            modal.style.display = "block";
        }
    }

    if (modalOkButton) {
        modalOkButton.addEventListener('click', function () {
            modal.style.display = "none";
            location.reload();
        });
    }

    window.onclick = function (event) {
        if (event.target === modal) {
            modal.style.display = "none";
            location.reload();
        }
    };
});