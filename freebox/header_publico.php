<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($nome_empresa); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/projeto/css/site_publico.css">

    <script type="text/javascript">
    function googleTranslateElementInit() {
        new google.translate.TranslateElement({
            pageLanguage: 'pt',
            includedLanguages: 'en,fr,es,pt',
            layout: google.translate.TranslateElement.InlineLayout.SIMPLE,
            autoDisplay: false
        }, 'google_translate_element');
    }
    </script>
    <script src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

    <style>
        .carousel-outer { position: relative; padding: 0 48px; }
        .carousel-wrapper { overflow: hidden; }
        .carousel-track {
            display: flex;
            transition: transform 0.45s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .carousel-page {
            min-width: 100%;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            box-sizing: border-box;
        }
        .carousel-btn {
            position: absolute; top: 50%; transform: translateY(-50%);
            width: 40px; height: 40px; border-radius: 50%; border: none;
            background: white; box-shadow: 0 2px 10px rgba(0,0,0,0.15);
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; z-index: 10; font-size: 0.95rem;
            transition: opacity 0.2s, box-shadow 0.2s;
        }
        .carousel-btn:hover { box-shadow: 0 4px 18px rgba(0,0,0,0.22); }
        .carousel-btn.prev { left: 0; }
        .carousel-btn.next { right: 0; }
        .carousel-dots {
            display: flex; justify-content: center; gap: 8px; margin-top: 28px;
        }
        .carousel-dots .dot {
            width: 8px; height: 8px; border-radius: 50%;
            background: #ccc; border: none; padding: 0; cursor: pointer;
            transition: background 0.2s, transform 0.2s;
        }
        .carousel-dots .dot.active { background: currentColor; transform: scale(1.4); }
        .portfolio-card {
            border-radius: 10px; overflow: hidden;
            position: relative; cursor: pointer; aspect-ratio: 4/3;
        }
        .portfolio-card img {
            width: 100%; height: 100%; object-fit: cover;
            display: block; transition: transform 0.4s ease;
        }
        .portfolio-card:hover img { transform: scale(1.06); }
        .portfolio-card .overlay {
            position: absolute; inset: 0; background: rgba(0,0,0,0.35);
            display: flex; align-items: center; justify-content: center;
            opacity: 0; transition: opacity 0.3s;
        }
        .portfolio-card:hover .overlay { opacity: 1; }
        .portfolio-card .overlay i { color: white; font-size: 1.6rem; }

        /* NAVBAR */
        .public-navbar {
            position: sticky;
            top: 0;
            z-index: 1000;
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            padding: 0 20px;
            height: 65px;
            display: flex;
            align-items: center;
        }
        .navbar-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
        }
        .brand { display: flex; align-items: center; text-decoration: none; }
        .brand-logo { height: 45px; object-fit: contain; }
        .brand-name { font-size: 1.2rem; font-weight: 700; color: #0066cc; }
        .nav-links { display: flex; gap: 30px; }
        .nav-links a {
            text-decoration: none; color: #444;
            font-weight: 500; font-size: 0.95rem; transition: color 0.2s;
        }
        .nav-links a:hover { color: #0066cc; }

        /* GOOGLE TRANSLATE */
        #google_translate_element {
            display: flex;
            align-items: center;
        }
        #google_translate_element select {
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 4px 8px;
            font-size: 0.85rem;
            color: #444;
            cursor: pointer;
            outline: none;
        }
        .goog-te-gadget-simple {
            border: none !important;
            background: none !important;
        }
        /* Esconder barra do Google Translate no topo */
        .goog-te-banner-frame { display: none !important; }
        body { top: 0 !important; }

        @media (max-width: 768px) {
            .nav-links { display: none; }
            .carousel-page { grid-template-columns: repeat(2, 1fr); }
            .carousel-outer { padding: 0 36px; }
        }
        @media (max-width: 480px) {
            .carousel-page { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<header class="public-navbar">
    <div class="container navbar-inner">

        <!-- LOGO / NOME -->
        <a href="#inicio" class="brand">
            <?php if (!empty($logo)): ?>
                <img src="<?= htmlspecialchars($logo); ?>" alt="<?= htmlspecialchars($nome_empresa); ?>" class="brand-logo">
            <?php else: ?>
                <span class="brand-name"><?= htmlspecialchars($nome_empresa); ?></span>
            <?php endif; ?>
        </a>

        <!-- LINKS -->
        <nav class="nav-links">
            <a href="#sobre">Sobre Nós</a>
            <?php if (!empty($servicos)): ?>
                <a href="#servicos">Serviços</a>
            <?php endif; ?>
            <?php if (!empty($portfolio)): ?>
                <a href="#portfolio">Portfólio</a>
            <?php endif; ?>
            <a href="/projeto/freebox/contato.php?url=<?= htmlspecialchars($website['url_site'] ?? ''); ?>">Contacto</a>
        </nav>

        <!-- GOOGLE TRANSLATE -->
        <div id="google_translate_element"></div>

    </div>
</header>