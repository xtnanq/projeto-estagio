<footer class="public-footer">
    <div class="footer-top">
        <div class="container">
            <div class="footer-cols">

                <!-- PÁGINAS -->
                <div class="footer-col">
                    <h6 class="footer-col-title">Páginas</h6>
                    <ul>
                        <li><a href="/projeto/freebox/?url=<?= htmlspecialchars($website['url_site'] ?? ''); ?>#sobre">Sobre Nós</a></li>
                        <?php if (!empty($servicos)): ?>
                            <li><a href="/projeto/freebox/?url=<?= htmlspecialchars($website['url_site'] ?? ''); ?>#servicos">Serviços</a></li>
                        <?php endif; ?>
                        <?php if (!empty($portfolio)): ?>
                            <li><a href="/projeto/freebox/?url=<?= htmlspecialchars($website['url_site'] ?? ''); ?>#portfolio">Portfólio</a></li>
                        <?php endif; ?>
                        <li><a href="/projeto/freebox/contato.php?url=<?= htmlspecialchars($website['url_site'] ?? ''); ?>">Contacto</a></li>
                        <li><a href="https://www.livroreclamacoes.pt/Inicio/" target="_blank">Reclamações</a></li>
                    </ul>
                </div>

                <!-- CONTACTO -->
                <div class="footer-col">
                    <h6 class="footer-col-title">Contacto</h6>
                    <ul>
                        <?php if (!empty($email_principal)): ?>
                            <li><a href="mailto:<?= htmlspecialchars($email_principal); ?>"><?= htmlspecialchars($email_principal); ?></a></li>
                        <?php endif; ?>
                        <?php if (!empty($telefone_principal)): ?>
                            <li><a href="tel:<?= htmlspecialchars($telefone_principal); ?>"><?= htmlspecialchars($telefone_principal); ?></a></li>
                        <?php endif; ?>
                        <?php if (!empty($morada_completa)): ?>
                            <li><span><?= htmlspecialchars($morada_completa); ?></span></li>
                        <?php endif; ?>
                    </ul>
                </div>

                <!-- FALE CONNOSCO -->
                <div class="footer-col">
                    <h6 class="footer-col-title">Fale Connosco</h6>
                    <a href="/projeto/freebox/formulario.php?url=<?= htmlspecialchars($website['url_site'] ?? ''); ?>" class="footer-btn-enviar" style="display:inline-block; text-decoration:none;">
                        Enviar Mensagem <i class="fas fa-paper-plane"></i>
                    </a>
                </div>

                <!-- REDES SOCIAIS -->
                <?php
                $tem_redes = !empty($website['link_facebook']) || !empty($website['link_instagram']) || !empty($website['link_x']);
                if ($tem_redes): ?>
                    <div class="footer-col">
                        <h6 class="footer-col-title">Sociais</h6>
                        <ul>
                            <?php if (!empty($website['link_facebook'])): ?>
                                <li><a href="<?= htmlspecialchars($website['link_facebook']); ?>" target="_blank">Facebook</a></li>
                            <?php endif; ?>
                            <?php if (!empty($website['link_instagram'])): ?>
                                <li><a href="<?= htmlspecialchars($website['link_instagram']); ?>" target="_blank">Instagram</a></li>
                            <?php endif; ?>
                            <?php if (!empty($website['link_x'])): ?>
                                <li><a href="<?= htmlspecialchars($website['link_x']); ?>" target="_blank">X / Twitter</a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
    <?php
    // filepath: [arquivo_principal].php
    // ...existing code...
    $nome_empresa = $empresa['nome'] ?? 'Nome Padrão';  // Defina aqui
    include 'footer_publico.php';
    // ...existing code...
    ?>
    <!-- NOME GRANDE -->
    <div class="footer-brand-name">
        <?= htmlspecialchars($nome_empresa); ?>
    </div>

    <div class="footer-bottom">
        <div class="container footer-bottom-inner">
            <h5><span>© <?= date('Y'); ?> <?= htmlspecialchars($nome_empresa); ?> — Todos os direitos reservados</span></h5>
            <h5><span>Made by <a href="https://webdesigner.is4.pt/" target="_blank" class="made-by">IS4 Web Designer</a></span></h5>
        </div>
    </div>
</footer>

<?php if (!empty($portfolio)): ?>
    <div id="lightbox" class="lightbox">
        <button class="lightbox-close" id="lightboxClose"><i class="fas fa-xmark"></i></button>
        <button class="lightbox-prev" id="lightboxPrev"><i class="fas fa-arrow-left"></i></button>
        <div class="lightbox-image-wrap">
            <img id="lightboxImage" src="" alt="Imagem do portfólio">
            <div class="lightbox-counter" id="lightboxCounter"></div>
        </div>
        <button class="lightbox-next" id="lightboxNext"><i class="fas fa-arrow-right"></i></button>
    </div>
<?php endif; ?>

<a href="#inicio" class="back-to-top"><i class="fas fa-chevron-up"></i></a>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.querySelectorAll('a[href^="#"]').forEach(function(anchor) {
        anchor.addEventListener('click', function(e) {
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });
</script>

<?php if (!empty($portfolio)): ?>
    <script>
        const portfolioImages = <?php echo json_encode(array_column($portfolio, 'imagem')); ?>;
        let currentImageIndex = 0;
        const lightbox = document.getElementById('lightbox');
        const lightboxImage = document.getElementById('lightboxImage');
        const lightboxCounter = document.getElementById('lightboxCounter');
        const lightboxClose = document.getElementById('lightboxClose');
        const lightboxPrev = document.getElementById('lightboxPrev');
        const lightboxNext = document.getElementById('lightboxNext');

        function openLightbox(index) {
            currentImageIndex = index;
            updateLightbox();
            lightbox.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeLightbox() {
            lightbox.classList.remove('active');
            document.body.style.overflow = '';
        }

        function updateLightbox() {
            lightboxImage.src = portfolioImages[currentImageIndex];
            lightboxCounter.textContent = (currentImageIndex + 1) + ' / ' + portfolioImages.length;
        }

        function nextImage() {
            currentImageIndex = (currentImageIndex + 1) % portfolioImages.length;
            updateLightbox();
        }

        function prevImage() {
            currentImageIndex = (currentImageIndex - 1 + portfolioImages.length) % portfolioImages.length;
            updateLightbox();
        }

        document.querySelectorAll('.portfolio-card').forEach(function(item) {
            item.addEventListener('click', function() {
                openLightbox(parseInt(this.dataset.index));
            });
        });
        lightboxClose.addEventListener('click', closeLightbox);
        lightboxNext.addEventListener('click', nextImage);
        lightboxPrev.addEventListener('click', prevImage);
        lightbox.addEventListener('click', function(e) {
            if (e.target === lightbox) closeLightbox();
        });
        document.addEventListener('keydown', function(e) {
            if (!lightbox.classList.contains('active')) return;
            if (e.key === 'Escape') closeLightbox();
            if (e.key === 'ArrowRight') nextImage();
            if (e.key === 'ArrowLeft') prevImage();
        });
    </script>
<?php endif; ?>

<script>
    function initCarousel(trackId, prevId, nextId, dotsId) {
        const track = document.getElementById(trackId);
        if (!track) return;
        const pages = track.querySelectorAll('.carousel-page').length;
        if (pages <= 1) return;
        let current = 0;

        function goTo(page) {
            current = (page + pages) % pages;
            track.style.transform = 'translateX(-' + (current * 100) + '%)';
            if (dotsId) {
                document.querySelectorAll('#' + dotsId + ' .dot').forEach(function(d, i) {
                    d.classList.toggle('active', i === current);
                });
            }
        }
        const prev = document.getElementById(prevId);
        const next = document.getElementById(nextId);
        if (prev) prev.addEventListener('click', function() {
            goTo(current - 1);
        });
        if (next) next.addEventListener('click', function() {
            goTo(current + 1);
        });
        if (dotsId) {
            document.querySelectorAll('#' + dotsId + ' .dot').forEach(function(dot) {
                dot.addEventListener('click', function() {
                    goTo(parseInt(this.dataset.page));
                });
            });
        }
    }
    initCarousel('svcTrack', 'svcPrev', 'svcNext', 'svcDots');
    initCarousel('prtTrack', 'prtPrev', 'prtNext', 'prtDots');
</script>

</body>

</html>