    </main>

    <footer class="border-t border-gray-200 bg-gray-50">
        <div class="max-w-6xl mx-auto px-4 py-5 flex flex-col gap-4">
            <nav class="flex flex-wrap items-center justify-center gap-x-5 gap-y-2 text-sm text-gray-600 sm:justify-start">
                <a href="<?= BASE_URL ?>/blog" class="hover:text-gray-900 transition">Blog</a>
                <a href="<?= BASE_URL ?>/#services" class="hover:text-gray-900 transition">Percorsi</a>
                <a href="<?= BASE_URL ?>/#about" class="hover:text-gray-900 transition">Chi sono</a>
                <a href="<?= BASE_URL ?>/login" class="hover:text-gray-900 transition">Accedi</a>
            </nav>
            <div class="flex flex-col items-center justify-between gap-2 text-sm text-gray-500 sm:flex-row">
                <p>© <?= date('Y') ?> <span class="font-medium text-gray-700">Giorgio Di Fusco</span>. Tutti i diritti riservati.</p>
                <div class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                    <a href="<?= BASE_URL ?>/" class="hover:text-gray-900 transition">giorgiodifusco.it</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="<?= asset('assets/js/public_nav.js') ?>"></script>
</body>
</html>
