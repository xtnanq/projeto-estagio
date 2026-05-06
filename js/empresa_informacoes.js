document.addEventListener("DOMContentLoaded", function () {

    const phpData   = document.getElementById("php-data");
    const showMsg   = phpData.dataset.showMessage;
    const success   = phpData.dataset.success;
    const error     = phpData.dataset.error;
    const empresaId = phpData.dataset.empresaId;

    if (showMsg === "1") {

        const modal  = document.getElementById("messageModal");
        const title  = document.getElementById("modalTitle");
        const message = document.getElementById("modalMessage");
        const okBtn  = document.getElementById("okButton");

        if (success) {
            title.textContent   = "Sucesso";
            message.textContent = success;
        } else if (error) {
            title.textContent   = "Erro";
            message.textContent = error;
        }

        modal.style.display = "block";

        okBtn.onclick = function () {
            modal.style.display = "none";
            window.history.replaceState(
                {},
                document.title,
                window.location.pathname + "?id=" + empresaId
            );
        };
    }

});