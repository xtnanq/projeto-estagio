
<link rel="stylesheet" href="../css/footer_cliente.css?v=<?= time(); ?>">

<footer class="public-footer">
    <div class="footer-top">
        <div class="container-fluid px-5">
            <div class="footer-cols">

                <div class="footer-col">
                    <h6 class="footer-col-title">Administração</h6>
                    <ul>
                        <li>
                            <a href="../admin/dashboard.php">
                                <i class="fas fa-home me-2"></i>Painel Admin
                            </a>
                        </li>

                        <li>
                            <a href="../admin/editar_admin.php">
                                <i class="fas fa-user-shield me-2"></i>Editar Admin
                            </a>
                        </li>
                    </ul>
                </div>

                
               

                    <p class="mt-3"
                       style="font-size: 0.7rem; color: rgba(255,255,255,0.3);">

                        

                    </p>
                </div>

            </div>
        </div>
    </div>

    <div class="footer-brand-name">
        FREEBOX ADMIN
    </div>

    <div class="footer-bottom">
        <div class="container-fluid px-5 footer-bottom-inner">

            <span>
                © <?= date('Y'); ?>
                <strong>FreeBox</strong> — Painel de Administração
            </span>

            <span>
                Desenvolvido por
                <a href="https://webdesigner.is4.pt/"
                   target="_blank"
                   class="made-by">

                    IS4 Web Designer

                </a>
            </span>

        </div>
    </div>
</footer>

<a href="#top" class="back-to-top">
    <i class="fas fa-chevron-up"></i>
</a>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Smooth scroll para o topo
    document.querySelector('.back-to-top').addEventListener('click', function(e) {

        e.preventDefault();

        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });

    });
</script>
<link rel="stylesheet" href="css/footer_admin.css?v=<?= time(); ?>">
</body>
</html>

