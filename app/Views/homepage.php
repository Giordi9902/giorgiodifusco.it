<?php
$pageTitle = 'Giorgio Di Fusco | Lezioni private di Matematica, Fisica e Informatica';
$metaDescription = 'Ripetizioni private di Matematica, Fisica, Geometria e Informatica. Percorsi su misura per studenti delle superiori e universitari.';
require __DIR__ . '/partials/header_public.php';
?>

<!-- Hero -->
<section class="relative pt-24 pb-20 overflow-hidden bg-white">
    <div class="absolute inset-0 bg-[radial-gradient(ellipse_80%_50%_at_50%_-10%,rgba(37,99,235,0.06),transparent)]"></div>
    <div class="relative max-w-6xl mx-auto px-4 grid gap-16 lg:grid-cols-2 items-center">

        <div class="space-y-7">
            <div class="inline-flex items-center gap-2 rounded-full border border-blue-100 bg-blue-50 px-3 py-1 text-xs font-medium text-blue-700">
                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                Ripetizioni · Area riservata · Blog tecnico
            </div>

            <h1 class="text-4xl sm:text-5xl lg:text-[3.25rem] font-bold tracking-tight text-gray-900 leading-[1.1]">
                Lezioni private<br>
                <span class="text-blue-600">chiare e concrete.</span>
            </h1>

            <p class="text-lg text-gray-600 leading-relaxed max-w-lg">
                Offro ripetizioni individuali di Matematica, Fisica, Geometria e Informatica, gestite su una
                piattaforma dedicata con calendario, materiali e pagamenti sempre a portata di mano — e un
                blog tecnico gratuito per chi vuole approfondire da solo.
            </p>

            <div class="flex flex-col sm:flex-row gap-3">
                <a href="<?= BASE_URL ?>/login"
                   class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 transition">
                    Accedi all'area riservata
                </a>
                <a href="#about"
                   class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-6 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                    Scopri di più
                </a>
            </div>

            <dl class="grid grid-cols-3 gap-6 pt-2 border-t border-gray-100 max-w-md">
                <div>
                    <dt class="text-sm font-semibold text-gray-900">Lezioni tracciate</dt>
                    <dd class="mt-1 text-xs text-gray-500">Storico temi e materiali sempre disponibili.</dd>
                </div>
                <div>
                    <dt class="text-sm font-semibold text-gray-900">Obiettivi chiari</dt>
                    <dd class="mt-1 text-xs text-gray-500">Progressi monitorati lezione per lezione.</dd>
                </div>
                <div>
                    <dt class="text-sm font-semibold text-gray-900">Gestione semplice</dt>
                    <dd class="mt-1 text-xs text-gray-500">Pagamenti e calendario sempre aggiornati.</dd>
                </div>
            </dl>
        </div>

        <!-- Dashboard preview card -->
        <div class="fade-in-up lg:flex lg:justify-end">
            <div class="w-full max-w-sm rounded-2xl border border-gray-200 bg-white shadow-xl shadow-gray-100 p-5">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-4">
                    <div class="flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                        <span class="text-xs font-medium text-gray-700">Oggi</span>
                    </div>
                    <span class="text-xs text-gray-400">Le tue prossime lezioni</span>
                </div>

                <div class="space-y-2.5 text-sm">
                    <div class="flex items-center justify-between rounded-xl bg-blue-50 border border-blue-100 px-3 py-2.5">
                        <div>
                            <p class="font-medium text-gray-800">Informatica – Basi di C++</p>
                            <p class="text-[11px] text-gray-500 mt-0.5">16:00–17:00 · Link Meet disponibile</p>
                        </div>
                        <span class="rounded-md bg-blue-600 px-2.5 py-1 text-[11px] font-semibold text-white">Partecipa</span>
                    </div>
                    <div class="flex items-center justify-between rounded-xl bg-gray-50 border border-gray-100 px-3 py-2.5">
                        <div>
                            <p class="font-medium text-gray-700">Matematica – Derivate</p>
                            <p class="text-[11px] text-gray-400 mt-0.5">Domani, 18:30–19:30</p>
                        </div>
                        <span class="rounded-md border border-gray-200 bg-white px-2.5 py-1 text-[11px] text-gray-600">Programmata</span>
                    </div>
                    <div class="flex items-center justify-between rounded-xl border border-dashed border-amber-200 bg-amber-50 px-3 py-2.5">
                        <div>
                            <p class="font-medium text-amber-800">Esercizi caricati</p>
                            <p class="text-[11px] text-amber-600 mt-0.5">Nuovi PDF nella sezione Materiali</p>
                        </div>
                        <span class="text-[11px] font-medium text-amber-700">Vedi →</span>
                    </div>
                </div>

                <div class="mt-4 flex items-center justify-between text-[11px] text-gray-400 pt-3 border-t border-gray-100">
                    <span>Pacchetto: 2 lezioni rimanenti</span>
                    <span class="text-blue-600 font-medium">Rinnova →</span>
                </div>
            </div>
        </div>

        <!-- Cosa trovi qui -->
        <div class="lg:col-span-2 grid gap-5 pt-10 border-t border-gray-100 sm:grid-cols-3">
            <a href="#services"
               class="group rounded-xl border border-gray-200 bg-white p-5 transition hover:border-blue-200 hover:shadow-md hover:shadow-blue-50">
                <div class="mb-3 inline-flex h-9 w-9 items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.42A9 9 0 0112 21a9 9 0 01-6.16-10.42L12 14z"/>
                    </svg>
                </div>
                <h3 class="text-sm font-semibold text-gray-900 mb-1">Ripetizioni</h3>
                <p class="text-xs text-gray-500 leading-relaxed">
                    Lezioni individuali di Matematica, Fisica, Geometria e Informatica, online o in presenza.
                </p>
            </a>

            <a href="<?= BASE_URL ?>/login"
               class="group rounded-xl border border-gray-200 bg-white p-5 transition hover:border-emerald-200 hover:shadow-md hover:shadow-emerald-50">
                <div class="mb-3 inline-flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17V9m6 8V5M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="text-sm font-semibold text-gray-900 mb-1">Area riservata</h3>
                <p class="text-xs text-gray-500 leading-relaxed">
                    La piattaforma LMS per gestire calendario, pacchetti, materiali e pagamenti in un unico posto.
                </p>
            </a>

            <a href="<?= BASE_URL ?>/blog"
               class="group rounded-xl border border-gray-200 bg-white p-5 transition hover:border-amber-200 hover:shadow-md hover:shadow-amber-50">
                <div class="mb-3 inline-flex h-9 w-9 items-center justify-center rounded-lg bg-amber-50 text-amber-600">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 20H7a2 2 0 01-2-2V6a2 2 0 012-2h7l5 5v9a2 2 0 01-2 2z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 13h6M9 17h6M9 9h1"/>
                    </svg>
                </div>
                <h3 class="text-sm font-semibold text-gray-900 mb-1">Blog</h3>
                <p class="text-xs text-gray-500 leading-relaxed">
                    Articoli tecnici ed esempi pratici su matematica, fisica e informatica, aperti a tutti.
                </p>
            </a>
        </div>
    </div>
</section>

<!-- Chi sono -->
<section id="about" class="bg-gray-50 py-20 border-t border-gray-100">
    <div class="max-w-5xl mx-auto px-4 grid gap-12 md:grid-cols-2 items-start">
        <div>
            <p class="text-xs font-semibold uppercase tracking-widest text-blue-600 mb-3">Chi sono</p>
            <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-gray-900 mb-4">Giorgio Di Fusco</h2>
            <p class="text-gray-600 text-sm sm:text-base leading-relaxed">
                Sono uno studente universitario di Informatica e programmatore con diversi anni di esperienza.
                Il mio percorso accademico mi ha portato a sviluppare un forte interesse per le scienze esatte:
                Matematica, Fisica, Geometria e Informatica.
            </p>
            <p class="mt-4 text-gray-600 text-sm sm:text-base leading-relaxed">
                So quanto possano sembrare ostili certe materie se spiegate in modo astratto.
                Per questo cerco di rendere logico, pratico e intuitivo ogni concetto difficile,
                adattando sempre il ritmo e il linguaggio a chi ho davanti.
            </p>
        </div>
        <div class="space-y-4">
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-semibold text-gray-900 mb-2">Un approccio centrato sullo studente</p>
                <p class="text-sm text-gray-600 leading-relaxed">
                    Che si tratti di lezioni singole o pacchetti, ogni incontro ha un contesto:
                    obiettivi precedenti, progressi attuali e materiali di approfondimento sempre a portata di clic.
                </p>
            </div>
            <div class="grid grid-cols-3 gap-3 text-sm">
                <div class="rounded-xl border border-gray-100 bg-white p-3 shadow-sm">
                    <p class="font-semibold text-gray-800">+ Struttura</p>
                    <p class="mt-1 text-xs text-gray-500">Visione chiara di cosa abbiamo fatto e faremo.</p>
                </div>
                <div class="rounded-xl border border-gray-100 bg-white p-3 shadow-sm">
                    <p class="font-semibold text-gray-800">+ Relazione</p>
                    <p class="mt-1 text-xs text-gray-500">Un rapporto diretto basato sul confronto continuo.</p>
                </div>
                <div class="rounded-xl border border-gray-100 bg-white p-3 shadow-sm">
                    <p class="font-semibold text-gray-800">+ Focus</p>
                    <p class="mt-1 text-xs text-gray-500">Uno spazio calmo, senza distrazioni esterne.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Cosa insegno -->
<section id="what-i-do" class="bg-white py-20 border-t border-gray-100">
    <div class="max-w-6xl mx-auto px-4">
        <div class="max-w-2xl mb-10">
            <p class="text-xs font-semibold uppercase tracking-widest text-blue-600 mb-3">Materie</p>
            <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-gray-900 mb-3">Cosa insegno</h2>
            <p class="text-gray-600 text-sm sm:text-base leading-relaxed">
                Le mie lezioni non si limitano a far memorizzare. Punto a farti sviluppare un ragionamento logico
                rigoroso per affrontare interrogazioni, prove di maturità e test universitari con sicurezza.
            </p>
        </div>

        <div class="grid gap-5 md:grid-cols-3">
            <article class="group rounded-xl border border-gray-200 bg-white p-6 transition hover:border-blue-200 hover:shadow-md hover:shadow-blue-50">
                <div class="mb-4 inline-flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01"/>
                    </svg>
                </div>
                <h3 class="text-base font-semibold text-gray-900 mb-2">Matematica e Geometria</h3>
                <p class="text-sm text-gray-600 leading-relaxed">
                    Dall'algebra all'analisi matematica, dalla trigonometria alla geometria analitica.
                    Regole e teoremi diventano calcoli visuali e logici, chiari e intuitivi.
                </p>
            </article>

            <article class="group rounded-xl border border-gray-200 bg-white p-6 transition hover:border-emerald-200 hover:shadow-md hover:shadow-emerald-50">
                <div class="mb-4 inline-flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                    </svg>
                </div>
                <h3 class="text-base font-semibold text-gray-900 mb-2">Fisica</h3>
                <p class="text-sm text-gray-600 leading-relaxed">
                    Meccanica, termodinamica, elettromagnetismo. Fenomeni fisici tradotti in formule comprensibili
                    e strategie per risolvere anche i problemi più intricati.
                </p>
            </article>

            <article class="group rounded-xl border border-gray-200 bg-white p-6 transition hover:border-amber-200 hover:shadow-md hover:shadow-amber-50">
                <div class="mb-4 inline-flex h-10 w-10 items-center justify-center rounded-lg bg-amber-50 text-amber-600">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                    </svg>
                </div>
                <h3 class="text-base font-semibold text-gray-900 mb-2">Informatica e Coding</h3>
                <p class="text-sm text-gray-600 leading-relaxed">
                    Pensiero computazionale e fondamentali della programmazione (C, C++, Java, Python).
                    Dalle basi alle logiche del software moderno, comprese le basi web.
                </p>
            </article>
        </div>
    </div>
</section>

<!-- Percorsi -->
<section id="services" class="bg-gray-50 py-20 border-t border-gray-100">
    <div class="max-w-5xl mx-auto px-4">
        <p class="text-xs font-semibold uppercase tracking-widest text-blue-600 mb-3">Percorsi</p>
        <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-gray-900 mb-3">I miei percorsi</h2>
        <p class="text-gray-600 text-sm sm:text-base leading-relaxed max-w-3xl mb-8">
            Che si tratti di preparazione agli esami, recupero di lacune o potenziamento su specifici argomenti,
            ogni percorso è personalizzato sui tuoi tempi e obiettivi.
        </p>

        <ul class="space-y-3 mb-10 max-w-2xl">
            <?php
            $services = [
                'Lezioni individuali con monitoraggio dei progressi e compiti mirati.',
                'Calendario flessibile: visualizza le disponibilità e gestisci i tuoi appuntamenti.',
                'Controllo dei pacchetti: sai sempre quante lezioni hai frequentato e quante ne restano.',
                'Area Studente: materiali, note e storico lezioni sempre accessibili.',
            ];
            foreach ($services as $s): ?>
                <li class="flex items-start gap-3">
                    <span class="mt-0.5 inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-blue-600">
                        <svg viewBox="0 0 24 24" fill="none" stroke="white" class="h-3 w-3" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                    </span>
                    <span class="text-sm text-gray-700"><?= $s ?></span>
                </li>
            <?php endforeach; ?>
        </ul>

        <a href="<?= BASE_URL ?>/login"
           class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 transition">
            Accedi al portale studenti
        </a>
    </div>
</section>

<?php if (!empty($recentPosts)): ?>
<!-- Blog -->
<section id="blog" class="bg-white py-20 border-t border-gray-100">
    <div class="max-w-6xl mx-auto px-4">
        <div class="flex flex-wrap items-end justify-between gap-4 mb-8">
            <div class="max-w-2xl">
                <p class="text-xs font-semibold uppercase tracking-widest text-blue-600 mb-3">Blog</p>
                <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-gray-900 mb-3">Ultimi articoli</h2>
                <p class="text-gray-600 text-sm sm:text-base leading-relaxed">
                    Approfondimenti tecnici, esempi ed esercizi su matematica, fisica e informatica,
                    gratuiti per chiunque voglia studiare in autonomia.
                </p>
            </div>
            <a href="<?= BASE_URL ?>/blog"
               class="inline-flex shrink-0 items-center text-sm font-medium text-blue-600 hover:text-blue-700">
                Tutti gli articoli <span class="ml-1">→</span>
            </a>
        </div>

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
            <?php foreach ($recentPosts as $post): ?>
                <?php $date = $post['published_at'] ?? $post['created_at']; ?>
                <article class="group flex flex-col rounded-2xl border border-gray-200 bg-white overflow-hidden shadow-sm transition hover:-translate-y-px hover:border-blue-200 hover:shadow-md hover:shadow-blue-50">
                    <?php if (!empty($post['featured_image_path'])): ?>
                        <a href="<?= BASE_URL ?>/blog/<?= htmlspecialchars($post['slug']) ?>" tabindex="-1" aria-hidden="true">
                            <img src="<?= BASE_URL . '/' . htmlspecialchars($post['featured_image_path']) ?>"
                                 alt="<?= htmlspecialchars($post['featured_image_alt'] ?? $post['title']) ?>"
                                 class="w-full h-36 object-cover"
                                 loading="lazy" decoding="async">
                        </a>
                    <?php else: ?>
                        <div class="w-full h-2 bg-gradient-to-r from-blue-600 to-blue-400"></div>
                    <?php endif; ?>

                    <a href="<?= BASE_URL ?>/blog/<?= htmlspecialchars($post['slug']) ?>" class="flex flex-1 flex-col p-5">
                        <time datetime="<?= date('Y-m-d', strtotime($date)) ?>"
                              class="mb-2 text-[10px] uppercase tracking-[0.12em] text-gray-400">
                            <?= date('d M Y', strtotime($date)) ?>
                        </time>
                        <h3 class="mb-2 line-clamp-2 text-base font-semibold text-gray-900 group-hover:text-blue-700 leading-snug transition-colors">
                            <?= htmlspecialchars($post['title']) ?>
                        </h3>
                        <?php if (!empty($post['excerpt'])): ?>
                            <p class="mb-4 line-clamp-2 text-sm text-gray-600 leading-relaxed">
                                <?= htmlspecialchars($post['excerpt']) ?>
                            </p>
                        <?php endif; ?>
                        <span class="mt-auto inline-flex items-center text-xs font-medium text-blue-600 group-hover:text-blue-700">
                            Leggi l'articolo <span class="ml-1">→</span>
                        </span>
                    </a>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Contatto / CTA -->
<section id="contact" class="bg-gray-50 py-20 border-t border-gray-100">
    <div class="max-w-4xl mx-auto px-4">
        <div class="rounded-2xl bg-blue-600 px-8 py-12 sm:px-12">
            <div class="max-w-xl">
                <h2 class="text-2xl sm:text-3xl font-bold text-white mb-3">
                    Iniziamo il tuo percorso?
                </h2>
                <p class="text-blue-100 text-sm sm:text-base leading-relaxed mb-8">
                    Se sei già uno studente, effettua il login per gestire lezioni, materiali e pagamenti.
                    Se vuoi iniziare, scrivimi un'email, oppure dai un'occhiata al blog per farti un'idea
                    di come lavoro prima ancora di prenotare la prima lezione.
                </p>
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="<?= BASE_URL ?>/login"
                       class="inline-flex items-center justify-center rounded-lg bg-white px-6 py-2.5 text-sm font-semibold text-blue-700 hover:bg-blue-50 transition shadow-sm">
                        Accedi alla tua area
                    </a>
                    <a href="mailto:<?= htmlspecialchars($contactEmail ?? 'giorgio99difusco@gmail.com') ?>"
                       class="inline-flex items-center justify-center rounded-lg border border-blue-400 bg-transparent px-6 py-2.5 text-sm font-medium text-white hover:bg-blue-700 transition">
                        Scrivimi una email
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="<?= asset('assets/js/homepage.js') ?>"></script>
<?php require __DIR__ . '/partials/footer_public.php'; ?>
