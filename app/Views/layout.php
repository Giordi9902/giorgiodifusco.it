<?php
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$role = $_SESSION['user_role'] ?? null;

$adminLinks = [
    ['path' => '/admin',           'label' => 'Panoramica', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>'],
    ['path' => '/admin/students',  'label' => 'Studenti',   'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>'],
    ['path' => '/admin/lessons',   'label' => 'Lezioni',    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>'],
    ['path' => '/admin/materials', 'label' => 'Materiali',  'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"></path>'],
    ['path' => '/admin/payments',  'label' => 'Pagamenti',  'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>'],
    ['path' => '/admin/report',    'label' => 'Report',     'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a2 2 0 012-2h2a2 2 0 012 2v2m-6 0h6m-6 0a2 2 0 01-2-2v-6a2 2 0 012-2h6a2 2 0 012 2v6a2 2 0 01-2 2m-6 0h6"></path>'],
];

$cmsLinks = [
    ['path' => '/cms',         'label' => 'Dashboard',        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>'],
    ['path' => '/cms/blog',    'label' => 'Articoli',         'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>'],
    ['path' => '/cms/blog/new', 'label' => 'Nuovo articolo',   'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>'],
    ['path' => '/cms/aree',    'label' => 'Aree & Argomenti', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>'],
];

$studentLinks = [
    ['path' => '/dashboard',           'label' => 'Panoramica', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>'],
    ['path' => '/dashboard/lessons',   'label' => 'Lezioni',    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>'],
    ['path' => '/dashboard/packages',  'label' => 'Pacchetti',  'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>'],
    ['path' => '/dashboard/materials', 'label' => 'Materiali',  'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"></path>'],
    ['path' => '/dashboard/payments',  'label' => 'Pagamenti',  'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>'],
    ['path' => '/dashboard/report',    'label' => 'Report',     'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a2 2 0 012-2h2a2 2 0 012 2v2m-6 0h6m-6 0a2 2 0 01-2-2v-6a2 2 0 012-2h6a2 2 0 012 2v6a2 2 0 01-2 2m-6 0h6"></path>'],
];

$area = $layoutArea ?? ($role === 'ADMIN' ? 'admin' : 'student');
$linksToShow = match ($area) {
    'cms'   => $cmsLinks,
    'admin' => $adminLinks,
    default => $studentLinks,
};

$isCmsArea   = $area === 'cms';
$isAdminArea = $area === 'admin';

$areaLabel    = $isCmsArea ? 'CMS Blog' : ($isAdminArea ? 'Area Riservata' : 'Area Studente');
$pageDocTitle = $isCmsArea ? 'CMS Blog' : 'LMS Dashboard';
?>
<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageDocTitle) ?></title>
    <link rel="icon" type="image/svg+xml" href="/public/assets/img/favicon.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <?php if (ADSENSE_CLIENT !== ''): ?>
        <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=<?= htmlspecialchars(ADSENSE_CLIENT) ?>"
            crossorigin="anonymous"></script>
    <?php endif; ?>
    <script>
        const BASE_URL = <?= json_encode(BASE_URL) ?>;
        const CSRF_TOKEN = <?= json_encode($_SESSION['csrf_token'] ?? '') ?>;
    </script>
</head>

<body class="bg-zinc-950 text-zinc-50 flex h-screen overflow-hidden antialiased">

    <!-- Sidebar -->
    <aside id="app-sidebar"
        class="w-64 bg-zinc-950 border-r border-zinc-800 flex flex-col transition-all duration-300 z-40 fixed md:relative h-screen transform -translate-x-full md:translate-x-0">

        <!-- Brand header -->
        <div class="h-[73px] flex items-center px-4 md:px-5 border-b border-zinc-800">
            <a href="<?= BASE_URL ?>/" class="flex items-center gap-3 w-full overflow-hidden">
                <?php if ($isCmsArea): ?>
                    <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-violet-500/10 text-violet-400 ring-1 ring-violet-500/40">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </span>
                <?php else: ?>
                    <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-600 text-white shadow-sm">
                        <span class="text-base font-bold">G</span>
                    </span>
                <?php endif; ?>
                <div class="sidebar-text flex flex-col leading-tight whitespace-nowrap transition-opacity duration-300">
                    <span class="text-zinc-100 font-semibold tracking-tight text-sm">Giorgio Di Fusco</span>
                    <span class="text-[10px] text-zinc-500"><?= $areaLabel ?></span>
                </div>
            </a>
        </div>

        <!-- Nav links -->
        <nav class="flex-1 px-3 py-5 space-y-0.5 text-sm overflow-y-auto overflow-x-hidden w-full">
            <?php foreach ($linksToShow as $link):
                $isActive = $currentPath === $link['path']
                    || ($link['path'] !== '/cms' && $link['path'] !== '/admin' && $link['path'] !== '/dashboard'
                        && str_starts_with($currentPath, $link['path']));
                $activeClass = $isActive
                    ? ($isCmsArea
                        ? 'bg-violet-500/10 text-violet-300 ring-1 ring-violet-500/30 font-medium'
                        : 'bg-blue-500/10 text-blue-400 ring-1 ring-blue-500/30 font-medium')
                    : 'text-zinc-400 hover:bg-zinc-900 hover:text-zinc-100';
            ?>
                <a href="<?= BASE_URL . $link['path'] ?>" title="<?= htmlspecialchars($link['label']) ?>"
                    class="flex items-center gap-3 rounded-lg px-3 py-2.5 transition <?= $activeClass ?>">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <?= $link['icon'] ?>
                    </svg>
                    <span class="sidebar-text whitespace-nowrap transition-opacity duration-300"><?= htmlspecialchars($link['label']) ?></span>
                </a>
            <?php endforeach; ?>
        </nav>

        <!-- Cross-area link -->
        <?php if ($isAdminArea): ?>
            <div class="px-3 pb-3">
                <a href="<?= BASE_URL ?>/cms"
                    class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-zinc-500 hover:text-violet-300 hover:bg-violet-500/5 border border-transparent hover:border-violet-500/20 transition">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    <span class="sidebar-text whitespace-nowrap">CMS Blog</span>
                    <svg class="w-3 h-3 ml-auto shrink-0 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                </a>
            </div>
        <?php elseif ($isCmsArea): ?>
            <div class="px-3 pb-3">
                <a href="<?= BASE_URL ?>/admin"
                    class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-zinc-500 hover:text-zinc-200 hover:bg-zinc-800/60 border border-transparent hover:border-zinc-700 transition">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span class="sidebar-text whitespace-nowrap">← LMS Admin</span>
                </a>
            </div>
        <?php endif; ?>

        <!-- Logout -->
        <div class="p-3 border-t border-zinc-800 flex justify-center w-full">
            <form action="<?= BASE_URL ?>/logout" method="POST" class="w-full">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                <button type="submit" title="Disconnetti"
                    class="flex items-center justify-center w-full min-w-[40px] h-9 gap-2 text-sm font-medium text-zinc-400 rounded-lg border border-zinc-800 hover:bg-zinc-900 hover:text-zinc-200 transition overflow-hidden">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                        </path>
                    </svg>
                    <span class="sidebar-text whitespace-nowrap transition-opacity duration-300">Disconnetti</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Mobile overlay -->
    <div id="sidebarOverlay"
        class="fixed inset-0 bg-zinc-950/80 backdrop-blur-sm z-30 hidden transition-opacity duration-300 opacity-0 md:hidden">
    </div>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col h-screen overflow-y-auto bg-zinc-950 min-w-0">
        <header class="bg-zinc-950/80 border-b border-zinc-800 backdrop-blur px-4 sm:px-8 py-4 flex justify-between items-center sticky top-0 z-20">
            <div class="flex items-center gap-4">
                <button id="sidebarToggle"
                    class="text-zinc-400 hover:text-zinc-100 focus:outline-none transition bg-zinc-900 hover:bg-zinc-800 p-2 rounded-lg border border-zinc-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                <h2 class="text-sm font-medium text-zinc-400 hidden sm:block">
                    Ciao, <span class="text-zinc-50 font-semibold"><?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?></span>
                    <?php if ($isCmsArea): ?>
                        <span class="ml-2 inline-flex items-center rounded-full bg-violet-500/10 px-2 py-0.5 text-[10px] font-medium text-violet-400 ring-1 ring-violet-500/30">CMS</span>
                    <?php endif; ?>
                </h2>
            </div>
            <div class="h-8 w-8 rounded-full bg-blue-500/15 text-blue-400 flex items-center justify-center text-sm font-semibold border border-blue-500/25 shadow-sm shrink-0">
                <?= strtoupper(substr($_SESSION['user_name'] ?? 'U', 0, 1)) ?>
            </div>
        </header>

        <div class="p-4 sm:p-8 max-w-7xl mx-auto w-full">
            <?= $content ?? '' ?>
        </div>
    </main>

    <script src="<?= asset('assets/js/app_modals.js') ?>"></script>
    <script src="<?= asset('assets/js/layout.js') ?>"></script>
</body>

</html>