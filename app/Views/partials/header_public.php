<!DOCTYPE html>
<html lang="it" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <?php if (!empty($metaDescription)): ?>
        <meta name="description" content="<?= htmlspecialchars($metaDescription) ?>">
    <?php endif; ?>
    <?php if (!empty($metaKeywords)): ?>
        <meta name="keywords" content="<?= htmlspecialchars($metaKeywords) ?>">
    <?php endif; ?>
    <?php if (!empty($canonicalUrl)): ?>
        <link rel="canonical" href="<?= htmlspecialchars($canonicalUrl) ?>">
    <?php endif; ?>

    <meta property="og:type" content="<?= htmlspecialchars($ogType ?? 'website') ?>">
    <meta property="og:site_name" content="GiorgioDiFusco.it">
    <meta property="og:locale" content="it_IT">
    <meta property="og:title" content="<?= htmlspecialchars($ogTitle ?? $pageTitle ?? 'GiorgioDiFusco.it') ?>">
    <?php if (!empty($ogDescription ?? $metaDescription ?? null)): ?>
        <meta property="og:description" content="<?= htmlspecialchars($ogDescription ?? $metaDescription) ?>">
    <?php endif; ?>
    <?php if (!empty($canonicalUrl)): ?>
        <meta property="og:url" content="<?= htmlspecialchars($canonicalUrl) ?>">
    <?php endif; ?>
    <?php if (!empty($ogImage)): ?>
        <meta property="og:image" content="<?= htmlspecialchars($ogImage) ?>">
        <meta property="og:image:alt" content="<?= htmlspecialchars($ogTitle ?? '') ?>">
    <?php endif; ?>

    <meta name="twitter:card" content="<?= !empty($ogImage) ? 'summary_large_image' : 'summary' ?>">
    <meta name="twitter:title" content="<?= htmlspecialchars($ogTitle ?? $pageTitle ?? 'GiorgioDiFusco.it') ?>">
    <?php if (!empty($ogDescription ?? $metaDescription ?? null)): ?>
        <meta name="twitter:description" content="<?= htmlspecialchars($ogDescription ?? $metaDescription) ?>">
    <?php endif; ?>
    <?php if (!empty($ogImage)): ?>
        <meta name="twitter:image" content="<?= htmlspecialchars($ogImage) ?>">
    <?php endif; ?>

    <title><?= htmlspecialchars($pageTitle ?? 'GiorgioDiFusco.it') ?></title>
    <link rel="icon" type="image/png" href="/public/assets/img/favicon.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif']
                    }
                }
            }
        };
        const BASE_URL = <?= json_encode(BASE_URL) ?>;
        const CSRF_TOKEN = <?= json_encode($_SESSION['csrf_token'] ?? '') ?>;
    </script>
    <?php if (ADSENSE_CLIENT !== ''): ?>
        <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=<?= htmlspecialchars(ADSENSE_CLIENT) ?>"
            crossorigin="anonymous"></script>
    <?php endif; ?>
    <style>
        body {
            font-family: 'Inter', system-ui, sans-serif;
        }
    </style>
</head>

<body class="bg-white text-gray-900 antialiased min-h-screen flex flex-col">

    <header class="fixed inset-x-0 top-0 z-50 border-b border-gray-200 bg-white/95 backdrop-blur">
        <nav class="max-w-6xl mx-auto flex items-center justify-between gap-2 px-4 py-3">
            <a href="<?= BASE_URL ?>/" class="flex min-w-0 items-center gap-2.5">
                <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-600 text-white shadow-sm">
                    <span class="text-base font-bold">G</span>
                </span>
                <div class="flex min-w-0 flex-col leading-tight">
                    <span class="truncate font-semibold tracking-tight text-gray-900">Giorgio Di Fusco</span>
                    <span class="hidden text-[11px] text-gray-500 sm:block">Lezioni private · Area riservata · Blog</span>
                </div>
            </a>

            <div class="flex shrink-0 items-center gap-2">
                <a href="<?= BASE_URL ?>/blog"
                    class="hidden sm:inline-flex items-center px-3 py-1.5 text-sm font-medium text-gray-600 hover:text-gray-900 transition rounded-md hover:bg-gray-100">
                    Blog
                </a>
                <a href="<?= BASE_URL ?>/#services"
                    class="hidden sm:inline-flex items-center px-3 py-1.5 text-sm font-medium text-gray-600 hover:text-gray-900 transition rounded-md hover:bg-gray-100">
                    Percorsi
                </a>
                <a href="<?= BASE_URL ?>/#about"
                    class="hidden sm:inline-flex items-center px-3 py-1.5 text-sm font-medium text-gray-600 hover:text-gray-900 transition rounded-md hover:bg-gray-100">
                    Chi sono
                </a>
                <a href="<?= BASE_URL ?>/login"
                    class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-1.5 text-sm font-semibold text-white hover:bg-blue-700 transition shadow-sm">
                    Accedi
                </a>

                <button type="button"
                    id="mobileMenuToggle"
                    aria-controls="mobileMenu"
                    aria-expanded="false"
                    aria-label="Apri il menu"
                    class="inline-flex items-center justify-center rounded-lg border border-gray-300 p-2 text-gray-600 transition hover:bg-gray-100 sm:hidden">
                    <svg id="mobileMenuIconOpen" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg id="mobileMenuIconClose" class="hidden h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </nav>

        <!-- Mobile menu -->
        <div id="mobileMenu" class="hidden border-t border-gray-100 bg-white sm:hidden">
            <div class="max-w-6xl mx-auto space-y-1 px-4 py-3">
                <a href="<?= BASE_URL ?>/blog"
                    class="block rounded-lg px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100">
                    Blog
                </a>
                <a href="<?= BASE_URL ?>/#services"
                    class="block rounded-lg px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100">
                    Percorsi
                </a>
                <a href="<?= BASE_URL ?>/#about"
                    class="block rounded-lg px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100">
                    Chi sono
                </a>
            </div>
        </div>
    </header>

    <main class="flex-1">