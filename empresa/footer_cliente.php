<link rel="stylesheet" href="../css/footer_cliente.css?v=<?= time(); ?>">

<footer class="public-footer">
    <div class="footer-top">
        <div class="container-fluid px-5">
            <div class="footer-cols">

                <div class="footer-col">
                    <h6 class="footer-col-title">Gestão</h6>
                    <ul>
                        <li><a href="dashboard.php"><i class="fas fa-home me-2"></i>Início</a></li>
                        <?php if (!empty($url_site)): ?>
                            <li>
                                <a href="/projeto/freebox/<?= htmlspecialchars($url_site); ?>">
                                    <i class="fas fa-globe me-2"></i>Meu Website
                                </a>
                            </li>
                        <?php endif; ?>
                        // ...existing code...
                        <li><a href="editar_conta.php"><i class="fas fa-user-gear me-2"></i>Minha Conta</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h6 class="footer-col-title">Ajuda & Suporte</h6>
                    <ul>
                        <li><a href="#"><i class="fas fa-question-circle me-2"></i>Centro de Ajuda</a></li>
                        <li><a href="#"><i class="fas fa-book me-2"></i>Documentação</a></li>
                        <li><a href="mailto:suporte@freebox.pt"><i class="fas fa-envelope me-2"></i>Email Suporte</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h6 class="footer-col-title">Problemas técnicos?</h6>
                    <a href="mailto:suporte@freebox.pt" class="footer-btn-enviar" style="display:inline-block; text-decoration:none;">
                        Contate-nos
                    </a>
                    <p class="mt-3" style="font-size: 0.7rem; color: rgba(255,255,255,0.3);">
                        Resposta média: 48 horas.
                    </p>
                </div>

            </div>
        </div>
    </div>

    <div class="footer-brand-name">
        <?= htmlspecialchars($empresa['nome_empresa'] ?? 'FREEBOX'); ?>
    </div>

    <div class="footer-bottom">
        <div class="container-fluid px-5 footer-bottom-inner">
            <span>© <?= date('Y'); ?> <strong>FreeBox</strong> — Painel de Gestão</span>
            <span>Desenvolvido por <a href="https://webdesigner.is4.pt/" target="_blank" class="made-by">IS4 Web Designer</a></span>
        </div>
    </div>
</footer>

<a href="#top" class="back-to-top"><i class="fas fa-chevron-up"></i></a>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.querySelector('.back-to-top').addEventListener('click', function(e) {
        e.preventDefault();
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
</script>

</body>

</html>