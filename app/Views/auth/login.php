<?php
$pageTitle = 'Login | Area Studenti';
require __DIR__ . '/../partials/header_public.php';
?>

<main class="min-h-[calc(100vh-4rem)] flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-sm">
        <div class="mb-7 text-center">
            <span class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-blue-600 text-white shadow-sm mb-4">
                <span class="text-xl font-bold">G</span>
            </span>
            <h1 class="text-2xl font-bold text-gray-900">Accedi alle tue lezioni</h1>
            <p class="mt-2 text-sm text-gray-500">
                Gestisci appuntamenti, materiali e pagamenti in uno spazio dedicato.
            </p>
        </div>

        <?php if (!empty($_SESSION['flash_error'])): ?>
            <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-xs text-red-800 shadow-sm">
                <?= htmlspecialchars($_SESSION['flash_error']) ?>
            </div>
            <?php unset($_SESSION['flash_error']); ?>
        <?php endif; ?>

        <div class="rounded-2xl border border-gray-200 bg-white px-6 py-7 shadow-md">
            <form action="<?= BASE_URL ?>/login" method="POST" class="space-y-4">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Email</label>
                    <input type="email" name="email" required autocomplete="email"
                        class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 shadow-sm
                               focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Password</label>
                    <input type="password" name="password" required autocomplete="current-password"
                        class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 shadow-sm
                               focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition">
                </div>

                <button type="submit"
                    class="mt-1 inline-flex w-full items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 transition">
                    Entra nel portale
                </button>
            </form>
        </div>

        <a href="<?= BASE_URL ?>/"
           class="mt-4 inline-flex w-full items-center justify-center rounded-lg border border-gray-200 bg-white px-4 py-2 text-xs font-medium text-gray-600 hover:bg-gray-50 transition">
            ← Torna alla homepage
        </a>
    </div>
</main>

<?php require __DIR__ . '/../partials/footer_public.php'; ?>
