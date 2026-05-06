
function filtrarEmpresas() {
    const input = document.getElementById('searchInput').value.toLowerCase();
    const linhas = document.querySelectorAll('#tabelaEmpresas tbody tr');
    let visiveis = 0;

    linhas.forEach(function(linha) {
        const nome = linha.cells[0].textContent.toLowerCase();
        const email = linha.cells[1].textContent.toLowerCase();
        if (nome.includes(input) || email.includes(input)) {
            linha.style.display = '';
            visiveis++;
        } else {
            linha.style.display = 'none';
        }
    });

    document.getElementById('semResultados').style.display = visiveis === 0 ? 'block' : 'none';
}