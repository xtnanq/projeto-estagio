<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($nome_empresa); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/projeto/css/site_publico.css">



    <style>
        .carousel-outer { position: relative; padding: 0 48px; }
        .carousel-wrapper { overflow: hidden; }
        .carousel-track { display: flex; transition: transform 0.45s cubic-bezier(0.4, 0, 0.2, 1); }
        .carousel-page {
            min-width: 100%; display: grid;
            grid-template-columns: repeat(3, 1fr); gap: 24px; box-sizing: border-box;
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
        .carousel-dots { display: flex; justify-content: center; gap: 8px; margin-top: 28px; }
        .carousel-dots .dot {
            width: 8px; height: 8px; border-radius: 50%;
            background: #ccc; border: none; padding: 0; cursor: pointer;
            transition: background 0.2s, transform 0.2s;
        }
        .carousel-dots .dot.active { background: currentColor; transform: scale(1.4); }
        .portfolio-card { border-radius: 10px; overflow: hidden; position: relative; cursor: pointer; aspect-ratio: 4/3; }
        .portfolio-card img { width: 100%; height: 100%; object-fit: cover; display: block; transition: transform 0.4s ease; }
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
            position: sticky; top: 0; z-index: 1000;
            background: white; box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            padding: 0 20px; height: 65px; display: flex; align-items: center;
        }
        .navbar-inner { display: flex; align-items: center; justify-content: space-between; width: 100%; }
        .brand { display: flex; align-items: center; text-decoration: none; }
        .brand-logo { height: 45px; object-fit: contain; }
        .brand-name { font-size: 1.2rem; font-weight: 700; color: #0066cc; }
        .nav-links { display: flex; gap: 30px; }
        .nav-links a { text-decoration: none; color: #444; font-weight: 500; font-size: 0.95rem; transition: color 0.2s; }
        .nav-links a:hover { color: #0066cc; }



        /* SELETOR DE LÍNGUA PERSONALIZADO */
        .lang-selector { position: relative; }
        .lang-btn {
            display: flex; align-items: center; gap: 6px;
            background: none; border: 1px solid #ddd; border-radius: 8px;
            padding: 6px 12px; font-size: 0.85rem; font-weight: 500;
            color: #444; cursor: pointer; transition: border-color 0.2s, color 0.2s;
            white-space: nowrap;
        }
        .lang-btn:hover { border-color: #0066cc; color: #0066cc; }
        .lang-btn i { font-size: 0.7rem; }
        .lang-dropdown {
            display: none; position: absolute; right: 0; top: calc(100% + 8px);
            background: white; border: 1px solid #eee; border-radius: 10px;
            box-shadow: 0 6px 24px rgba(0,0,0,0.1); min-width: 150px;
            overflow: hidden; z-index: 9999;
        }
        .lang-dropdown.open { display: block; }
        .lang-option {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 16px; font-size: 0.88rem; color: #444;
            cursor: pointer; transition: background 0.15s;
            border: none; background: none; width: 100%; text-align: left;
        }
        .lang-option:hover { background: #f0f6ff; color: #0066cc; }
        .lang-option .flag { font-size: 1.1rem; }

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
        <a href="/projeto/freebox/?url=<?= htmlspecialchars($website['url_site'] ?? ''); ?>" class="brand">
            <?php if (!empty($logo)): ?>
                <img src="<?= htmlspecialchars($logo); ?>" alt="<?= htmlspecialchars($nome_empresa); ?>" class="brand-logo">
            <?php else: ?>
                <span class="brand-name"><?= htmlspecialchars($nome_empresa); ?></span>
            <?php endif; ?>
        </a>

        <!-- LINKS -->
        <nav class="nav-links">
            <a href="/projeto/freebox/?url=<?= htmlspecialchars($website['url_site'] ?? ''); ?>#sobre">Sobre Nós</a>
            <?php if (!empty($servicos)): ?>
                <a href="/projeto/freebox/?url=<?= htmlspecialchars($website['url_site'] ?? ''); ?>#servicos">Serviços</a>
            <?php endif; ?>
            <?php if (!empty($portfolio)): ?>
                <a href="/projeto/freebox/?url=<?= htmlspecialchars($website['url_site'] ?? ''); ?>#portfolio">Portfólio</a>
            <?php endif; ?>
            <a href="/projeto/freebox/contato.php?url=<?= htmlspecialchars($website['url_site'] ?? ''); ?>">Contacto</a>
        </nav>

        <!-- SELETOR DE LÍNGUA -->
        <div class="lang-selector" id="langSelector">
            <button class="lang-btn" id="langBtn">
                <span id="langCurrent">🇵🇹 PT</span>
                <i class="fas fa-chevron-down"></i>
            </button>
            <div class="lang-dropdown" id="langDropdown">
                <button class="lang-option" onclick="changeLang('pt', '🇵🇹', 'PT')">
                    <span class="flag">🇵🇹</span> Português
                </button>
                <button class="lang-option" onclick="changeLang('en', '🇬🇧', 'EN')">
                    <span class="flag">🇬🇧</span> English
                </button>
                <button class="lang-option" onclick="changeLang('es', '🇪🇸', 'ES')">
                    <span class="flag">🇪🇸</span> Español
                </button>
                <button class="lang-option" onclick="changeLang('fr', '🇫🇷', 'FR')">
                    <span class="flag">🇫🇷</span> Français
                </button>
            </div>
        </div>

    </div>
</header>

<script>
// ── Tradutor MyMemory ──────────────────────────────────────────
let originalTexts = [];   // guarda textos originais para reverter
let currentLang  = 'pt';

// Recolher todos os nós de texto relevantes (visíveis, não vazios)
function getTextNodes() {
    const skip = ['SCRIPT','STYLE','NOSCRIPT','IFRAME','INPUT','TEXTAREA','SELECT','BUTTON'];
    const skipIds = ['langSelector'];
    const walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT, {
        acceptNode: function(node) {
            if (!node.parentElement) return NodeFilter.FILTER_REJECT;
            if (skip.includes(node.parentElement.tagName)) return NodeFilter.FILTER_REJECT;
            // Não traduzir o seletor de língua
            if (node.parentElement.closest('#langSelector')) return NodeFilter.FILTER_REJECT;
            if (!node.textContent.trim()) return NodeFilter.FILTER_REJECT;
            return NodeFilter.FILTER_ACCEPT;
        }
    });
    const nodes = [];
    while (walker.nextNode()) nodes.push(walker.currentNode);
    return nodes;
}

// Traduzir array de strings via MyMemory (em lotes de 10)
async function translateBatch(texts, toLang) {
    const results = [];
    for (let i = 0; i < texts.length; i += 10) {
        const batch = texts.slice(i, i + 10);
        const promises = batch.map(text => {
            const url = 'https://api.mymemory.translated.net/get?q=' +
                encodeURIComponent(text.trim()) + '&langpair=pt|' + toLang;
            return fetch(url)
                .then(r => r.json())
                .then(d => d.responseData?.translatedText || text)
                .catch(() => text);
        });
        const batchResults = await Promise.all(promises);
        results.push(...batchResults);
    }
    return results;
}

async function changeLang(lang, flag, code) {
    if (lang === currentLang) return;

    document.getElementById('langCurrent').textContent = flag + ' ' + code;
    document.getElementById('langDropdown').classList.remove('open');

    // Mostrar loading no botão
    const btn = document.getElementById('langBtn');
    btn.style.opacity = '0.6';
    btn.style.pointerEvents = 'none';

    if (lang === 'pt') {
        // Reverter para originais
        const nodes = getTextNodes();
        nodes.forEach((node, i) => {
            if (originalTexts[i] !== undefined) node.textContent = originalTexts[i];
        });
        originalTexts = [];
        currentLang = 'pt';
        document.getElementById('langCurrent').textContent = flag + ' ' + code;
    } else {
        // Guardar originais se ainda não guardados
        const nodes = getTextNodes();
        if (originalTexts.length === 0) {
            originalTexts = nodes.map(n => n.textContent);
        } else {
            // Reverter para PT antes de traduzir para outra língua
            nodes.forEach((node, i) => {
                if (originalTexts[i] !== undefined) node.textContent = originalTexts[i];
            });
        }
        const fresh = getTextNodes();
        const texts = fresh.map(n => n.textContent);
        const translated = await translateBatch(texts, lang);
        fresh.forEach((node, i) => { node.textContent = translated[i]; });
        currentLang = lang;
    }

    btn.style.opacity = '1';
    btn.style.pointerEvents = 'auto';
}

// Toggle dropdown
document.getElementById('langBtn').addEventListener('click', function(e) {
    e.stopPropagation();
    document.getElementById('langDropdown').classList.toggle('open');
});
document.addEventListener('click', function() {
    document.getElementById('langDropdown').classList.remove('open');
});
</script>